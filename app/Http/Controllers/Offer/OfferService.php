<?php

namespace App\Http\Controllers\Offer;

use App\Http\Controllers\ResponseHelper\ResponseHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Offer;
use App\Models\Product;
use Auth;
use Illuminate\Support\Facades\Storage;

class OfferService
{
    use ResponseHelper;
    use UplodeImageHelper;
    public function index (){
    $lang=Auth::user()->preferred_language;
    $offers = Offer::with('products')->get();
    if ($offers->isNotEmpty()){
    $data=[];
      foreach ($offers as $offer) {
       $data[]=[
       'id'=>$offer->id,
       'title'=>$offer->getTranslation('title', $lang),
       'total_price'=>$offer->total_price_text,
       'price_after_discount'=>$offer->price_after_discount_text,
       'discount_value'=>$offer->discount_value,
       'start_date'=>$offer->start_date,
       'end_date'=>$offer->end_date,
       'image'=> $offer->image ? url('storage/' . $offer->image->path) : null,
       'calories'=>$offer->products->sum('calories'),
       ];

      }
       $message=__('message.All_Offer_Retrived',[],$lang);
       return ['data'=>$data,'message'=>$message,'code'=>200];
    }
    else{
       $message=__('message.Offers_Not_Found',[],$lang);
       return ['data'=>[false],'message'=>$message,'code'=>404];
    }
}

