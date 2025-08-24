<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class ProductController extends Controller
{
    use ResponseHelper;
    private ProductService  $_productService;

    public function __construct(ProductService  $productService)
    {
        $this->_productService = $productService;
    }


    public function index(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->index($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function index_product_by_admins(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->index_product_by_admins($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function top_ratings():JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->top_ratings();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function searchByCategory(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->searchByCategory($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function search(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->search($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function filter(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->filter($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(FormRequestProduct $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_productService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(FormRequestProduct $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_productService->show($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_product_by_chef(FormRequestProduct $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_productService->show_product_by_chef($request);
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
    public function update(FormRequestProduct $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_productService->update($request);
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
    public function destroy(FormRequestProduct $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_productService->destroy($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
