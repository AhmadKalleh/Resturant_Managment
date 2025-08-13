<?php

namespace App\Http\Controllers\Order;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderService
{

    private function formatCartItem($item, $lang): ?array
    {
        $itemTotal = $item->price_at_order * $item->quantity;
        $extrasTotal = 0;

        foreach ($item->extra_products as $extraProduct) {
            if ($extraProduct->extra) {
                $extrasTotal += $extraProduct->extra->price;
            }
        }

        $total_price = $itemTotal + $extrasTotal;

        // منتج عادي
        if (is_null($item->offer_id) && $item->product) {
            return [
                'cart_item_id'   => $item->id,
                'type'           => 'product',
                'quantity'       => $item->quantity,
                'total_price'    => number_format(ceil($total_price), 0, ',', ',') . ' $',
                'product_id'     => $item->product->id,
                'product_name'   => $item->product->getTranslation('name', $lang),
                'calories'       => $item->product->calories_text,
                'product_image'  => $item->product->image ? url(Storage::url($item->product->image->path)) : null,
                'product_price'  => $item->product->price_text,
            ];
        }

        // عرض (Offer)
        if (is_null($item->product_id) && $item->offer) {
            return [
                'cart_item_id'   => $item->id,
                'type'           => 'offer',
                'quantity'       => $item->quantity,
                'total_price'    => number_format(ceil($total_price), 0, ',', ',') . ' $',
                'offer_id'       => $item->offer->id,
                'offer_name'     => $item->offer->getTranslation('title', $lang),
                'offer_image'    => $item->offer->image ? url(Storage::url($item->offer->image->path)) : null,
                'offer_price'    => $item->offer->price_after_discount_text,
                'calories'       => $item->offer->total_calories_text,
            ];
        }

        // غير معروف أو بيانات ناقصة
        return null;
    }


    public function index_pre_orders(): array
    {
        $lang = Auth::user()->preferred_language;

        $pre_orders = Reservation::with(['orders.carts.cart_items.extra_products.extra'])
            ->where('customer_id', Auth::user()->customer->id)
            ->where('is_canceled', false)
            ->where('is_checked_in', false)
            ->where('reservation_end_time', '>=', now()->addHours(3))
            ->get()
            ->map(function ($reservation) {
                $orders = $reservation->orders->map(function ($order) {
                    $carts = $order->carts->where('is_completed', 0)->map(function ($cart) {
                        $pre_order_items = $cart->cart_items
                            ->where('is_pre_order', 1)
                            ->where('is_ready',0)
                            ->where('is_selected_for_checkout',1)
                            ->where('prepare_at', '>', now()->addHours(3));

                        if ($pre_order_items->isEmpty()) {
                            return null;
                        }

                        $new_total_price = 0;
                        foreach ($pre_order_items as $item) {
                            $itemTotal = $item->price_at_order * $item->quantity;
                            $extrasTotal = $item->extra_products->sum(fn($e) => $e->extra->price);
                            $new_total_price += $itemTotal + $extrasTotal;
                        }

                        return [
                            'created_at' => Carbon::parse($cart->created_at)->format('F j, Y'),
                            'cart_id' => $cart->id,
                            'total_pre_order_price' => number_format(ceil($new_total_price), 0, ',', ',') . ' $',
                            'items_count' => $pre_order_items->count(),
                        ];
                    })
                    ->filter()
                    ->values();

                    if ($carts->isEmpty()) {
                        return null;
                    }

                    return [
                        'order_id' => $order->id,
                        'carts' => $carts,
                    ];
                })
                ->filter()
                ->values();

                if ($orders->isEmpty()) {
                    return null;
                }

                return [
                    'reservation_id' => $reservation->id,
                    'reservation_start_time' => Carbon::parse($reservation->reservation_start_time)->format('Y-m-d H:i:s'),
                    'reservation_end_time' => Carbon::parse($reservation->reservation_end_time)->format('Y-m-d H:i:s'),
                    'orders' => $orders,
                ];
            })
            ->filter()
            ->values();

        if ($pre_orders->isNotEmpty()) {
            return [
                'data' => $pre_orders,
                'message' => __('message.Pre_Orders_Retrived', [], $lang),
                'code' => 200,
            ];
        } else {
            return [
                'data' => [],
                'message' => __('message.There_Arenot_Pre_Orders', [], $lang),
                'code' => 200,
            ];
        }
    }

