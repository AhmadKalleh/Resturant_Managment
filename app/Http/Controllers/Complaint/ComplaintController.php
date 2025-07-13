<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class ComplaintController extends Controller
{
    use ResponseHelper;

    private ComplaintService $_complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->_complaintService = $complaintService;
    }

    public function index_complaints():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_complaintService->index_complaints();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function create_complaint(FormRequestComplaint $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_complaintService->create_complaint($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function resolve_complaint(FormRequestComplaint $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_complaintService->resolve_complaint($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function dismiss_complaint(FormRequestComplaint $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_complaintService->dismiss_complaint($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
