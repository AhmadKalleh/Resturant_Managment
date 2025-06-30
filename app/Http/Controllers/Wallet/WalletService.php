<?php

namespace App\Http\Controllers\Wallet;

use Auth;
use Hash;

class WalletService
{
    public function ChargeMywallet($request):array
    {
        $lang = Auth::user()->preferred_language;
        $wallet = Auth::user()->customer->myWallet;
        $incomingAmount = $request->has('amount') ? $request->amount : 100000;
        $newAmount = ($wallet->amount ?? 0) + $incomingAmount;
        $wallet->update(['amount' => $newAmount]);
        $data = [];
        $message = __('message.My_Wallet_Charged',[],$lang);
        $code = 200;
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }


        public function show_my_wallet($request):array
    {
        $lang = Auth::user()->preferred_language;
        $user=Auth::user();
            if(!Hash::check($request['password'], $user->password))
            {
                $data = [];
                $message = __('message.Invalid_Password',[],$lang);
                $code = 401;
                return ['data' =>$data,'message'=>$message,'code'=>$code];
            }

        $data = ["amount"=>$user->customer->myWallet->amount];
        $message = __('message.Wallet_information',[],$lang);
        $code = 200;
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }



}