    public function show_pre_order($request): array
    {
        $lang = Auth::user()->preferred_language;

        $cart = Cart::with([
                'cart_items.product.image',
                'cart_items.offer.image',
                'cart_items.extra_products.extra'
            ])
            ->where('id', $request['cart_id'])
            ->where('is_completed', 0)
            ->first();

        if (!$cart) {
            return [
                'data' => [],
                'message' => __('message.Cart_Not_Found', [], $lang),
                'code' => 404
            ];
        }

        $cart_items_for_pre_order = $cart->cart_items
            ->where('is_pre_order', 1)
            ->where('is_ready',0)
            ->where('is_selected_for_checkout',1)
            ->filter(function ($item) {
                return Carbon::parse($item->prepare_at)->gt(now()->addHours(3));
            })
            ->map(function ($item) use ($lang) {
                $prepare_at = Carbon::parse($item->prepare_at)->subMinutes(30);
                $now = now()->addHours(3);
                $is_cancelable = $now <= $prepare_at;

                $formatted = $this->formatCartItem($item, $lang);
                if ($formatted) {
                    $formatted['prepare_at'] = Carbon::parse($item->prepare_at)->format('F j, Y \a\t h:i A');
                    $formatted['is_cancelable'] = $is_cancelable;
                }
                return $formatted;
            })
            ->filter()
            ->values();

        $data = $cart_items_for_pre_order->isNotEmpty() ? $cart_items_for_pre_order : [];
        $message = $cart_items_for_pre_order->isNotEmpty()
            ? __('message.Pre_Order_Retrieved', [], $lang)
            : __('message.There_Isnot_Pre_Order', [], $lang);
        $code = 200;

        return ['data' => $data, 'message' => $message, 'code' => $code];
    }

