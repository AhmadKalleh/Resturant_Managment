<?php

namespace App\Http\Controllers\Reservation;

use App\Jobs\AutoCancelExpiredReservations;
use App\Models\Reservation;
use Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationService
{


    public function index():array
    {
        $lang = Auth::user()->preferred_language;

        $now = now()->addHours(3);

        $previous_reservations = Reservation::with('table')
            ->where('customer_id', Auth::user()->customer->id)
            ->where('reservation_end_time', '<', $now)
            ->orWhere('is_canceled','=',true)
            ->orWhere('is_checked_in',true)
            ->orderby('reservation_end_time', 'desc')
            ->get()
            ->map(function ($reservation) use ($lang)
            {
                return [
                    'reservation_id' => $reservation->id,
                    'table_id' => $reservation->table_id,
                    'price_table' => $reservation->table->price,
                    'loaction_table' => $reservation->table->getTranslation('location',$lang),
                    'reservation_start_time' => $reservation->reservation_start_time,
                    'reservation_end_time' => $reservation->reservation_end_time,
                    'is_checked_in' => $reservation->is_checked_in,
                ];
            });

            $next_reservations = Reservation::with('table')
            ->where('customer_id', Auth::user()->customer->id)
            ->where('is_canceled', false)
            ->where('is_checked_in', false)
            ->where('reservation_end_time', '>=', $now)
            ->orderBy('reservation_start_time', 'asc')
            ->get()
            ->map(function ($reservation) use ($lang) {
                $now = now()->addHours(3); // هذه لأجل الحساب فقط إن كنت تعمل بتوقيت آخر
                $start_time = Carbon::parse($reservation->reservation_start_time);
                $start_time2 = $start_time->copy()->addMinutes(31);

                $is_cancelable = ($now >= $start_time && $now <= $start_time2);

                return [
                    'reservation_id' => $reservation->id,
                    'table_id' => $reservation->table_id,
                    'price_table' => $reservation->table->price,
                    'loaction_table' => $reservation->table->getTranslation('location', $lang),
                    'reservation_start_time' => $reservation->reservation_start_time,
                    'reservation_end_time' => $reservation->reservation_end_time,
                    'is_checked_in' => $reservation->is_checked_in,
                    'is_cancelable' => $is_cancelable,
                ];
            });







        $data = [
            'next_res' => $next_reservations,
            'previous_res' => $previous_reservations,
        ];

        $message = __('message.Reservation_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];


    }


    public function show_all_reservation_for_table($request):array
    {
        $lang = Auth::user()->preferred_language;

        $reservations = Reservation::query()
        ->where('table_id','=',$request['table_id'])
        ->where('reservation_end_time','>=', Carbon::now())
        ->orderby('created_at','ASC')
        ->get()
            ->map(function ($reservation)
            {
                return [
                    'reservation_id' => $reservation->id,
                    'reservation_start_time' => $reservation->reservation_start_time,
                    'reservation_end_time' => $reservation->reservation_end_time,
                    'is_checked_in' => $reservation->is_checked_in,
                ];
        });

        if($reservations->isEmpty())
        {
            $data = [];
            $message = __('message.No_Reservation_For_This_Table',[],$lang);
            $code = 200;
        }
        else
        {
            $data = $reservations;
            $message = __('message.Reservation_Retrived',[],$lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }


    public function create_reservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        if (Auth::user()->customer->block_reservation)
        {
            $blockedUntil = Auth::user()->customer->blocked_until;

            $date = Carbon::parse($blockedUntil)
                    ->locale($lang)
                    ->isoFormat('D MMMM YYYY');

            $data = [];
            $message = __('message.blocked_reservation_until', ['date' => $date], $lang);
            $code = 400;

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }


        $exist_reservation = Reservation::query()
        ->where('is_canceled','=',false)
        ->where('table_id', $request['table_id'])
        ->where(function ($query) use ($request) {
            $query->where('reservation_start_time', '<', $request['reservation_end_time'])
                ->where('reservation_end_time', '>', $request['reservation_start_time']);
        })
        ->exists(); // or ->first()


        if(!$exist_reservation)
        {
            $new_reservation = Auth::user()->customer->reservations()->create([
                'table_id' => $request['table_id'],
                'reservation_start_time' => $request['reservation_start_time'],
                'reservation_end_time' => $request['reservation_end_time'],
                'is_checked_in' => false
            ]);



            $data = [true];
            $message = __('message.Reservation_Created',[],$lang);
            $code = 201;
        }
        else
        {
            $data = [false];
            $message = __('message.Reservation_Conflict', [], $lang); // تأكد من وجود هذا النص في ملف الترجمة
            $code = 409; // Conflict
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function cancel_reservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        $reservation = Reservation::query()->where('id','=',$request['reservation_id'])->first();

        if($reservation)
        {
            $reservation->update([
                'is_canceled' => true,
                'canceled_by' => 'customer'
            ]);

            $data = [true];
            $message = __('message.Reservation_Canceled', [], $lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function check_in_reservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        $reservation = Reservation::query()->where('id','=',$request['reservation_id'])->first();

        if($reservation)
        {
            $reservation->update([
                'is_checked_in' => true,
            ]);

            $data = [true];
            $message = __('message.Checked_In_Successful', [], $lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

}
