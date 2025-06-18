<?php

namespace App\Http\Controllers\Offer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class OfferController extends Controller
{
    use ResponseHelper;

    private OfferService $_offerService;

    public function __construct(OfferService $offerService)
    {
        $this->_offerService = $offerService;
    }

    public function index():JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_offerService->index();
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
    public function store(FormRequestOffer $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_offerService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
    public function special_offers(FormRequestOffer $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_offerService->special_offers($request);
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
    public function show(FormRequestOffer $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_offerService->show($request);
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
    public function update(FormRequestOffer $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_offerService->update($request);
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
    public function destroy(FormRequestOffer $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_offerService->destroy($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