    public function create_pre_order($request):array
    {
        $lang = Auth::user()->preferred_language;

        $exist_order = Order::query()->where('reservation_id','=',$request['reservation_id'])->first();
        $exist_reservation = Reservation::query()->where('id',$request['reservation_id'])->first();

        if($request['prepare_at'] < $exist_reservation->reservation_start_time)
        {
            $data = [];
            $message = __('message.Prepare_At_Must_After_Reservation_Start_Time', [], $lang);
            $code = 400;
        }
        else if($request['prepare_at'] > $exist_reservation->reservation_end_time)
        {
            $data = [];
            $message = __('message.Prepare_At_Must_Before_Reservation_End_Time', [], $lang);
            $code = 400;
        }
        else
        {

            $cartItems = CartItem::with(['extra_products.extra'])
                ->whereIn('id', $request['cart_item_ids'])
                ->get();


                $new_total_price = 0;

                foreach ($cartItems as $item)
                {

                    $itemTotal = $item->price_at_order * $item->quantity;
                    $extrasTotal = 0;

                    foreach ($item->extra_products as $extraProduct)
                    {
                        $extrasTotal += $extraProduct->extra->price;
                    }

                    $new_total_price += $itemTotal + $extrasTotal;
                }



            $customer = Auth::user()->customer;
            if (!$customer->my_wallet)
            {
                $data = [];
                $message = __('message.Wallet_Not_Found', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                $code = 400;

                return ['data' => $data, 'message' => $message, 'code' => $code];
            }

            if(is_null($exist_order))
            {



                $new_pre_order = Auth::user()->customer->orders()->create([
                    'reservation_id' => $request['reservation_id'],
                    'total_amount' => $new_total_price,
                ]);

                if($customer->my_wallet->amount < $new_pre_order->total_amount)
                {
                    $data = [];
                    $message = __('message.Order_Price_exceeds_Your_Wallet', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                    $code = 400;

                    return ['data' => $data, 'message' => $message, 'code' => $code];
                }

                CartItem::query()
                ->whereIn('id', $request['cart_item_ids'])
                ->update([
                    'is_selected_for_checkout' => true,
                    'is_pre_order' => true,
                    'prepare_at' => $request['prepare_at']
                ]);

                $are_all_cart_items_taked = CartItem::query()
                ->where('cart_id', $request['cart_id'])
                ->where('is_selected_for_checkout', false)
                ->doesntExist();

                $new_payment = Payment::query()->create([
                    'order_id' => $new_pre_order->id,
                    'amount' => $new_pre_order->total_amount + $exist_reservation->table->price
                ]);

                $old_amount = $customer->my_wallet->amount;
                $customer->my_wallet()->update([
                    'amount' => $old_amount - $new_pre_order->total_amount
                ]);

                Cart::query()
                    ->where('id', $request['cart_id'])
                    ->update([
                        'order_id' => $new_pre_order->id,
                        'is_checked_out' => $are_all_cart_items_taked
                    ]);

                $data = [];
                $message = __('message.Pre_Order_Created_And_Cart_Attached', [], $lang);
                $code = 201;

            }
            else
            {
                $exist_total_price = ($exist_order->total_amount) + $new_total_price;

                if($customer->my_wallet->amount < $new_total_price)
                {
                    $data = [];
                    $message = __('message.Order_Price_exceeds_Your_Wallet', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                    $code = 400;

                    return ['data' => $data, 'message' => $message, 'code' => $code];
                }

                $exist_order->update([
                    'total_amount' => $exist_total_price
                ]);

                $exist_order->payment->update([
                    'amount' => $exist_order->total_amount + $exist_reservation->table->price
                ]);

                $old_amount = $customer->my_wallet->amount;
                $customer->my_wallet()->update([
                    'amount' => $old_amount - $new_total_price
                ]);

                CartItem::query()
                ->whereIn('id', $request['cart_item_ids'])
                ->update([
                    'is_selected_for_checkout' => true,
                    'is_pre_order' => true,
                    'prepare_at' => $request['prepare_at']
                ]);

                $are_all_cart_items_taked = CartItem::query()
                ->where('cart_id', $request['cart_id'])
                ->where('is_selected_for_checkout', false)
                ->doesntExist();

                Cart::query()
                    ->where('id', $request['cart_id'])
                    ->update([
                        'order_id' => $exist_order->id,
                        'is_checked_out' => $are_all_cart_items_taked
                    ]);

                $data = [];
                $message = __('message.Pre_Order_Items_Attached', [], $lang);
                $code = 200;

            }
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }


    public function index_now_orders(): array
    {
            $lang = Auth::user()->preferred_language;

            $now_orders = Reservation::with(['orders.carts.cart_items.extra_products.extra'])
                ->where('customer_id', Auth::user()->customer->id)
                ->where('reservation_end_time', '>=', now()->addHours(3))
                ->where('reservation_start_time', '<=', now()->addHours(3))
                ->where('is_canceled', false)
                ->get()
                ->map(function ($reservation) {
                    $orders = $reservation->orders->map(function ($order) {
                        $carts = $order->carts
                            ->where('is_completed', 0)
                            ->map(function ($cart) {
                                $now_order_items = $cart->cart_items
                                    ->where('is_ready',0)
                                    ->where('is_pre_order', 0)
                                    ->where('is_selected_for_checkout',1);
                                if ($now_order_items->isEmpty()) {
                                    return null;
                                }

                                $new_total_price = 0;
                                foreach ($now_order_items as $item) {
                                    $itemTotal = $item->price_at_order * $item->quantity;
                                    $extrasTotal = 0;

                                    foreach ($item->extra_products as $extraProduct) {
                                        $extrasTotal += $extraProduct->extra->price;
                                    }

                                    $new_total_price += $itemTotal + $extrasTotal;
                                }

                                return [
                                    'created_at' => Carbon::parse($cart->created_at)->format('F j, Y'),
                                    'cart_id' => $cart->id,
                                    'total_now_order_price' => number_format(ceil($new_total_price), 0, ',', ',') . ' $',
                                    'items_count' => $now_order_items->count(),
                                ];
                            })
                            ->filter()
                            ->values();

                        if ($carts->isEmpty()) {
                            return null;
                        }

                        return [
                            'order_id' => $order->id,
                            'carts' => $carts,
                        ];
                    })
                    ->filter()
                    ->values();

                    if ($orders->isEmpty()) {
                        return null;
                    }

                    return [
                        'reservation_id' => $reservation->id,
                        'reservation_start_time' => Carbon::parse($reservation->reservation_start_time)->format('Y-m-d H:i:s'),
                        'reservation_end_time' => Carbon::parse($reservation->reservation_end_time)->format('Y-m-d H:i:s'),
                        'orders' => $orders,
                    ];
                })
                ->filter()
                ->values();

            if ($now_orders->isNotEmpty()) {
                return [
                    'data' => $now_orders,
                    'message' => __('message.Now_Orders_Retrived', [], $lang),
                    'code' => 200,
                ];
            } else {
                return [
                    'data' => [],
                    'message' => __('message.There_Arenot_Now_Orders', [], $lang),
                    'code' => 200,
                ];
            }
    }

    public function show_now_order($request): array
    {
            $lang = Auth::user()->preferred_language;

            $cart = Cart::with([
                    'cart_items.product.image',
                    'cart_items.offer.image',
                    'cart_items.extra_products.extra'
                ])
                ->where('id', $request['cart_id'])
                ->where('is_completed', 0)
                ->first();

            if (!$cart) {
                return [
                    'data' => [],
                    'message' => __('message.Cart_Not_Found', [], $lang),
                    'code' => 404
                ];
            }

            $now_cart_items = $cart->cart_items
                ->where('is_pre_order', 0)
                ->where('is_ready', 0)
                ->where('is_selected_for_checkout',1)
                ->map(fn($item) => $this->formatCartItem($item, $lang))
                ->filter()
                ->values();

            return [
                'data' => $now_cart_items,
                'message' => __('message.Now_Order_Retrieved', [], $lang),
                'code' => 200
            ];
    }

    public function create_order_now($request):array
    {
        $lang = Auth::user()->preferred_language;
        $customer_id = Auth::user()->customer->id;

        $exist_reservation = Reservation::query()
        ->where('customer_id',$customer_id)
        ->where('reservation_start_time','<=', now())
        ->where('reservation_end_time','>=', now())
        ->first();

        //return ['data'=>$exist_reservation,'message'=>'','code'=>200];

        if($exist_reservation)
        {
            $exist_order = Order::query()->where('reservation_id','=',$exist_reservation->id)->first();
            $cartItems = CartItem::with(['extra_products.extra'])
                ->whereIn('id', $request['cart_item_ids'])
                ->get();


                $new_total_price = 0;

                foreach ($cartItems as $item)
                {

                    $itemTotal = $item->price_at_order * $item->quantity;
                    $extrasTotal = 0;

                    foreach ($item->extra_products as $extraProduct)
                    {
                        $extrasTotal += $extraProduct->extra->price;
                    }

                    $new_total_price += $itemTotal + $extrasTotal;
                }



            $customer = Auth::user()->customer;
            if (!$customer->my_wallet)
            {
                $data = [];
                $message = __('message.Wallet_Not_Found', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                $code = 400;

                return ['data' => $data, 'message' => $message, 'code' => $code];
            }

            if(is_null($exist_order))
            {
                $new_order_now = Auth::user()->customer->orders()->create([
                    'reservation_id' => $exist_reservation->id,
                    'total_amount' => $new_total_price,
                ]);

                if($customer->my_wallet->amount < $new_order_now->total_amount)
                {
                    $data = [];
                    $message = __('message.Order_Price_exceeds_Your_Wallet', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                    $code = 400;

                    return ['data' => $data, 'message' => $message, 'code' => $code];
                }

                CartItem::query()
                ->whereIn('id', $request['cart_item_ids'])
                ->update([
                    'is_selected_for_checkout' => true,
                ]);

                $are_all_cart_items_taked = CartItem::query()
                ->where('cart_id', $request['cart_id'])
                ->where('is_selected_for_checkout', false)
                ->doesntExist();

                $new_payment = Payment::query()->create([
                    'order_id' => $new_order_now->id,
                    'amount' => $new_order_now->total_amount + $exist_reservation->table->price
                ]);

                $old_amount = $customer->my_wallet->amount;
                $customer->my_wallet()->update([
                    'amount' => $old_amount - $new_order_now->total_amount
                ]);

                Cart::query()
                    ->where('id', $request['cart_id'])
                    ->update([
                        'order_id' => $new_order_now->id,
                        'is_checked_out' => $are_all_cart_items_taked
                    ]);

                $data = [];
                $message = __('message.Order_Now_Created_And_Cart_Attached', [], $lang);
                $code = 201;
            }
            else
            {

                $exist_total_price = ($exist_order->total_amount) + $new_total_price;

                if($customer->my_wallet->amount < $new_total_price)
                {
                    $data = [];
                    $message = __('message.Order_Price_exceeds_Your_Wallet', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
                    $code = 400;

                    return ['data' => $data, 'message' => $message, 'code' => $code];
                }

                $exist_order->update([
                    'total_amount' => $exist_total_price
                ]);

                $exist_order->payment->update([
                    'amount' => $exist_order->total_amount + $exist_reservation->table->price
                ]);

                $old_amount = $customer->my_wallet->amount;
                $customer->my_wallet()->update([
                    'amount' => $old_amount - $new_total_price
                ]);

                CartItem::query()
                ->whereIn('id', $request['cart_item_ids'])
                ->update([
                    'is_selected_for_checkout' => true,
                ]);

                $are_all_cart_items_taked = CartItem::query()
                ->where('cart_id', $request['cart_id'])
                ->where('is_selected_for_checkout', false)
                ->doesntExist();

                Cart::query()
                    ->where('id', $request['cart_id'])
                    ->update([
                        'order_id' => $exist_order->id,
                        'is_checked_out' => $are_all_cart_items_taked
                    ]);

                $data = [];
                $message = __('message.Order_Now_Items_Attached', [], $lang);
                $code = 200;
            }
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }



    public function index_completed_orders()
    {
        $lang= Auth::user()->preferred_language;
        $completed_orders=Reservation::with(['orders.carts.cart_items.extra_products.extra'])
        ->where('customer_id', Auth::user()->customer->id)
        ->get()->map(function ($reservation) {
                    $orders = $reservation->orders->map(function ($order) {
                        $carts = $order->carts
                            ->where('is_completed', 1)
                            ->map(function ($cart) {
                                $now_order_items = $cart->cart_items->where('is_selected_for_checkout',1);
                                if ($now_order_items->isEmpty()) {
                                    return null;
                                }

                                $new_total_price = 0;
                                foreach ($now_order_items as $item) {
                                    $itemTotal = $item->price_at_order * $item->quantity;
                                    $extrasTotal = 0;

                                    foreach ($item->extra_products as $extraProduct) {
                                        $extrasTotal += $extraProduct->extra->price;
                                    }

                                    $new_total_price += $itemTotal + $extrasTotal;
                                }

                                return [
                                    'created_at' => Carbon::parse($cart->created_at)->format('F j, Y'),
                                    'cart_id' => $cart->id,
                                    'total_now_order_price' => number_format(ceil($new_total_price), 0, ',', ',') . ' $',
                                    'items_count' => $now_order_items->count(),
                                ];
                            })
                            ->filter()
                            ->values();

                        if ($carts->isEmpty()) {
                            return null;
                        }

                        return [
                            'order_id' => $order->id,
                            'carts' => $carts,
                        ];
                    })
                    ->filter()
                    ->values();

                    if ($orders->isEmpty()) {
                        return null;
                    }

                    return [
                        'reservation_id' => $reservation->id,
                        'reservation_start_time' => Carbon::parse($reservation->reservation_start_time)->format('Y-m-d H:i:s'),
                        'reservation_end_time' => Carbon::parse($reservation->reservation_end_time)->format('Y-m-d H:i:s'),
                        'orders' => $orders,
                    ];
                })
                ->filter()
                ->values();

                if ($completed_orders->isNotEmpty()) {
                return [
                    'data' => $completed_orders,
                    'message' => __('message.Completed_Orders_Retrived', [], $lang),
                    'code' => 200,
                ];
            } else {
                return [
                    'data' => [],
                    'message' => __('message.There_Arenot_Completed_Orders', [], $lang),
                    'code' => 200,
                ];
            }

    }

    public function show_completed_orders($request)
    {
        $lang = Auth::user()->preferred_language;

        $cart = Cart::with([
                'cart_items.product.image',
                'cart_items.offer.image',
                'cart_items.extra_products.extra'
            ])
            ->where('id', $request['cart_id'])
            ->where('is_completed', 1)
            ->first();

        if (!$cart) {
            return [
                'data' => [],
                'message' => __('message.Cart_Not_Found', [], $lang),
                'code' => 404
            ];
        }


        $completed_cart_items = $cart->cart_items->where('is_selected_for_checkout',1)
        ->map(fn($item) => $this->formatCartItem($item, $lang))
        ->filter()
        ->values();

        return [
            'data' => $completed_cart_items,
            'message' => __('message.Completed_Orders_Retrived', [], $lang),
            'code' => 200
        ];
    }

    public function show_now_order_for_chef(): array
    {
        $lang = Auth::user()->preferred_language;
        $chefId = Auth::user()->chef->id ?? null;

        if (!$chefId) {
            return [
                'data' => [],
                'message' => __('message.Chef_Not_Found', [], $lang),
                'code' => 404,
            ];
        }
        $now_cart_items = CartItem::query()
            ->where('is_pre_order', 0)
            ->where('is_ready', 0)
            ->where('is_selected_for_checkout', 1)
            ->where(function ($query) use ($chefId) {
                $query
                    // حالة العرض
                    ->where(function ($q) use ($chefId) {
                        $q->whereNotNull('offer_id')
                        ->whereHas('offer', function ($cq) use ($chefId) {
                            $cq->where('created_by', $chefId);
                        });
                    })
                    // حالة المنتج
                    ->orWhere(function ($q) use ($chefId) {
                        $q->whereNotNull('product_id')
                        ->whereHas('product', function ($pq) use ($chefId) {
                            $pq->where('chef_id', $chefId);
                        });
                    });
            })
            ->with([
                'product.image',
                'offer.image',
                'extra_products.extra'
            ])
            ->get()
            ->map(fn($item) => $this->formatCartItem($item, $lang))
            ->filter()
            ->values();

        if ($now_cart_items->isEmpty()) {
            return [
                'data' => [],
                'message' => __('message.No_Now_Orders', [], $lang),
                'code' => 404
            ];
        }

        return [
            'data' => $now_cart_items,
            'message' => __('message.Now_Order_Retrieved', [], $lang),
            'code' => 200
        ];
    }

    public function show_pre_order_for_chef(): array
    {
        $lang = Auth::user()->preferred_language;
        $chefId = Auth::user()->chef->id ?? null;

        if (!$chefId) {
            return [
                'data' => [],
                'message' => __('message.Chef_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        $pre_order_items = CartItem::query()
            ->where('is_pre_order', 1)
            ->where('is_ready', 0)
            ->where('is_selected_for_checkout', 1)
            ->where(function ($query) use ($chefId) {
                $query
                    ->where(function ($q) use ($chefId) {
                        $q->whereNotNull('offer_id')
                        ->whereHas('offer', function ($cq) use ($chefId) {
                            $cq->where('created_by', $chefId);
                        });
                    })
                    ->orWhere(function ($q) use ($chefId) {
                        $q->whereNotNull('product_id')
                        ->whereHas('product', function ($pq) use ($chefId) {
                            $pq->where('chef_id', $chefId);
                        });
                    });
            })
            ->with([
                'product.image',
                'offer.image',
                'extra_products.extra'
            ])
            ->get()
            ->map(fn($item) => $this->formatCartItem($item, $lang))
            ->filter()
            ->values();

        if ($pre_order_items->isEmpty()) {
            return [
                'data' => [],
                'message' => __('message.No_Pre_Orders', [], $lang),
                'code' => 404,
            ];
        }

        return [
            'data' => $pre_order_items,
            'message' => __('message.Pre_Order_Retrieved', [], $lang),
            'code' => 200,
        ];
    }

    public function mark_cart_item_ready( $request): array
    {
        $lang = Auth::user()->preferred_language;

        // جلب عنصر الكارت مع السلة والعناصر المرتبطة في نفس السلة
        $cartItem = CartItem::with('cart.cart_items')->find($request['cart_item_id']);

        if (!$cartItem) {
            return [
                'data' => [],
                'message' => __('message.CartItem_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        if (!$cartItem->cart) {
            return [
                'data' => [],
                'message' => __('message.Cart_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        // تعيين حالة العنصر كجاهز
        $cartItem->is_ready = true;
        $cartItem->save();

        // التأكد إن كل عناصر الكارت جاهزة
        $allReady = $cartItem->cart->cart_items->every(fn($item) => $item->is_ready == true);

        if ($allReady) {
            $cart = $cartItem->cart;
            $cart->is_completed = true;
            $cart->save();
        }

        return [
            'data' => [],
            'message' => __('message.CartItem_Ready_Updated', [], $lang),
            'code' => 200,
        ];
    }


}
