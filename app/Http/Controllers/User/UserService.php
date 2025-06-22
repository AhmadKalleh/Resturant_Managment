<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    use UplodeImageHelper;

    public function show_info():array
    {

        $user = Auth::user();
        $lang = $user->preferred_language;

        $role = $user->roles->pluck('name')->first();

        $roleData = null;

        switch ($role) {
            case 'chef':
                $roleData = $user->chef;
                break;
            case 'customer':
                $roleData = $user->customer;
                break;
            case 'reception':
                $roleData = $user->reception;
                break;
        }

        $data = [
            'id' => $user->id,
            'image_path' =>url(Storage::url($user->image->path??Image::query()->where('id',1)->first()->path)),
            'first_name' => $user->first_name,
            'last_name'=>$user->last_name,
            'mobile' =>$user->mobile,
            'email' => $user->email,
            'gendor' => $user->gendor,
            'date_of_birth' => $user->date_of_birth,
            'preferred_language' => $user->preferred_language,
            'preferred_theme' => $user->preferred_theme,
            'details' => $roleData,
        ];

        $message = __('message.Info_Profile_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function change_mobile($request):array
    {

        $lang = Auth::user()->preferred_language;
        $old_mobile = Auth::user()->mobile;

        $exist_mobile = User::query()->where('mobile','=',$request['mobile'])->where('mobile','!=',$old_mobile)->first();

        if ($exist_mobile)
        {
            $data = [];
            $message = __('message.Mobile_Already_Exist',[],$lang);
            $code = 409;

            return ['data'=>$data,'message'=>$message,'code'=>$code];
        }


        Auth::user()->update([
            'mobile' => $request['mobile']
        ]);


        $message = __('message.Mobile_Updated',[],$lang);
        $code = 200;

        $data = [true];

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function update_password($request):array
    {

        $lang = Auth::user()->preferred_language;
        Auth::user()->update([
            'password' => Hash::make($request['password'])
        ]);

        $message = __('message.Password_Changed',[],$lang);
        $code = 200;
        $data = [true];

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function update_lan($request):array
    {
        Auth::user()->update([
            'preferred_language' => $request['lan']
        ]);

        $message = __('message.Password_Changed',[],$request['lan']);
        $code = 200;
        $data = [true];

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }


    public function update_theme($request):array
    {
        $lang = Auth::user()->preferred_language;
        Auth::user()->update([
            'preferred_theme' => $request['theme']
        ]);

        $message = __('message.Theme_Changed',[],$lang);
        $code = 200;
        $data = [true];

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function check_password($request) :array
    {
        $lang = Auth::user()->preferred_language;
        if(Hash::check( $request['password'],Auth::user()->password))
        {
            $data = [true];

            $message = __('message.Correct_Password',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.In_Correct_Password',[],$lang);
            $code = 401;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function delete_account($request):array
    {
        $lang = Auth::user()->preferred_language;
        if(Hash::check($request['password'],Auth::user()->password))
        {
            Auth::user()->delete();
            $data = [];
            $message = __('message.Account_Deleted',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.In_Correct_Password',[],$lang);
            $code = 401;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function update_user_image_profile($request):array
    {

        $lang = Auth::user()->preferred_language;

        $current_image = Auth::user()->image->path??Image::query()->where('id',1)->first()->path;

        $default_image =Image::query()->where('id',1)->first()->path;



        if (Storage::disk('public')->exists($current_image)
                && $current_image != $default_image) {
            Storage::disk('public')->delete($current_image);
        }

        if($current_image === $default_image)
        {
            Auth::user()->image()->create([
                'path' => $this->uplodeImage($request->file('image'),'users')
            ]);
        }
        else
        {
            Auth::user()->image->update([
            'path' => $this->uplodeImage($request->file('image'),'users')
            ]);
        }


        $data = [true];

        $message = __('message.Profile_Image_Updated',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
}
