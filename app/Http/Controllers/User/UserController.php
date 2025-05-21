<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{
    use ResponseHelper;

    private UserService $_userService;

    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
    }

    public function show():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_userService->show_info();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function change_mobile(FormRequestUser $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_userService->change_mobile($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function update_image_profile(FormRequestUser $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_userService->update_user_image_profile($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function check_password(FormRequestUser $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_userService->check_password($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }


    public function update_password(FormRequestUser $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_userService->update_password($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormRequestUser $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_userService->delete_account($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
