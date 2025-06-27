<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cart\CartService;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class CartController extends Controller
{
    use ResponseHelper;

    private CartService $_cartService;

    public function __construct(CartService $cartService)
    {
        $this->_cartService = $cartService;
    }

    public function index():JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_own_extra_for_product(FormRequestCart $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->show_own_extra_for_product($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function store(FormRequestCart $request): JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function update_quantity(FormRequestCart $request): JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->update_quantity($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function update_cart_item(FormRequestCart $request): JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->update_cart_item($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function destroy_extra(FormRequestCart $request): JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->destroy_extra($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function destroy(FormRequestCart $request): JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_cartService->destroy($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
