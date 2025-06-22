<?php

namespace App\Http\Controllers\Extra_product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class ExtraProductController extends Controller
{
    use ResponseHelper;

    private ExtraProductService $_extraProductService;

    public function __construct(ExtraProductService $extraProductService)
    {
        $this->_extraProductService = $extraProductService;
    }

    public function show_extra_product_details(FormRequestExtraProduct $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_extraProductService->show_extra_product_details($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function store_extra_product(FormRequestExtraProduct $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_extraProductService->store_extra_product($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function delete_extra_product(FormRequestExtraProduct $request): JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_extraProductService->delete_extra_product($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
