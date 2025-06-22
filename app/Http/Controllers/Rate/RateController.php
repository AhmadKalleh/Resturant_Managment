<?php

namespace App\Http\Controllers\Rate;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class RateController extends Controller
{
    use ResponseHelper;

    private RateService $_rateService;

    public function __construct(RateService $rateService)
    {
        $this->_rateService = $rateService;
    }

    public function rate_product(FormRequestRate $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_rateService->rate_product($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
