<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class OrderController extends Controller
{
    use ResponseHelper;

    private OrderService $_orderService;

    public function __construct(OrderService $orderService)
    {
        $this->_orderService = $orderService;
    }

    public function index_pre_orders():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->index_pre_orders();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_pre_order(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->show_pre_order($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function create_pre_order(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->create_pre_order($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function create_order_now(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->create_order_now($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
