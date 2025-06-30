<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WalletController extends Controller
{
    use ResponseHelper;

    private WalletService $_walletService;

    public function __construct(WalletService $walletService)
    {
        $this->_walletService = $walletService;
    }
    public function ChargeMywallet (FormRequestWallet $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_walletService->ChargeMywallet($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }


}

    public function show_my_wallet (FormRequestWallet $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_walletService->show_my_wallet($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }

    }
}
