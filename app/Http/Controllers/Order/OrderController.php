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

    public function index_now_orders():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->index_now_orders();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function index_completed_orders():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->index_completed_orders();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_now_order_for_chef():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->show_now_order_for_chef();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_pre_order_for_chef():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->show_pre_order_for_chef();
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

    public function mark_cart_item_ready(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->mark_cart_item_ready($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_now_order(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->show_now_order($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_completed_orders(FormRequestOrder $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_orderService->show_completed_orders($request);
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
