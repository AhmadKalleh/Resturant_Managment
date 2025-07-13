<?php

namespace App\Http\Controllers\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function get_payments():array
    {
        $lang = Auth::user()->preferred_language;
        $payments = Order::query()->with(['reservation.table','payment'])
        ->where('customer_id','=',Auth::user()->customer->id)
        ->get()
        ->map(function($order){
            return [
                'payment_id'=>$order->payment->id,
                'order_id' =>$order->id,
                'order_price' => $order->total_amount_text,
                'reservation_price' => $order->reservation->table->price_text,
                'total_bill' => $order->payment->amount_text
            ];
        });

        if(is_null($payments))
        {
            $data = [];
            $message = __('message.Payments_Empty',[],$lang);
            $code = 200;
        }
        else
        {
            $data = $payments;
            $message = __('message.Payments_Retrived',[],$lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
}
