<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\ReceptionService;
use App\Http\Controllers\Reception\FormRequestReception;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ReceptionController extends Controller
{
    use ResponseHelper;
    private ReceptionService $_receptionService;


    public function __construct(ReceptionService $receptionService)
    {
        $this->_receptionService = $receptionService;
    }

        public function index ():JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_receptionService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function store (FormRequestReception $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_receptionService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }


    public function destroy (FormRequestReception $request):JsonResponse
    {

        $data=[];
        $id=$request->input("id");
        try
        {
            $data = $this->_receptionService->destroy($id);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }

    public function show (FormRequestReception $request):JsonResponse
    {
        $data=[];
        $id=$request->input("id");
        try
        {
            $data = $this->_receptionService->show($id);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }

}
