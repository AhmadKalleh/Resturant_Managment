<?php

namespace App\Http\Controllers\Favorite;

use App\Jobs\DeleteFavoriteHistoryJob;
use App\Models\Favorite;
use App\Models\Product;
use Auth;
use Illuminate\Support\Facades\Storage;

class FavoriteService
{

    public function index():array
    {
        $lang = Auth::user()->preferred_language;

        $favorites = Auth::user()->customer->favorites()
        ->latest()
        ->get()
        ->map(function($favorite,) use ($lang)
        {
            $product = $favorite->product;
                return [
                    'id' =>$favorite->id,
                    'product_id' =>$product->id,
                    'name' => $product->getTranslation('name',$lang),
                    'description' => $product->getTranslation('description',$lang),
                    'price' => $product->price_text,
                    'calories' => $product->calories,
                    'image_path' => url(Storage::url($product->image->path)),
                ];
        });

        if ($favorites->isEmpty())
        {
            $data = [];
            $message = __('message.No_Favorites_Available',[],$lang);
            $code = 200;
        }
        else
        {
            $data = $favorites->toArray();
            $message = __('message.Favorites_Retrived',[],$lang);
            $code = 200;
        }


        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function store($request):array
    {
        $product = Product::where('id',$request['product_id'])->first();
        $lang = Auth::user()->preferred_language;
        if(!is_null($product))
        {

                if(Favorite::query()->where('product_id',$product->id)->first())
                {
                    $data = [];
                    $message = __('message.Favorite_Already_Exist',[],$lang);
                    $code = 410;
                    return ['data' =>$data,'message'=>$message,'code'=>$code];
                }
                else
                {
                    Auth::user()->customer->favorites()->create([
                        'product_id' => $request['product_id']
                    ]);

                    DeleteFavoriteHistoryJob::dispatch()->delay(now()->addMinute(10));

                    $data =[true];
                    $message = 'Added To favorite successfully';
                    $code = 201;
                }


        }
        else
        {
            $data=[];
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function destroy($request):array
    {
        $favorite = Auth::user()->customer->favorites()->where('id',$request['favorite_id'])->first();
        $lang = Auth::user()->preferred_language;
        if(!is_null($favorite))
        {

            Auth::user()->customer->favorites()->where('id',$request['favorite_id'])->delete();
            $data=[];
            $message = __('message.Favorite_Deleted',[],$lang);
            $code = 200;


        }
        else
        {
            $data=[];
            $message = __('message.Favorite_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }



}
