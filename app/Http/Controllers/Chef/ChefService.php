<?php

namespace App\Http\Controllers\Chef;

use App\Models\Chef;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ChefService
{
    public function index():array
    {
        $lang = Auth::user()->preferred_language;
        $chefs = Chef::query()->with('user')
        ->get()
        ->map(function($chef) use ($lang){
            return [
                'chef_id' => $chef->id,
                'speciality' => $chef->getTranslation('speciality', $lang),
                'years_of_experience' => $chef->years_of_experience,
                'bio' => $chef->bio,
                'certificates' =>json_decode($chef->certificates)
            ];
        });


        if($chefs->isEmpty())
        {
            $data = [];
            $message = __('message.Chefs_Not_Found',[],$lang);
            $code = 200;
        }
        else
        {
            $data = $chefs;
            $message = __('message.chefs_Retrived',[],$lang);
            $code = 200;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }


    public function show($request):array
    {
        $lang = Auth::user()->preferred_language;

        $chef = Chef::query()->where('id',$request['chef_id'])->first();

        if(is_null($chef))
        {
            $data = [];
            $message = __('message.Chef_Not_Found',[],$lang);
            $code = 200;
        }
        else
        {
            $user = $chef->user;
            $data = [
                'first_name' => $user->first_name,
                'last_name'=> $user->last_name,
                'email'=> $user->email,
                'mobile' => $user->mobile_text,
                'gendor' => $user->gendor,
                'date_of_birth' => $user->date_of_birth,
                'speciality' => $chef->getTranslation('speciality', $lang),
                'years_of_experience' => $chef->years_of_experience,
                'bio' => $chef->bio,
                'certificates' =>json_decode($chef->certificates),
                'image_path' =>url(Storage::url($user->image->path??Image::query()->where('id',1)->first()->path)),
            ];
            $message = __('message.chef_Retrived',[],$lang);
            $code = 200;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }

    private function canBeDeleted(Chef $chef): array
    {
        $lang = Auth::user()->preferred_language;
        $relations = [];

        if ($chef->categories()->exists()) {
            $relations[] = $lang=='en'?'categories' :'فئات';
        }

        if ($chef->products()->exists()) {
            $relations[] = $lang=='en'?'products':'منتجات';
        }

        if ($chef->extras()->exists()) {
            $relations[] = $lang=='en'?'extras':'اضافات للمنتجات';
        }

        if ($chef->offers()->exists()) {
            $relations[] = $lang=='en'?'offers':'عروض';
        }

        return $relations;
    }


    public function store($request):array
    {
        $lang = Auth::user()->preferred_language;
        $exist_user = User::query()->where('email',$request->email)->orWhere('mobile',$request['mobile'])->first();

        if ($exist_user)
        {
            $data = [];
            $message = __('message.Mobile_Or_Email_Already_Exist',[],$lang);
            $code = 400;
        }
        else
        {
            $user = User::query()->create(
            [
                'first_name' => $request['first_name'],
                'last_name'=>$request['last_name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'mobile' => $request['mobile'],
                'gendor' =>$request['gendor'],
                'date_of_birth' =>$request['date_of_birth'],
                'preferred_language' =>$request['preferred_language'] ?? 'en',
                'preferred_theme' =>$request['preferred_theme'] ?? 'light',
            ]);


            $user->chef()->create([
                'speciality' => ['en' => $request['speciality_en'],'ar' => $request['speciality_ar'],],
                'years_of_experience' => $request['years_of_experience'],
                'bio' => $request['bio'] ?? '',
                'certificates' => json_encode($request['certificates'])
            ]);


            $data = [true];
            $message = __('message.Chef_Info_Added',[],$lang);
            $code = 201;
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];
    }

    public function transfer_ownership($request): array
    {

        $lang = Auth::user()->preferred_language;
        $fromChef = Chef::with('user')->find($request['from_chef_id']);
        $toChef = Chef::query()->where('id',$request['to_chef_id'])->first();

        $blockedRelations = $this->canBeDeleted($fromChef);

        if (empty($blockedRelations))
        {
            $data = [];
            $message = __('message.Chef_Has_No_Relations',[], $lang);
            $code = 400;

            return ['data'=>$data,'message'=>$message,'code'=>$code];
        }

        if($fromChef && $toChef)
        {
            $fromChef->categories()->update(['chef_id' => $toChef->id]);

            $fromChef->products()->update(['chef_id' => $toChef->id]);

            $fromChef->extras()->update(['chef_id' => $toChef->id]);

            $fromChef->offers()->update(['created_by'=> $toChef->id]);
        }

        $data = [true];
        $message = __('message.Ownership_transfered',['chef_name' => $fromChef->user->full_name],$lang);
        $code = 200;

        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }


    public function delete($request):array
    {
        $lang = Auth::user()->preferred_language;

        $chef = Chef::query()->where('id',$request['chef_id'])->first();

        if(is_null($chef))
        {
            $data = [];
            $message = __('message.Chef_Not_Found',[],$lang);
            $code = 200;
        }
        else
        {
            $blockedRelations = $this->canBeDeleted($chef);

            if (!empty($blockedRelations))
            {
                $data = [];
                $message = __('message.Chef_Has_Relations', ['relations' => implode(', ', $blockedRelations)], $lang);
                $code = 200;
            }
            else
            {
                $user = $chef->user;
                $user->delete();
                $data = [];
                $message = __('message.Chef_Deleted', [],$lang);
                $code = 200;
            }
        }

        return ['data'=>$data,'message'=>$message,'code'=>$code];

    }



}
