<?php

namespace App\Http\Controllers\Cart;

use App\Models\CartItem;
use App\Models\Product;
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


        if (!$exist_cart)
        {
            $exist_cart = collect();
        }
        else
        {
            $cart_items = $exist_cart->cart_items()
            ->with(['product.image', 'product.category']) // eager loading
            ->get()
            ->map(function ($item) use ($lang) {
                $product = $item->product;

                if (!$product) {
                    return null;
                }

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'name' => $product->getTranslation('name', $lang),
                    'product_image' => $product->image ? url(Storage::url($product->image->path)) : null,
                    'category_name' => $product->category ? $product->category->getTranslation('name', $lang) : null,
                    'total_price' => $item->total_price_text,
                    'quantity' => $item->quantity,
                ];
            })
            ->filter()
            ->values();

            if(!is_null($cart_items))
            {
                $total_price = CartItem::TotalPriceForCart($exist_cart->id);
                $data = [
                    'total_price' => number_format($total_price, 0, ',', ',') . ' $',
                    'cart_items' => $cart_items,
                ];
                $message = __('message.Cart_Retrived',[],$lang);
                $code = 200;

                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }


        }


        $data = $exist_cart;
        $message = __('message.Cart_Empty',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];



    }

    public function store($request):array
    {

        $lang = Auth::user()->preferred_language;
        $product = Product::where('id',$request['product_id'])->first();

        if(!is_null($product))
        {
            $exist_cart = Auth::user()->customer->carts()
            ->where('is_checked_out', false)
            ->latest()
            ->first();

            if(is_null($exist_cart))
            {
                $new_cart = Auth::user()->customer->carts()->create([
                    'order_id' => null,
                    'is_checked_out' => false
                ]);

                $new_cart->cart_items()->create([
                    'product_id' => $request['product_id'],
                    'price_at_order' => $product->price,
                    'total_price' => $product->price * $request['quantity'],
                    'quantity' => $request['quantity']
                ]);
            }
            else
            {
                $exist_cart_item = $exist_cart->cart_items()->where('product_id', $request['product_id'])->first();
                if($exist_cart_item)
                {
                    $exist_cart_item->update([
                        'quantity' => $exist_cart_item->quantity + $request['quantity']
                    ]);
                }
                else
                {
                    $exist_cart->cart_items()->create([
                        'product_id' => $request['product_id'],
                        'price_at_order' => $product->price,
                        'total_price' => $product->price * $request['quantity'],
                        'quantity' => $request['quantity']
                    ]);
                }
            }

            $data = [true];
            $message = __('message.Cart_Item_Added',[],$lang);
            $code = 201;

        }
        else
        {
            $data = [];
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function update($request):array
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
            $message = __('message.Cart_Item_Updated_Quantity',[],$lang);
            $code = 200;

        }

        return ['data'=>['total_price' => number_format($total_price, 0, ',', ',') . ' $',],'message'=>$message,'code'=>$code];
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
                    'total_price' => number_format($total_price, 0, ',', ',') . ' $',
                ];
            }

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }

        return ['data' =>[],'message'=>'','code'=>200];
    }
}
