<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ResponseHelper\ResponseHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Category;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth;
use Storage;

class ProductService
{

    use ResponseHelper;
    use UplodeImageHelper;
    public function index($request):array
    {
        $category = Category::query()->where('id',$request['category_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($category))
        {
            $products = $category->products()
                ->get()
                ->map(function ($product) use ($lang)
                {
                    $image_path = $product->image->path;
                    $name = $product->getTranslation('name', $lang);
                    $description = $product->getTranslation('description', $lang);
                    return [
                        'id' => $product->id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $product->price_text,
                        'calories' => $product->calories_text,
                        'image_path' => url(Storage::url($image_path) ?? null)
                ];
            });

            if ($products->isEmpty())
            {
                $message =__('message.No_Products_Available',[],$lang);
            }
            else
            {
                $message = __('message.Products_Retrieved',[],$lang);
            }

            $code = 200;
            return ['data' => $products, 'message' => $message, 'code' => $code];
        }

        else
        {
            $message = __('message.Category_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }

    }

    public function show($request):array
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
                'image_path' => url(Storage::url($image_path) ?? null)
            ];

            $message = __('message.Product_Retrieved',[],$lang);
            $code = 200;
            return ['data' =>$data,'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
            return ['data' =>$product,'message'=>$message,'code'=>$code];
        }
    }

    public function searchByCategory($request):array
    {
        $value = strtolower($request['value']);
        $category = Category::query()->where('id',$request['category_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($category))
        {
            $products = $category->products()
            ->where(DB::raw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')))"), 'LIKE', '%' . $value . '%')
            ->orWhere(DB::raw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')))"),'LIKE','%'.$value.'%')
            ->get()
            ->map(function ($product) use ($lang)
            {
                $image_path = $product->image->path;
                $name = $product->getTranslation('name', $lang);
                $description = $product->getTranslation('description', $lang);
                return [
                    'id' => $product->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $product->price_text,
                    'calories' => $product->calories_text,
                    'image_path' => url(Storage::url($image_path) ?? null)
                ];
            });

            if($products->isEmpty())
            {
                $message = __('message.No_Products_For_Category',[],$lang);
            }
            else
            {
                $message = __('message.Products_Retrieved',[],$lang);
            }

            $code = 200;
            return ['data' =>['products' => $products],'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Category_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }
    }

    public function search($request):array
    {
        $value = strtolower($request['value']);
        $lang = Auth::user()->preferred_language;

        $products = Product::query()
            ->where(DB::raw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')))"), 'LIKE', '%' . $value . '%')
            ->orWhere(DB::raw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')))"),'LIKE','%'.$value.'%')
            ->get()
            ->map(function ($product) use ($lang)
            {
                $image_path = $product->image->path;
                $name = $product->getTranslation('name', $lang);
                $description = $product->getTranslation('description', $lang);
                return [
                    'id' => $product->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $product->price_text,
                    'calories' => $product->calories_text,
                    'image_path' => url(Storage::url($image_path) ?? null)
                ];
            });

            if($products->isEmpty())
            {
                $message = __('message.No_Products_For_Category',[],$lang);
            }
            else
            {
                $message = __('message.Products_Retrieved',[],$lang);
            }

            $code = 200;
            return ['data' =>['products' => $products],'message'=>$message,'code'=>$code];

    }

    public function filter($request):array
    {
        $lang = Auth::user()->preferred_language;


        $products = Product::query()
            ->where('category_id','=',$request['category_id'])
            ->whereBetween('price', [ $request['price_start'], $request['price_end'] ])
            ->whereBetween('calories', [ $request['calories_start'], $request['calories_end'] ])
            ->get()
            ->map(function ($product) use ($lang)
            {
                $image_path = $product->image->path;
                $name = $product->getTranslation('name', $lang);
                $description = $product->getTranslation('description', $lang);
                return [
                    'id' => $product->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $product->price_text,
                    'calories' => $product->calories_text,
                    'image_path' => url(Storage::url($image_path) ?? null)
                ];
            });

            if($products->isEmpty())
            {
                $message = __('message.No_Products_For_Category',[],$lang);
            }
            else
            {
                $message = __('message.Products_Retrieved',[],$lang);
            }

            $code = 200;
            return ['data' =>['products' => $products],'message'=>$message,'code'=>$code];
    }


    public function store($request):array
    {
        $category = Category::query()->where('id',$request['category_id'])->first();

        $lang = Auth::user()->preferred_language;


        if(!is_null($category))
        {
            $nameEn = $request['name_en'];
            $nameAr = $request['name_ar'];
            $product = Product::where('name->en', $nameEn)
                    ->orWhere('name->ar', $nameAr)
                    ->first();

            if($product)
            {

                if($product->getTranslation('name', 'en') === $nameEn && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_En_en',[],$lang);
                }
                else if ($product->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_En_ar',[],$lang);
                }

                else if($product->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_Ar_en',[],$lang);
                }

                else if($product->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_Ar_ar',[],$lang);
                }

                $data =[];
                $code = 409;

                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }

            $descriptionEn = $request['description_en'];
            $descriptionAr = $request['description_ar'];

            $product = $category->products()->create([
                'name' =>[
                    'en' => $nameEn,
                    'ar' => $nameAr,
                ],
                'description' =>[
                    'en' => $descriptionEn,
                    'ar' => $descriptionAr,
                ],
                'chef_id' => Auth::user()->id,
                'price' => $request['price'],
                'calories' => $request['calories'],
            ]);


            $product->image()->create([
                'path' => $this->uplodeImage($request->file('image_file'),'products')
            ]);

            $data =[true];
            $code = 201;
            $message= __('message.Product_Created',[],$lang);

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }
        else
        {
            $message = __('message.Category_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }


    }


    public function update($request):array
    {

        $old_product = Product::query()->where('id', '=', $request['product_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($old_product))
        {

            $nameEn = $request['name_en'];
            $nameAr = $request['name_ar'];

            $name_en_old = $old_product->getTranslation('name', 'en');
            $name_ar_old = $old_product->getTranslation('name', 'ar');

            $product = Product::where(function ($query) use ($nameEn,$name_en_old) {
                    $query->where('name->en', $nameEn)
                    ->where('name->en','!=', $name_en_old);
            })
            ->orWhere(function ($query) use ($nameAr, $name_ar_old) {
                    $query->where('name->ar', $nameAr)
                    ->where('name->ar', '!=', $name_ar_old);
            })
            ->first();

            if($product)
            {

                if($product->getTranslation('name', 'en') === $nameEn && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_En_en',[],$lang);
                }
                else if ($product->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_En_ar',[],$lang);
                }

                else if($product->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_Ar_en',[],$lang);
                }

                else if($product->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_Ar_ar',[],$lang);
                }

                $data =[];
                $code = 409;

                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }

            $descriptionEn = $request['description_en'];
            $descriptionAr = $request['description_ar'];

            $old_product->update([
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
                ],
            'description' =>[
                    'en' => $descriptionEn,
                    'ar' => $descriptionAr,
                ],
            'price' => $request['price'],
            'calories' =>$request['calories']
            ]);

            $data = [true];
            $code= 200;
            $message = __('message.Product_Updated',[],$lang);

            if (request()->hasFile('image_file'))
            {
                if (Storage::disk('public')->exists($old_product->image->path))
                {
                    Storage::disk('public')->delete($old_product->image->path);
                }

                $path = $this->uplodeImage(request()->file('image_file'), 'products');

                $old_product->image()->updateOrCreate([], ['path' => $path]);
            }

        return ['data'=>$data,'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }
    }

    public function destroy($request):array
    {
        $product = Product::where('id', $request['product_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($product))
        {
            if(Storage::disk('public')->exists($product->image->path))
            {
                Storage::disk('public')->delete($product->image->path);
            }

            $product->image()->delete();
            $product->delete();

            $message = __('message.Product_Deleted',[],$lang);
            $code = 200;

            return ['data'=> [],'message'=> $message,'code'=> $code];

        }

        else
        {
            $message = __('message.Product_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }
    }
}
