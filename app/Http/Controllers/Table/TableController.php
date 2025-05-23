<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Table\TableService;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TableController extends Controller
{
    use ResponseHelper;
    private TableService $_tableService;


    public function __construct(TableService $tableService)
    {
        $this->_tableService = $tableService;
    }


    public function index ():JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_tableService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function store (FormRequestTable $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_tableService->store($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }


    public function update (FormRequestTable $request):JsonResponse
    {

        $data=[];

        try
        {
            $id = $request->input('id');
            $data = $this->_tableService->update($request->validated(),$id);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }


    public function destroy (Request $request):JsonResponse
    {

        $data=[];
        $id=$request->input("id");
        try
        {
            $data = $this->_tableService->destroy($id);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }

    
    public function show (Request $request):JsonResponse
    {

        $data=[];
        $id=$request->input("id");
        try
        {
            $data = $this->_tableService->show($id);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }

}
