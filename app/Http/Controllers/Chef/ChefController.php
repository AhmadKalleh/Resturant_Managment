<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class ChefController extends Controller
{
    use ResponseHelper;

    private ChefService $_chefService;

    public function __construct(ChefService $chefService)
    {
        $this->_chefService = $chefService;
    }

    public function index(): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chefService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show(FormRequestChef $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chefService->show($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function store(FormRequestChef $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chefService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function delete(FormRequestChef $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chefService->delete($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function transfer_ownership(FormRequestChef $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chefService->transfer_ownership($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

}
