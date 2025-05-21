<?php

namespace App\Http\Controllers\Extra;

use App\Models\Extra;
use Illuminate\Support\Facades\Auth;

class ExtraService
{
    public function index():array
    {
        $extras = Extra::query()->with('chef')->get();
        $lang = Auth::user()->preferred_language;
        $data = [];
        foreach ($extras as $extra)
        {
            $data[] = [
                'id'=> $extra['id'],
                'name' => $extra->getTranslation('name', $lang),
                'chef' => [
                    'id' =>$extra['chef']['id'],
                    'speciality' => $extra['chef']->getTranslation('speciality', $lang),
                    'years_of_experience'=> $extra['chef']['years_of_experience'],
                    'bio' => $extra['chef']['bio'],
                    'certificates' =>json_decode($extra['chef']['certificates'], true),
                ],
                'price' => $extra->price_text,
                'calories' => $extra['calories'],
            ];
        }

        $message = __('message.All_Extra_Retrived',[],$lang);
        $code = 200;

        // Send the token to the client and send it to the server with the authorization
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function show($request):array
    {
        $extra = Extra::query()->where('id',$request['extra_id'])->with('chef')->first();
        $lang = Auth::user()->preferred_language;

        if($extra)
        {

            $data = [
                'id' => $extra->id,
                'name' => $extra->getTranslation('name', $lang),
                    'chef' => [
                        'id' =>$extra['chef']['id'],
                        'speciality' => $extra['chef']->getTranslation('speciality', $lang),
                        'years_of_experience'=> $extra['chef']['years_of_experience'],
                        'bio' => $extra['chef']['bio'],
                        'certificates' =>json_decode($extra['chef']['certificates'], true),
                    ],
                'price' => $extra->price_text,
                'calories' => $extra['calories'],
            ];
            $code = 200;

            $message = __('message.Extra_Retrived',[],$lang);
        }
        else
        {
            $data = [];
            $message = __('message.Extra_Not_Found',[],$lang);
            $code = 404;
        }


        return ['data' =>$data,'message'=>$message,'code'=>$code];


    }

    public function store($request):array
    {

        $lang = Auth::user()->preferred_language;
        $nameEn = $request['name_en'];
        $nameAr = $request['name_ar'];



        $extra = Extra::where('name->en', $nameEn)
                    ->orWhere('name->ar', $nameAr)
                    ->first();

        if($extra)
        {

            if($extra->getTranslation('name', 'en') === $nameEn && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_En_en',[],$lang);
            }
            else if ($extra->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_En_ar',[],$lang);
            }

            else if($extra->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_Ar_en',[],$lang);
            }

            else if($extra->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_Ar_ar',[],$lang);
            }

            $data =[];
            $code = 409;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }

        $extra = Extra::query()->create([
            'chef_id' => Auth::user()->id,
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
                ],
            'price' => $request['price'],
            'calories' =>$request['calories']
        ]);

        $data = [
            'id' => $extra->id,
            'name' => $extra->getTranslation('name', $lang),
                'chef' => [
                    'id' =>$extra['chef']['id'],
                    'speciality' => $extra['chef']->getTranslation('speciality', $lang),
                    'years_of_experience'=> $extra['chef']['years_of_experience'],
                    'bio' => $extra['chef']['bio'],
                    'certificates' =>json_decode($extra['chef']['certificates'], true),
                    ],
            'price' => $extra->price_text,
            'calories' => $extra['calories'],
        ];

        $message = __('message.Extra_Created',[],$lang);
        $code = 201;
        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }

    public function update($request)
    {
        $lang = Auth::user()->preferred_language;

        $old_extra = Extra::query()->where('id',$request['extra_id'])->first();

        $nameEn = $request['name_en'];
        $nameAr = $request['name_ar'];

        $name_en_old = $old_extra->getTranslation('name', 'en');
        $name_ar_old = $old_extra->getTranslation('name', 'ar');

        $extra = Extra::where(function ($query) use ($nameEn,$name_en_old) {
                $query->where('name->en', $nameEn)
                ->where('name->en','!=', $name_en_old);
        })
        ->orWhere(function ($query) use ($nameAr, $name_ar_old) {
                $query->where('name->ar', $nameAr)
                ->where('name->ar', '!=', $name_ar_old);
        })
        ->first();

        if($extra)
        {

            if($extra->getTranslation('name', 'en') === $nameEn && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_En_en',[],$lang);
            }
            else if ($extra->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_En_ar',[],$lang);
            }

            else if($extra->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_Ar_en',[],$lang);
            }

            else if($extra->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_Ar_ar',[],$lang);
            }

            $data =[];
            $code = 409;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }

        $old_extra->update([
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
                ],
            'price' => $request['price'],
            'calories' =>$request['calories']
        ]);

        $data = [
            'id' => $old_extra->id,
            'name' => $old_extra->getTranslation('name', $lang),
                'chef' => [
                    'id' =>$old_extra['chef']['id'],
                    'speciality' => $old_extra['chef']->getTranslation('speciality', $lang),
                    'years_of_experience'=> $old_extra['chef']['years_of_experience'],
                    'bio' => $old_extra['chef']['bio'],
                    'certificates' =>json_decode($old_extra['chef']['certificates'], true),
                    ],
            'price' => $old_extra->price_text,
            'calories' => $old_extra['calories'],
        ];
        $code= 200;
        $message = __('message.Extra_Updated',[],$lang);

        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }

    public function delete($request)
    {
        $extra = Extra::query()->where('id',$request['extra_id'])->first();
        $lang = Auth::user()->preferred_language;
        if($extra)
        {
            $extra->delete();
            $data = [];
            $code= 200;
            $message = __('message.Extra_Deleted',[],$lang);
        }
        else
        {
            $data = [];
            $message = __('message.Extra_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }
}
