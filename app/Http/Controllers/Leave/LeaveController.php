<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class LeaveController extends Controller
{
    use ResponseHelper;

    private LeaveService $_leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->_leaveService = $leaveService;
    }

    public function index_leaves():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_leaveService->index_leaves();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function get_my_leaves():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_leaveService->get_my_leaves();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function create_leave(FormRequestLeave $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_leaveService->create_leave($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function approve_leave(FormRequestLeave $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_leaveService->approve_leave($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function reject_leave(FormRequestLeave $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_leaveService->reject_leave($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
