<?php

namespace App\Http\Controllers\Rate;

use App\Models\Product;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class RateService
{
    public function rate_product($request):array
    {
        $lang = Auth::user()->preferred_language;

        $product = Product::where('id', $request['product_id'])->first();

        if (!is_null($product))
        {
            $existing_rating = $product->ratings()->where('customer_id',Auth::user()->customer->id)->first();

            if ($existing_rating)
            {

                $existing_rating->update([
                    'rating' => $request['Rate']
                ]);
            }
            else
            {
                $product->ratings()->create([
                    'customer_id' => Auth::user()->customer->id,
                    'rating' => $request['Rate']
                ]);
            }

            $average = $product->ratings()->avg('rating');
            $product->update([
                'average_rating' => round($average, 2)
            ]);

            $data = [true];
            $message = __('message.Rate_Created',[],$lang);
            $code = 201;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }
}
