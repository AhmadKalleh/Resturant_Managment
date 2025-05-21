<?php

namespace App\Http\Controllers\Extra;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class ExtraControlle extends Controller
{

    use ResponseHelper;

    private ExtraService $_extraService;

    public function __construct(ExtraService $extraService)
    {
        $this->_extraService = $extraService;
    }

    public function index():JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_extraService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show(FormRequestExtra $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_extraService->show($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function store(FormRequestExtra $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_extraService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function update(FormRequestExtra $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_extraService->update($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function destroy(FormRequestExtra $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_extraService->delete($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
