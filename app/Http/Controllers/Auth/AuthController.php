<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use App\Http\Controllers\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Throwable;

class AuthController extends Controller
{

    use ResponseHelper;

    private AuthService $_authService;

    public function __construct(AuthService $authService)
    {
        $this->_authService = $authService;
    }

    public function register_pendding_user(FormRequestAuth $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_authService->register_pendding_user($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function register(FormRequestAuth $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_authService->register_user($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function login(FormRequestAuth $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_authService->login($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function logout():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_authService->logout();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

}
