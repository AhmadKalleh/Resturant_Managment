<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ResponseHelper\ResponseHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth;
use Storage;

class ProductService
{

    use ResponseHelper;
    use UplodeImageHelper;

    public function top_ratings()
    {
        $categories = Category::with('products.image')->get();
        $lang = Auth::user()->preferred_language;

        // اجمع أعلى منتجين من كل فئة
        $allTopProducts = $categories->flatMap(function ($category) {
            return $category->products->sortByDesc(function ($product) {
                return optional($product->average_rating) ?? 0;
            })->take(2);
        })->unique('id') // إزالة المنتجات المكررة في حال تكررت عبر الفئات
        ->sortByDesc(function ($product) {
            return optional($product->average_rating) ?? 0;
        })->values();

        $customer = Auth::user()->customer;
        $exist_cart = $customer->carts()
                ->where('is_checked_out', false)
                ->latest()
                ->first();

            // منتجات المفضلة كلها
        $favorite_products_ids = $customer->favorites()->pluck('product_id')->toArray();


        // تنسيق المنتجات حسب اللغة
        $formattedProducts = $allTopProducts->map(function ($product) use ($lang,$exist_cart, $favorite_products_ids) {
            $image_path = $product->image->path ?? null;
                    $name = $product->getTranslation('name', $lang);
                    $description = $product->getTranslation('description', $lang);


                    $in_cart = false;
                    if ($exist_cart)
                    {
                        $in_cart = $exist_cart->cart_items()
                            ->where('product_id', $product->id)
                            ->exists();
                    }


                    $in_favorite = in_array($product->id, $favorite_products_ids);

                    return
                    [
                        'id' => $product->id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $product->price_text,
                        'calories' => $product->calories_text,
                        'image_path' => url(Storage::url($image_path)),
                        'rating' => $product->average_rating,
                        'exist_in_cart' => $in_cart,
                        'exist_in_favorite' => $in_favorite,
                    ];
        });

        if ($formattedProducts->isNotEmpty()) {
            $message = __('message.Top_Ratings_Retirived', [], $lang);
            $data = $formattedProducts;
        } else {
            $message = __('message.No_Products_Available', [], $lang);
            $data = [];
        }

        return [
            'data' => $data,
            'message' => $message,
            'code' => 200,
        ];
    }





    public function index($request): array
    {
        $category = Category::query()->where('id', $request['category_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($category)) {

            $customer = Auth::user()->customer;

            // آخر سلة غير مشتراة
            $exist_cart = $customer->carts()
                ->where('is_checked_out', false)
                ->latest()
                ->first();

            // منتجات المفضلة كلها
            $favorite_products_ids = $customer->favorites()->pluck('product_id')->toArray();

            if($category->getTranslation('name', $lang) =='Top Ratings' || $category->getTranslation('name', $lang) =='الأعلى تقييما')
            {
                $pro_service = new ProductService();
                $products = collect($pro_service->top_ratings()['data']);
            }
            else
            {

                $products = $category->products()
                    ->with('favorites')
                    ->get()
                    ->sortByDesc(fn($product) => $product->average_rating ?? 0)
                    ->values()
                    ->map(function ($product) use ($lang, $exist_cart, $favorite_products_ids) {

                        $image_path = $product->image->path ?? null;
                        $name = $product->getTranslation('name', $lang);
                        $description = $product->getTranslation('description', $lang);


                        $in_cart = false;
                        if ($exist_cart)
                        {
                            $in_cart = $exist_cart->cart_items()
                                ->where('product_id', $product->id)
                                ->exists();
                        }


                        $in_favorite = in_array($product->id, $favorite_products_ids);

                        return
                        [
                            'id' => $product->id,
                            'name' => $name,
                            'description' => $description,
                            'price' => $product->price_text,
                            'calories' => $product->calories_text,
                            'image_path' => url(Storage::url($image_path)),
                            'rating' => $product->average_rating,
                            'exist_in_cart' => $in_cart,
                            'exist_in_favorite' => $in_favorite,
                        ];

                    });
            }

            $message = $products->isEmpty()
                ? __('message.No_Products_Available', [], $lang)
                : __('message.Products_Retrieved', [], $lang);

            return [
                'data' => $products,
                'message' => $message,
                'code' => 200
            ];

        }
        else
        {
            return [
                'data' => [],
                'message' => __('message.Category_Not_Found', [], $lang),
                'code' => 404
            ];
        }
    }


    public function show_product_by_chef($request):array
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
                'rating' => ($product->average_rating),
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

    public function show($request):array
    {
        $product = Product::query()->where('id', '=', $request['product_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($product))
        {

            $image_path = $product->image->path;
            $name = $product->getTranslation('name', $lang);
            $description = $product->getTranslation('description', $lang);

            $exist_cart = Auth::user()->customer->carts()
            ->where('is_checked_out', false)
            ->latest()
            ->first();

            $exist_product_in_favorite = null ;
            $exist_product_in_favorite = Auth::user()->customer->favorites()
            ->where('product_id',$product->id)
            ->first();

            $cart_item =null;

            if ($exist_cart)
            {
                $cart_item = $exist_cart->cart_items()->where('product_id', $product->id)->with('extra_products')->first();
            }

            $data = [
                'id' => $product->id,
                'name' => $name,
                'description' => $description,
                'price' => $product->price_text,
                'calories' => $product->calories_text,
                'rating' => ($product->average_rating),
                'exist_in_cart' => ($cart_item) ? true:false,
                'exist_in_favorite' =>($exist_product_in_favorite) ? true:false,
                'image_path' => url(Storage::url($image_path) ?? null),
                'extra_product' => $product->extra_products->map(function ($extraProduct) use ($lang,$cart_item)
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
                'chef_id' => Auth::user()->chef->id,
                'price' => $request['price'],
                'calories' => $request['calories'],
                'average_rating' => 0.0
            ]);



            $product->image()->create([
                'path' => $this->uplodeImage($request->file('image_file'),'products')
            ]);

            $data =[];
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

            $data = [];
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
