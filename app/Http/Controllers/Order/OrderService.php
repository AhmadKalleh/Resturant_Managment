<?php

namespace App\Http\Controllers\Order;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderService
{
    public function index_pre_orders():array
    {
        $lang = Auth::user()->preferred_language;


        $pre_orders = Reservation::with(['orders.carts.cart_items.extra_products.extra'])
        ->where('customer_id', Auth::user()->customer->id)
        ->where('is_canceled', false)
        ->where('is_checked_in', false)
        ->where('reservation_end_time', '>=', now()->addHours(3))
        ->get()
        ->map(function($reservation) {
            return [
                'reservation_id'=> $reservation->id,
                'reservation_start_time' => $reservation->reservation_start_time,
                'reservation_end_time' => $reservation->reservation_end_time,
                'orders' => $reservation->orders->map(function($order) {
                    return [
                        'order_id'=>$order->id,
                        'carts' => $order->carts->map(function($cart){
                            $pre_order_items = $cart->cart_items->where('is_pre_order', 1)->where('prepare_at','>',now()->addHours(3));
                            $maxPrepareAt = $pre_order_items->max('prepare_at');

                            if (!$maxPrepareAt || Carbon::parse($maxPrepareAt)->lt(now()->addHours(3))) {
                                return null;
                            }

                            $new_total_price = 0;
                            foreach ($pre_order_items as $item)
                            {
                                $itemTotal = $item->price_at_order * $item->quantity;
                                $extrasTotal = 0;

                                foreach ($item->extra_products as $extraProduct)
                                {
                                    $extrasTotal += $extraProduct->extra->price;
                                }

                                $new_total_price += $itemTotal + $extrasTotal;
                            }

                            return [
                                'created_at' => Carbon::parse($cart->created_at)->format('F j, Y'),
                                'cart_id' => $cart->id,
                                'total_pre_order_price' => number_format(ceil($new_total_price), 0, ',', ',') . ' $',
                                'items_count' => $pre_order_items->count()
                            ];
                        })->filter()->values()

                    ];
                })

            ];
        });


        if ($pre_orders->filter(function($reservation)
        {
            return collect($reservation['orders'])->filter(function($order) {
                return isset($order['carts']) && count($order['carts']) > 0;
            })->isNotEmpty();
        })->isNotEmpty())
        {
            $data = $pre_orders;
            $message = __('message.Pre_Orders_Retrived', [], $lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.There_Arenot_Pre_Orders', [], $lang);
            $code = 200;
        }



        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function show_pre_order($request):array
    {
        $lang = Auth::user()->preferred_language;

        $cart_items_for_pre_order = Cart::query()
            ->with(['cart_items.product'])
            ->where('id', $request['cart_id'])
            ->first()
            ?->cart_items
            ->filter(function($item) {
                return $item->is_pre_order == 1;
            })
            ->values()
            ->map(function($item) use ($lang) {
                if (Carbon::parse($item->prepare_at)->gt(now()->addHours(3)))
                {
                    $prepare_at = Carbon::parse($item->prepare_at)->subMinutes(30);
                    $now = now()->addHours(3);
                    $is_cancelable = $now <= $prepare_at;

                    $new_total_price = 0;
                    $itemTotal = $item->price_at_order * $item->quantity;
                    $extrasTotal = 0;

                    foreach ($item->extra_products as $extraProduct) {
                        $extrasTotal += $extraProduct->extra->price;
                    }

                    $new_total_price += $itemTotal + $extrasTotal;

                    return [
                        'cart_item_id'   => $item->id,
                        'quantity'       => $item->quantity,
                        'total_price'    => number_format(ceil($new_total_price), 0, ',', ',') . ' $' ,
                        'prepare_at'     => Carbon::parse($item->prepare_at)->format('F j, Y \a\t h:i A'),
                        'product_id'     => $item->product->id ,
                        'product_name'   => $item->product->getTranslation('name', $lang),
                        'calories'       => $item->product->calories_text,
                        'product_image'  => url(Storage::url($item->product->image->path)),
                        'product_price'  => $item->product->price_text ,
                        'is_cancelable'  => $is_cancelable
                    ];
                }


                return null;
            })
            ->filter()
            ->values();



        if ($cart_items_for_pre_order->isNotEmpty())
        {
            $data = $cart_items_for_pre_order;
            $message = __('message.Pre_Order_Retrived', [], $lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.There_Isnot_Pre_Order', [], $lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

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
                // return [
                //     'data' => $are_all_cart_items_taked,
                //     'message'=>'fsdfs',
                //     'code'=>200
                // ];

            if(is_null($exist_order))
            {

                $new_pre_order = Auth::user()->customer->orders()->create([
                    'reservation_id' => $request['reservation_id'],
                    'total_amount' => $new_total_price,
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

                $exist_order->update([
                    'total_amount' => $exist_total_price
                ]);

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
}
