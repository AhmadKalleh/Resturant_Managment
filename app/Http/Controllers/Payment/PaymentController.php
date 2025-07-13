<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaymentController extends Controller
{
    use ResponseHelper;

    private PaymentService $_paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->_paymentService = $paymentService;
    }

    public function get_payments():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_paymentService->get_payments();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
