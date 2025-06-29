<?php

namespace App\Http\Controllers\Wallet;

use Auth;

class WalletService
{
    public function store($request):array
    {
        $lang = Auth::user()->preferred_language;
        $my_wallet= Auth::user()->customer->myWallet()-> create([
            'customer_id' =>Auth::user()->customer->id ,
            'card_number'=>$request['card_number'],
            'cvc' => $request['cvc'],
            'amount' =>$request['amount']??100000,
            'email' =>$request['email'],
        ]);


        $data = [];
        $message = __('message.My_Wallet_Created',[],$lang);
        $code = 200;
        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

}
