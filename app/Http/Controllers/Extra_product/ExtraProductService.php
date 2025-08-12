<?php

namespace App\Http\Controllers\Extra_product;

use App\Models\Extra;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExtraProductService
{
    public function show_extra_product_details($request):array
    {
        $product = Product::query()->where('id', '=', $request['product_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($product))
        {

            $image_path = $product->image->path;
            $name = $product->getTranslation('name', $lang);
            $description = $product->getTranslation('description', $lang);

            $data = [
                'id' => $product->id,
                'name' => $name,
                'description' => $description,
                'price' => $product->price_text,
                'calories' => $product->calories_text,
                'rating' => $product->average_rating,
                'image_path' => url(Storage::url($image_path) ?? null),
                'extra_product' => $product->extra_products->map(function ($extraProduct) use ($lang)
                {
                    return [
                        'extra_product_id' => $extraProduct->id,
                        'extra_name' => $extraProduct->extra?->getTranslation('name', $lang),
                        'extra_price' => $extraProduct->extra->price_text,
                    ];
            }),

            ];

            $message = __('message.Product_Retrieved',[],$lang);
            $code = 200;
            return ['data' =>$data,'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
            return ['data' =>[],'message'=>$message,'code'=>$code];
        }
    }

    public function store_extra_product($request): array
    {
        $lang = Auth::user()->preferred_language;

        $product = Product::find($request['product_id']);
        if (!$product) {
            return [
                'data' => [],
                'message' => __('message.Product_Not_Found', [], $lang),
                'code' => 404
            ];
        }

        // إزالة التكرار داخل المصفوفة
        $extraIds = array_unique($request['extra_ids']);

        // جلب الإضافات الموجودة مسبقاً (ID + الاسم حسب اللغة)
        $existingExtras = $product->extra_products()->pluck('extra_id')->toArray();

        $toAdd = array_diff($extraIds, $existingExtras);

        $duplicates = array_intersect($request['extra_ids'], $existingExtras);

        $extras = Extra::whereIn('id', $duplicates)->get();

        $existingNames = $extras->map(function($extra) use ($lang) {
            return $extra->getTranslation('name', $lang);
        })->toArray();


        if (!empty($existingNames)) {
            $message = __('message.Some_Extras_Already_Exist', [], $lang) . ': ' . implode(', ', $existingNames);
            $code = 200; // نجاح مع تنبيه
        } else {
            // إضافة الجديد بدون حذف القديم

            foreach($toAdd as $extraId)
            {
                $product->extra_products()->create([
                    'extra_id' => $extraId
                ]);
            }

            $message = __('message.Extras_Attached_Successfully', [], $lang);
            $code = 201; // نجاح وإنشاء جديد فقط
        }

        return [
            'data' => [],
            'message' => $message,
            'code' => $code
        ];
    }


    public function delete_extra_product($request) : array
    {
        $product = Product::query()->where('id', '=', $request['product_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($product))
        {
            $product->extra_products()->where('id','=', $request['extra_product_id'])->delete();
            $data = [];
            $message = __('message.Extra_Deleted',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }
}
