<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\TranslateHelper\TranslateHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Category;
use Auth;
use Storage;

class CategoryService{
use UplodeImageHelper;

    use TranslateHelper;
    public function index():array
    {
        $categories = Category::query()->with('image','chef')->get();
        $lang = Auth::user()->preferred_language;
        $data = [];
        if($categories)
        {
            foreach ($categories as $category)
            {
                $data[] =
                    [
                        "id"=>$category->id,
                        "name"=>$category->getTranslation('name', $lang),
                        "description"=>$category->getTranslation('description', $lang),
                        "image_path"=>url('storage/' . $category->image->path),
                        "chef"=>
                        [
                            'id' =>$category->chef->id,
                            'speciality' => $category->chef->getTranslation('speciality', $lang),
                            'years_of_experience'=> $category->chef->years_of_experience,
                            'bio' => $category->chef->bio,
                            'certificates' =>json_decode($category['chef']['certificates'], true),
                        ]
                    ];

            }

            $message = __('message.All_category_Retrived',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Categore_Not_Found',[],$lang);
            $code = 404;
        }
        // Send the token to the client and send it to the server with the authorization
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function show($request):array
    {
        $category = Category::query()->where('id',$request['category_id'])->with('chef')->first();
        $lang = Auth::user()->preferred_language;

        if($category)
        {

            $data = [
                'id' => $category->id,
                'name' => $category->getTranslation('name', $lang),
                "description"=>$category->getTranslation('description', $lang),
                "image_path"=>url('storage/' . $category->image->path),
                    'chef' => [
                        'id' =>$category['chef']['id'],
                        'speciality' => $category['chef']->getTranslation('speciality', $lang),
                        'years_of_experience'=> $category['chef']['years_of_experience'],
                        'bio' => $category['chef']['bio'],
                        'certificates' =>json_decode($category['chef']['certificates'], true),
                    ],

            ];
            $code = 200;

            $message = __('message.Category_Retrived',[],$lang);
        }
        else
        {
            $data = [];
            $message = __('message.Category_Not_Found',[],$lang);
            $code = 404;
        }


        return ['data' =>$data,'message'=>$message,'code'=>$code];


    }

    public function store($request):array
    {

        $lang = Auth::user()->preferred_language;
        $nameEn = $request['name_en'];
        $nameAr = $request['name_ar'];



        $category = Category::where('name->en', $nameEn)
                    ->orWhere('name->ar', $nameAr)
                    ->first();

        if($category)
        {

            if($category->getTranslation('name', 'en') === $nameEn && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_En_en',[],$lang);
            }
            else if ($category->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_En_ar',[],$lang);
            }

            else if($category->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_Ar_en',[],$lang);
            }

            else if($category->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_Ar_ar',[],$lang);
            }

            $data =[];
            $code = 409;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }

        $category = Category::query()->create([
            'chef_id' => Auth::user()->chef->id,
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
            ],
            "description"=>[
                'en' => $request["description_en"],
                'ar' => $request["description_ar"],
            ],

        ]);


        $category->image()->create([
            'path' => $this->uplodeImage($request['category_Image'],"categories"),
        ]);


        $data = [true];

        $message = __('message.Category_Created',[],$lang);
        $code = 201;


        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }

    public function update($request)
    {
        $lang = Auth::user()->preferred_language;

        $old_category = Category::query()->where('id',$request['category_id'])->first();

        $nameEn = $request['name_en'];
        $nameAr = $request['name_ar'];

        $name_en_old = $old_category->getTranslation('name', 'en');
        $name_ar_old = $old_category->getTranslation('name', 'ar');

        $category = Category::where(function ($query) use ($nameEn,$name_en_old) {
                $query->where('name->en', $nameEn)
                ->where('name->en','!=', $name_en_old);
        })
        ->orWhere(function ($query) use ($nameAr, $name_ar_old) {
                $query->where('name->ar', $nameAr)
                ->where('name->ar', '!=', $name_ar_old);
        })
        ->first();

        if($category)
        {

            if($category->getTranslation('name', 'en') === $nameEn && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_En_en',[],$lang);
            }
            else if ($category->getTranslation('name', 'en') === $nameEn && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_En_ar',[],$lang);
            }

            else if($category->getTranslation('name', 'ar') === $nameAr && $lang == 'en')
            {
                $message = __('message.Extra_Already_Exist_Ar_en',[],$lang);
            }

            else if($category->getTranslation('name', 'ar') === $nameAr && $lang == 'ar')
            {
                $message = __('message.Extra_Already_Exist_Ar_ar',[],$lang);
            }

            $data =[];
            $code = 409;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }

        $old_category->update([
            'name' => [
                'en' => $nameEn ?? $old_category->name->en,
                'ar' => $nameAr ?? $old_category->name->en,
                ],
            "description" => [
                'en' => $request["description_en"] ?? $old_category->description->en,
                'ar' => $request["description_ar"] ??$old_category->description->ar,
                ],
        ]);


        if (request()->hasFile('category_Image'))
        {
            if (Storage::disk('public')->exists($old_category->image->path))
            {
                Storage::disk('public')->delete($old_category->image->path);
            }

            $path = $this->uplodeImage(request()->file('category_Image'), 'categories');

            $old_category->image()->updateOrCreate([], ['path' => $path]);
        }


        $data = [true];
        $code= 200;
        $message = __('message.Category_Updated',[],$lang);

        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }

    public function delete($request)
    {
        $category = Category::query()->where('id',$request['category_id'])->first();

        $lang = Auth::user()->preferred_language;
        if($category)
        {
            if (Storage::disk('public')->exists($category->image->path))
            {
                Storage::disk('public')->delete($category->image->path);
                $category->image->delete();
            }

            $category->delete();
            $data = [true];
            $code= 200;
            $message = __('message.Category_Deleted',[],$lang);
        }
        else
        {
            $data = [];
            $message = __('message.Category_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }
}
