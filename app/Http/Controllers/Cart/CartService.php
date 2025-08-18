<?php

namespace App\Http\Controllers\Cart;

use App\Models\CartItem;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CartService
{

    public function index(): array
    {

        $lang = Auth::user()->preferred_language;

        $exist_cart = Auth::user()->customer->carts()
        ->where('is_checked_out', false)
        ->latest()
        ->first();

        $cart_items = collect(); // تعريف مبدئي كمجموعة فاضية


        if (!$exist_cart)
        {
            $data = [];
            $message = __('message.Cart_Empty',[],$lang);
            $code = 200;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }


        $now = now();
        $reservation = Auth::user()
        ->customer
        ->reservations()
        ->where(function ($query) use ($now) {
                $query->where('reservation_start_time', '<=', $now)
                    ->where('reservation_end_time', '>=', $now);
        })
        ->where('is_canceled', false)
        ->first();

        $type = null;

        //return ['data' => $reservation['reservation_start_time']->toDateTimeString(),'message'=>'h','code'=>200];

        if ($reservation)
        {
            if ($reservation->is_checked_in) {
                $type = 'in_session';
            }
            else
            {
                $type = 'pre_order';
            }
        }
        else
        {
            $lastReservation = Auth::user()->customer
                ->reservations()
                ->where('is_canceled', false)
                ->latest('reservation_start_time')
                ->first();

            if ($lastReservation && $lastReservation->reservation_start_time > $now) {
                $type = 'pre_order';
            }
        }

            $cart_items = $exist_cart->cart_items()
                ->with([
                    'product.image',
                    'product.category',
                    'extra_products.extra',
                    'offer.image'
                ])
                ->where('is_selected_for_checkout',false)
                ->get()
                ->map(function ($item) use ($lang)
                {
                    // If the item is a product
                    if ($item->product_id)
                    {
                        $product = $item->product;

                        if (!$product)
                        {
                            return null;
                        }

                        return [
                                'cart_item_id' => $item->id,
                                'type' => 'product',
                                'product_id' => $product->id,
                                'name' => $product->getTranslation('name', $lang),
                                'product_image' => $product->image ? url(Storage::url($product->image->path)) : null,
                                'category_name' => $product->category ? $product->category->getTranslation('name', $lang) : null,
                                'product_price' => $product->price_text,
                                'quantity' => $item->quantity,
                                'total_product_price' => ($product->price * $item->quantity) + $item->extra_products->sum(function ($extraProduct) {
                                    return $extraProduct->extra?->price ?? 0;
                                }). ' $',
                                'extras' => $product->extra_products->map(function ($extraProduct) use ($lang,$item)
                                {
                                    $is_reserved = false;

                                    if ($item && $item->extra_products->contains($extraProduct->id)) {
                                        $is_reserved = true;
                                    }

                                    return [
                                        'extra_product_id' => $extraProduct->id,
                                        'extra_name' => $extraProduct->extra?->getTranslation('name', $lang),
                                        'extra_price' => $extraProduct->extra->price_text,
                                        'is_reserved' => $is_reserved
                                    ];

                                })
                        ];

                    }

                    // If the item is an offer
                    elseif ($item->offer_id)
                    {
                        $offer = $item->offer;

                        if (!$offer) {
                            return null;
                        }

                        return [
                            'cart_item_id' => $item->id,
                            'type' => 'offer',
                            'offer_id' => $offer->id,
                            'title' => $offer->getTranslation('title', $lang),
                            'offer_image' => $offer->image ? url(Storage::url($offer->image->path)) : null,
                            'offer_price' => $offer->price_after_discount_text,
                            'quantity' => $item->quantity,
                        ];
                    }

                    return null;

                })
                ->filter()
                ->values();



            if ($cart_items->isNotEmpty())
            {
                $total_price = CartItem::TotalPriceForCart($exist_cart->id);
                $data = [
                    'cart_id' => $exist_cart->id,
                    'type' => $type,
                    'cart_total_price' => $total_price . ' $',
                    'cart_items' => $cart_items,
                ];
                $message = __('message.Cart_Retrived', [], $lang);
                $code = 200;
            }
            else
            {
                $data = [];
                $message = __('message.Cart_Empty', [], $lang);
                $code = 200;
            }

            return ['data' => $data, 'message' => $message, 'code' => $code];



    }

    public function show_own_extra_for_product($request)
    {
        $cart_item = CartItem::query()->where('id', '=', $request['cart_item_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($cart_item))
        {

            $product  = $cart_item->product()->first();

            $data = $product->extra_products->map(function ($extraProduct) use ($lang,$cart_item)
                {
                    $is_reserved = false;

                    if ($cart_item && $cart_item->extra_products->contains($extraProduct->id)) {
                        $is_reserved = true;
                    }

                    return [
                        'extra_product_id' => $extraProduct->id,
                        'extra_name' => $extraProduct->extra?->getTranslation('name', $lang),
                        'extra_price' => $extraProduct->extra->price_text,
                        'is_reserved' => $is_reserved
                    ];

                });

            $message = __('message.All_Extra_Retrived', [], $lang);
            $code = 200;


            return ['data' => $data, 'message' => $message, 'code' => $code];

        }
    }

    public function store($request): array
    {
        $lang = Auth::user()->preferred_language;

        $isProduct = !empty($request['product_id']);
        $isOffer   = !empty($request['offer_id']);

        $itemModel = $isProduct
            ? Product::find($request['product_id'])
            : Offer::find($request['offer_id']);

        if (!$itemModel) {
            return [
                'data' => [],
                'message' => $isProduct
                    ? __('message.Product_Not_Found', [], $lang)
                    : __('message.Offer_Not_Found', [], $lang),
                'code' => 404
            ];
        }

        // احصل على السلة الحالية أو أنشئ واحدة جديدة
        $exist_cart = Auth::user()->customer->carts()
            ->where('is_checked_out', false)
            ->latest()
            ->first();

        if (is_null($exist_cart)) {
            $exist_cart = Auth::user()->customer->carts()->create([
                'order_id' => null,
                'is_checked_out' => false
            ]);
        }

        // Default price
        $price = $isProduct ? $itemModel->price : $itemModel->price_after_discount;

        // الحالة: إذا كان المنتج
        if ($isProduct)
        {
            $extraProductIds = collect($request['extra_product_ids'] ?? [])
                ->map(fn($id) => (int)$id)
                ->sort()
                ->values()
                ->toArray();

            $existing_items = $exist_cart->cart_items()
                ->where('product_id', $request['product_id'])
                ->get();

            $cart_item = null;

            foreach ($existing_items as $item) {
                $existing_extra_ids = $item->extra_products()->pluck('extra_product_id')->sort()->values()->toArray();

                $isSameExtras = $extraProductIds === $existing_extra_ids;
                $isBothEmpty = empty($extraProductIds) && empty($existing_extra_ids);

                if ($isSameExtras || $isBothEmpty) {
                    $cart_item = $item;
                    break;
                }
            }

            if ($cart_item) {
                // إذا كان موجود مسبقًا، فقط نزيد الكمية
                $cart_item->update([
                    'quantity' => $cart_item->quantity + $request['quantity']
                ]);
            } else {
                // غير موجود → إنشاء عنصر جديد في السلة
                $cart_item = $exist_cart->cart_items()->create([
                    'product_id' => $request['product_id'],
                    'price_at_order' => $price,
                    'total_price' => $price * $request['quantity'],
                    'quantity' => $request['quantity']
                ]);

                if (count($extraProductIds) > 0) {
                    $cart_item->extra_products()->sync($extraProductIds);
                }
            }

            // حساب السعر الكامل مع الإضافات
            $total_extras_price = $cart_item->extra_products->sum(fn($extraProduct) => $extraProduct->extra->price ?? 0);
            $total_price = rtrim(rtrim(number_format($cart_item->total_price + $total_extras_price, 2, '.', ','), '0'), '.') . ' $';

        }
        else
        {
            $exist_cart_item = CartItem::query()->where('offer_id','=',$request['offer_id'])->first();

            if($exist_cart_item)
            {
                $exist_cart_item->update([
                    'quantity' => $exist_cart_item->quantity + $request['quantity']
                ]);
            }
            else
            {
                $cart_item = $exist_cart->cart_items()->create([
                    'offer_id' => $request['offer_id'],
                    'price_at_order' => $price,
                    'total_price' => $price * $request['quantity'],
                    'quantity' => $request['quantity']
                ]);
            }


        }

        return [
            'data' => [],
            'message' => __('message.Cart_Item_Added', [], $lang),
            'code' => 201
        ];
    }

    public function update_quantity($request):array
    {
        $cart_item = CartItem::query()->where('id', $request['cart_item_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($cart_item))
        {
            $exist_cart = $cart_item->cart()
            ->where('is_checked_out', false)
            ->first();

            if($request['quantity'] == 0)
            {
                $cart_item->delete();
                $cart_items = $exist_cart->cart_items()->get();

                if(is_null($cart_items))
                {
                    $exist_cart->delete();

                    $data = [];
                    $message = __('message.Cart_Empty',[],$lang);
                    $code = 200;

                    return ['data' =>$data,'message'=>$message,'code'=>$code];
                }
            }
            else
            {
                $cart_item->update([
                    'quantity'=> $request['quantity'],
                ]);
            }

            $total_price = CartItem::TotalPriceForCart($exist_cart->id);
            if($cart_item->product_id)
            {
                $total_price_cart_item = $cart_item->total_price + $cart_item->extra_products->sum(function ($extraProduct) {
                                    return $extraProduct->extra?->price ?? 0;
                }). ' $';

            }
            else
            {
                $total_price_cart_item = $cart_item->total_price .' $';
            }

            $data = [
                'total_price_cart' => $total_price . ' $',
                'total_price_cart_item' => $total_price_cart_item
            ];
            $message = __('message.Cart_Item_Updated_Quantity',[],$lang);
            $code = 200;

        }
        else
        {
            $data = [];
            $message = __('message.Cart_Item_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }

    public  function update_cart_item($request)
    {
        $cart_item = CartItem::query()->where('id', $request['cart_item_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($cart_item))
        {
            $cart_item->extra_products()->sync($request['extra_product_ids']);

            $exist_cart = $cart_item->cart()
            ->where('is_checked_out', false)
            ->first();

            $total_price = CartItem::TotalPriceForCart($exist_cart->id);
            $total_price_cart_item = $cart_item->total_price + $cart_item->extra_products->sum(function ($extraProduct) {
                        return $extraProduct->extra?->price ?? 0;
                }). ' $';


            $data = [
                'total_price_cart' => $total_price . ' $',
                'total_price_cart_item' => $total_price_cart_item
            ];
            $message = __('message.Cart_Item_Extra_Product_Updated',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Cart_Item_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }

    public function destroy_extra($request):array
    {
        $cart_item = CartItem::query()->where('id', $request['cart_item_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($cart_item))
        {
            $exist_cart = $cart_item->cart()
            ->where('is_checked_out', false)
            ->first();
            $extra = $cart_item->extra_products()->where('extra_product_id', $request['extra_product_id'])->first();

            if(!is_null($extra))
            {
                $extra->delete();
                $total_price = CartItem::TotalPriceForCart($exist_cart->id);
                $data = [
                    'total_price' => $total_price . ' $'
                ];

                $message = __('message.Extra_Deleted',[],$lang);
                $code = 200;
            }
            else
            {
                $data = [];
                $message = __('message.Extra_Not_Found',[],$lang);
                $code = 404;
            }
        }
        else
        {
            $data = [];
            $message = __('message.Cart_Item_Not_Found',[],$lang);
            $code = 404;
        }
        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }

    public function destroy($request):array
    {
        $cart_item = CartItem::query()->where('id', $request['cart_item_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($cart_item))
        {
            $exist_cart = $cart_item->cart()
            ->where('is_checked_out', false)
            ->first();

            $cart_items = $exist_cart->cart_items()->get();
            $data = [];
            $message = __('message.Cart_Item_Deleted',[],$lang);
            $code = 200;

            if(count($cart_items) == 1)
            {
                $exist_cart->delete();
            }
            else
            {
                $cart_item->delete();
                $total_price = CartItem::TotalPriceForCart($exist_cart->id);
                $data = [
                    'total_price' => $total_price. ' $',
                ];
            }

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }
        else
        {
            $data = [];
            $message = __('message.Cart_Item_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>[],'message'=>'','code'=>200];
    }
}
