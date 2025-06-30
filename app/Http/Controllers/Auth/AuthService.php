<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\TranslateHelper\TranslateHelper;
use App\Jobs\DeletePendingUsersJob;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TWILIO_SERVICE\TwilioService;
use App\Mail\SendVerificationCodeMail;
use App\Models\Customer;
use App\Models\ForgetPassword;
use App\Models\UserStripeData;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role as ModelsRole;

class AuthService
{

    function generateUniqueVerificationCode()
    {
        do {
            $code = random_int(100000, 999999); // توليد رقم عشوائي
        } while (PendingUser::where('verfication_code', $code)->exists()); // التحقق من التكرار


        return $code;
    }

    public function register_pendding_user($request):array
    {

        // create a new user with the specified password and password hash and password confirmation code and
        $verificationCode = $this->generateUniqueVerificationCode();

        $lang = $request['preferred_language'] ? $request['preferred_language']:'en';
        app()->setLocale($lang);


        $pendingUser = PendingUser::create([
            "first_name"=>$request['first_name'],
            "last_name"=>$request['last_name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'mobile' => $request['mobile'],
            'gendor'=> $request['gendor'],
            'date_of_birth'=> $request['date_of_birth'],
            'preferred_language'=> $request['preferred_language']??'en',
            'preferred_theme'=> $request['preferred_theme']??'light',
            'verfication_code' => $verificationCode,
        ]);


        DeletePendingUsersJob::dispatch()->delay(now()->addMinutes(10));
        Mail::to($pendingUser->email)->send(new SendVerificationCodeMail($verificationCode));

        $data = ['success_register' => true];

        $message = __('message.Verification_code',[],$lang);
        $code = 200;

        // Send the token to the client and send it to the server with the authorization
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }


    public function register_user($request):array
    {
        // create a new instance of User object with the specified permissions

        $pendingUser = PendingUser::where('verfication_code', $request['verification_code'])->first();
        $lang = app()->getLocale();

        if (!$pendingUser) {
            return [
                'data' => [],
                'message' => __('message.Invalid_Code',[],$lang),
                'code' => 400
            ];
        }

        $user = User::create([
            "first_name"=>$pendingUser->first_name,
            "last_name"=>$pendingUser->last_name,
            'email' => $pendingUser->email,
            'password' => $pendingUser->password,
            'mobile' => $pendingUser->mobile,
            'gendor'=> $pendingUser->gendor,
            'date_of_birth'=> $pendingUser->date_of_birth,
            'preferred_language'=> $pendingUser->preferred_language,
            'preferred_theme'=> $pendingUser->preferred_theme,
            'fcm_token' => $request['fcm_Token']??'',
        ]);

        $user->customer()->create([
            'person_height' => 0,
            'person_weight' => 0,
        ]);
        $user->customer->mywallet()->create([
        'customer_id' =>$user->customer->id
        ]);

        // Assigning the client role to the user and giving the user all permissions of the client role

        $customerRole = ModelsRole::query()->where('name', '=', 'customer')->first();
        $user->assignRole($customerRole);

        $permissions = $customerRole->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);






        // Creating a token for the user and sending it as a response
        $token = $user->createToken("api_token")->plainTextToken;


        $pendingUser->delete();

        // Send the token to the client and send it to the server with the authorization information in the response object and the user
        $data = [
            'lan' => $user->preferred_language,
            'theme' => $user->preferred_theme,
            'id' => $user->customer->id,
            'token'=>$token
        ];

        $message = __('message.Sign_up_successfully',[],$lang);
        $code = 201;

        // Send the token to the client and send it to the server with the authorization
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }


    public function login($request):array
    {

        $user = User::query()->where('email',$request['email'])->first();


        if(!is_null($user))
        {
            $lang = $user->preferred_language;
            if(!Hash::check($request['password'], $user->password))
            {
                $data = [];
                $message = __('message.Invalid_Password',[],$lang);
                $code = 401;
            }
            else
            {
                $token = $user->createToken("api_token")->plainTextToken;
                $user->update(['fcm_token' => $request['fcm_Token']??'']);

                $data = [
                    'id' => $user->id,
                    'token'=>$token
                ];
                $code = 200;
                $message = __('message.Login_successfully',[],$lang);
            }

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }
        else
        {

            $data = [];
            $message  = __('message.Account_Not_Found',[],'en');
            $code = 404;
            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }


    }

    public function logout():array
    {
        $user = Auth::user();


        $lang = $user->preferred_language;

        if(!is_null($user))
        {
            $token = $user->currentAccessToken();
            /** @var PersonalAccessToken|null $token */
            $token?->delete();
            $data = [];
            $message = __('message.Logged_successfully',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Invalid_Token',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }


    public function send_varification_code_to_email($request):array
    {
        $user = User::query()->where('email',$request['email'])->first();

        if(!is_null($user))
        {
            $verificationCode = $this->generateUniqueVerificationCode();

            Mail::to($user->email)->send(new SendVerificationCodeMail($verificationCode));
            ForgetPassword::query()->create([
                'email' => $user->email,
                'verfication_code' => $verificationCode
            ]);
            $data = [true];

            $message = __('message.Verification_code',[],$user->preferred_language);
            $code = 200;

            // Send the token to the client and send it to the server with the authorization
            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }
        else
        {
            $data = [];
            $message  = __('message.Account_Not_Found',[],'en');
            $code = 404;
            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }
    }

    public function is_varification_code_right($request):array
    {
        $user = ForgetPassword::query()->where('verfication_code',$request['verfication_code'])->first();

        if(!is_null($user))
        {
            $user->delete();
            $data = [true];
            $message = __('Insert_New_Password',[],'en');
            $code = 200;
            return ['data'=>$data,'message'=>$message,'code'=>$code];
        }
        else
        {
            return [
                'data' => [],
                'message' => __('message.Invalid_Code',[],'en'),
                'code' => 400
            ];
        }

    }


    public function reset_password($request):array
    {
        $user = User::query()->where('email',$request['email'])->first();

        if(!is_null($user))
        {
            $user->update(['password'=> Hash::make($request['password'])]);

            $message = __('message.Password_Changed',[],$user->preferred_language);
            $code = 200;
            $data = [true];
        }
        else
        {
            $data = [];
            $message  = __('message.Account_Not_Found',[],'en');
            $code = 404;
        }


        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }




}