    public function show($request):array
    {
        $offer = Offer::query()->where('id', '=', $request['offer_id'])->first();
        $lang = Auth::user()->preferred_language;

        if(!is_null($offer))
        {

           $data[]=[
           'offer'=>[
             'id'=>$offer->id,
             'title'=>$offer->getTranslation('title', $lang),
             'description'=>$offer->getTranslation('description', $lang),
             'total_price'=>$offer->total_price_text,
             'price_after_discount'=>$offer->price_after_discount_text,
             'discount_value'=>$offer->discount_value,
             'start_date'=>$offer->start_date,
             'end_date'=>$offer->end_date,
             'image'=> $offer->image ? url('storage/' . $offer->image->path) : null,
             'calories'=>$offer->products->sum('calories'),
            ],
            'products'=>[]
        ];
        foreach ($offer->products as $product) {
        $data[count($data) - 1]['products'][] = [
        'id' => $product->id,
        'name' => $product->getTranslation('name', $lang),
        'description' => $product->getTranslation('description', $lang),
        'price' => $product->price_text,
        'calories' => $product->calories_text,
        'image' => $product->image ? url('storage/' . $product->image->path) : null,
         ];
        }



            $message = __('message.Offer_Retrieved',[],$lang);
            $code = 200;
            return ['data' =>$data,'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Offer_Not_Found',[],$lang);
            $code = 404;
            return ['data' =>$offer,'message'=>$message,'code'=>$code];
        }
    }
    public function store($request):array
    {

        $lang = Auth::user()->preferred_language;



            $nameEn = $request['title_en'];
            $nameAr = $request['title_ar'];
            $offer = Offer::where('title->en', $nameEn)
                    ->orWhere('title->ar', $nameAr)
                    ->first();

            if($offer)
            {

                if($offer->getTranslation('title', 'en') === $nameEn && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_En_en',[],$lang);
                }
                else if ($offer->getTranslation('title', 'en') === $nameEn && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_En_ar',[],$lang);
                }

                else if($offer->getTranslation('title', 'ar') === $nameAr && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_Ar_en',[],$lang);
                }

                else if($offer->getTranslation('title', 'ar') === $nameAr && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_Ar_ar',[],$lang);
                }

                $data =[];
                $code = 409;

                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }

            $descriptionEn = $request['description_en'];
            $descriptionAr = $request['description_ar'];
            $discountRate = (float) str_replace('%', '', $request['discount_value']) / 100;

            $totalPrice = Product::query()->whereIn('id', $request['products_ids'])->sum('price');

            $discountAmount = $totalPrice * $discountRate;
            $priceAfterDiscount = $totalPrice - $discountAmount;
            $offer =Offer::create([
                'title' =>[
                    'en' => $nameEn,
                    'ar' => $nameAr,
                ],
                'description' =>[
                    'en' => $descriptionEn,
                    'ar' => $descriptionAr,
                ],
                'total_price' => $totalPrice,
                'discount_value' =>$request['discount_value'] ,
                'price_after_discount' => $priceAfterDiscount,
                'start_date'=>$request['start_date'],
                'end_date'=>$request['end_date'],
                'created_by'=>Auth::user()->id,
            ]);


            $offer->Products()->attach($request->products_ids);
            $offer->image()->create([
                'path' => $this->uplodeImage($request->file('image_file'),'offers')
            ]);

            $data =[true];
            $code = 201;
            $message= __('message.Offer_Created',[],$lang);

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }
    public function update($request):array
    {

        $old_offer = Offer::query()->where('id', '=', $request['offer_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($old_offer))
        {

            $nameEn = $request['title_en'];
            $nameAr = $request['title_ar'];

            $name_en_old = $old_offer->getTranslation('title', 'en');
            $name_ar_old = $old_offer->getTranslation('title', 'ar');

            $offer = Offer::where(function ($query) use ($nameEn,$name_en_old) {
                    $query->where('title->en', $nameEn)
                    ->where('title->en','!=', $name_en_old);
            })
            ->orWhere(function ($query) use ($nameAr, $name_ar_old) {
                    $query->where('title->ar', $nameAr)
                    ->where('title->ar', '!=', $name_ar_old);
            })
            ->first();

            if($offer)
            {

                if($offer->getTranslation('title', 'en') === $nameEn && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_En_en',[],$lang);
                }
                else if ($offer->getTranslation('title', 'en') === $nameEn && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_En_ar',[],$lang);
                }

                else if($offer->getTranslation('title', 'ar') === $nameAr && $lang == 'en')
                {
                    $message = __('message.Product_Already_Exist_Ar_en',[],$lang);
                }

                else if($offer->getTranslation('title', 'ar') === $nameAr && $lang == 'ar')
                {
                    $message = __('message.Product_Already_Exist_Ar_ar',[],$lang);
                }

                $data =[];
                $code = 409;

                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }
            $old_offer->update([
             'title' => [
             'en' => $request['title_en'] ?? $old_offer->title->en,
             'ar' => $request['title_ar'] ?? $old_offer->title->ar,
            ],
             'description' => [
             'en' => $request['description_en'] ?? $old_offer->description->en,
             'ar' => $request['description_ar'] ?? $old_offer->description->ar,
            ],
             'total_price' => $request['products_ids'] ? Product::whereIn('id', $request['products_ids'])->sum('price') : $old_offer->total_price,
             'discount_value' => $request['discount_value'] ?? $old_offer->discount_value,
             'price_after_discount' => $request['products_ids'] ?
                Product::whereIn('id', $request['products_ids'])->sum('price') *
                (1 - ((float)str_replace('%', '', ($request['discount_value'] ?? $offer->discount_value)) / 100))
                : $old_offer->price_after_discount,
            'start_date' => $request['start_date'] ?? $old_offer->start_date,
            'end_date' => $request['end_date'] ?? $old_offer->end_date,
            ]);

            if (isset($request['products_ids'])) {
            $old_offer->products()->sync($request['products_ids']);
            }
            $data = [true];
            $code= 200;
            $message = __('message.Offer_Updated',[],$lang);

            if (request()->hasFile('image_file'))
            {
                if ($old_offer->image && Storage::disk('public')->exists($old_offer->image->path))
                {
                    Storage::disk('public')->delete($old_offer->image->path);
                }

                $path = $this->uplodeImage(request()->file('image_file'), 'offers');

                $old_offer->image()->updateOrCreate([], ['path' => $path]);
            }

        return ['data'=>$data,'message'=>$message,'code'=>$code];

        }
        else
        {
            $message = __('message.Offer_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }
    }

    public function destroy($request):array
    {
        $offer = Offer::where('id', $request['offer_id'])->first();
        $lang = Auth::user()->preferred_language;

        if (!is_null($offer))
        {
            if(Storage::disk('public')->exists($offer->image->path))
            {
                Storage::disk('public')->delete($offer->image->path);
            }

            $offer->image()->delete();
            $offer->delete();

            $message = __('message.Offer_Deleted',[],$lang);
            $code = 200;

            return ['data'=> [],'message'=> $message,'code'=> $code];

        }

        else
        {
            $message = __('message.Offer_Not_Found',[],$lang);
            $code = 404;
            return ['data' => [], 'message' => $message, 'code' => $code];
        }
    }






}
