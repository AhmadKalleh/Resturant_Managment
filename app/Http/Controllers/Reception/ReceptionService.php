<?php
namespace App\Http\Controllers\Reception;

use App\Http\Controllers\TranslateHelper\TranslateHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Reception;
use App\Models\Table;
use App\Models\User;
use Auth;
use Hash;
use Spatie\Permission\Models\Role;
use Storage;
class ReceptionService
{
    use UplodeImageHelper;
    use TranslateHelper;

    public function index():array
    {
        $lang = Auth::user()->preferred_language;
        $receptions= Reception::with('image')->with('user')->get();
        if (!$receptions)
        {
            return [
                'data' => [],
                'message' => __('message.Reception_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        $data = [];
        foreach ($receptions as $reception)
        {
            $data[] = [
                'reception_id'=> $reception['id'],
                'years_of_experience'=> $reception->YearsOfExperienceText,
                'shift'=> $this->translate('shift',$reception['shift']),
                'image_path' => $reception->user->image ? url('storage/' . $reception->user->image->path) : url('storage/' . 'users/profile-user.png') ,
                'user_id' => $reception['user_id'],
                'first_name'=>$reception->user['first_name'],
                'last_name'=>$reception->user['last_name'],
                'full_name' => $reception->user->full_name,
                'email'=>$reception->user['email'],
                'mobile'=>$reception->user->mobile_text,
                'gendor'=>$this->translate('gendor',$reception->user['gendor']),
                'date_of_birth'=>$reception->user['date_of_birth'],

            ];
        }

        $message = __('message.Reception_Retrieved',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function show ( $id )
    {

        $reception =Reception::With('user')->with('image')->find($id);
        $lang = Auth::user()->preferred_language;

        if (!$reception)
        {
            return [
                'data' => [],
                'message' => __('message.Reception_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        $data =
        [
                'reception_id'=> $reception['id'],
                'years_of_experience'=> $reception->YearsOfExperienceText,
                'shift'=> $this->translate('shift',$reception['shift']),
                'image_path' => $reception->user->image ? url('storage/' . $reception->user->image->path) : url('storage/' . 'users/profile-user.png') ,
                'user_id' => $reception['user_id'],
                'first_name'=>$reception->user['first_name'],
                'last_name'=>$reception->user['last_name'],
                'full_name' => $reception->user->full_name,
                'email'=>$reception->user['email'],
                'mobile'=>$reception->user->mobile_text,
                'gendor'=>$this->translate('gendor',$reception->user['gendor']),
                'date_of_birth'=>$reception->user['date_of_birth'],
        ];


        return [
            'data' => [$data],
            'message' => __('message.Reception_Retrieved', [], $lang),
            'code' => 200,
        ];
    }


    public function store($request):array
    {
        $lang = Auth::user()->preferred_language;
        $reception = User::query()->create([
            'first_name' => $request['first_name'],
            'last_name'=>$request['last_name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'mobile' => $request['mobile'],
            'gendor' =>$request['gendor'],
            'date_of_birth' =>$request['date_of_birth'],

        ]);
        $reception->reception()->create([
            'shift' =>$request['shift'],
            'years_of_experience' => $request['years_of_experience'],
        ]);
        $reception_role = Role::firstOrCreate(['name' => 'reception','guard_name' => 'web']);
        $reception->assignRole($reception_role);
        $permissions = $reception_role->permissions()->pluck('name')->toArray();
        $reception->givePermissionTo($permissions);

        $data = [];
        $message = __('message.Reception_Created',[],$lang);
        $code = 200;
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }



    public function destroy($id)
    {
        $lang = Auth::user()->preferred_language;

        $reception = Reception::query()->where('id', $id)->first();
        if (!$reception)
        {
            return [
                'data' => [],
                'message' => __('message.Reception_Not_Found', [], $lang),
                'code' => 404,
            ];
        }

        if ($reception->user->image&&$reception->user->image->id!=1)
        {
            Storage::disk('public')->delete($reception->user->image->path);
            $reception->user->image()->delete();
        }

        $reception->user->delete();
        return [
            'data' => [],
            'message' => __('message.Reception_Deleted', [], $lang),
            'code' => 200,
        ];
    }



}
