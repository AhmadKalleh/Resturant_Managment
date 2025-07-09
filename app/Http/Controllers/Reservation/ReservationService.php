<?php

namespace App\Http\Controllers\Reservation;

use App\Jobs\AutoCancelExpiredReservations;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\ReservationExtension;
use App\Models\Table;
use Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationService
{

    public function get_upcoming_and_current_reservations():array
    {
        $lang = Auth::user()->preferred_language;

        $now = now()->addHours(3);

        $reservations = Reservation::with(['table', 'extensions'])
        ->where('customer_id', Auth::user()->customer->id)
        ->where('is_canceled', false)
        ->where(function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                // حجز حالي
                $q->where('reservation_start_time', '<=', $now)
                ->where('reservation_end_time', '>=', $now);
            })
            ->orWhere(function ($q) use ($now) {
                // حجز قادم
                $q->where('reservation_start_time', '>', $now)
                ->where('is_checked_in', false);
            });
        })
        ->orderBy('reservation_start_time', 'asc')
        ->get()
        ->map(function ($reservation) use ($lang, $now) {
            $now = now()->addHours(3); // للتوقيت المحلي
            $cancel_limit_time = Carbon::parse($reservation->reservation_start_time)->addMinutes(30);

            $is_cancelable = ($now <= $cancel_limit_time);
            $is_extendalbe = !$reservation->is_extended &&
                    ($now <= $reservation->reservation_end_time);


            return [
                'reservation_id'         => $reservation->id,
                'table_id'               => $reservation->table_id,
                'price_table'            => $reservation->table->price,
                'loaction_table'         => $reservation->table->getTranslation('location', $lang),
                'reservation_start_time' => $reservation->reservation_start_time->toDateTimeString(),
                'reservation_end_time'   => $reservation->reservation_end_time->toDateTimeString(),
                'is_checked_in'          => $reservation->is_checked_in,
                'is_cancelable'          => $is_cancelable,
                'is_extendalbe'          => $is_extendalbe,
            ];
        });

        $data = [
            'reservations' => $reservations,
        ];

        $message = __('message.Reservation_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }



    public function index():array
    {
        $lang = Auth::user()->preferred_language;

        $now = now()->addHours(3);

        $previous_reservations = Reservation::with(['table', 'extensions'])
        ->where('customer_id', Auth::user()->customer->id)
        ->where(function ($query) use ($now) {
            $query->where('reservation_end_time', '<', $now)
                ->orWhere('is_canceled', true)
                ->orWhere(function ($q) use ($now) {
                    $q->where('is_checked_in', true)
                    ->where('reservation_end_time', '<', $now);
                });
        })
        ->orderBy('reservation_end_time', 'desc')
        ->get()
        ->map(function ($reservation) use ($lang) {
            return [
                'reservation_id'           => $reservation->id,
                'table_id'                 => $reservation->table_id,
                'price_table'              => $reservation->table->price,
                'loaction_table'           => $reservation->table->getTranslation('location', $lang),
                'reservation_start_time'   => $reservation->reservation_start_time->toDateTimeString(),
                'reservation_end_time'     => $reservation->reservation_end_time->toDateTimeString(),
                'is_checked_in'            => $reservation->is_checked_in,

                'extension_count' => $reservation->extensions->count(),

                'extensions' => $reservation->extensions->map(function ($extension) use ($lang) {
                    $start = Carbon::parse($extension->extended_start);
                    $end = Carbon::parse($extension->extended_until);

                    if ($lang === 'ar') {
                        $formatted = 'تم التمديد من ' . $start->translatedFormat('g:i A') . ' إلى ' . $end->translatedFormat('g:i A');
                    } else {
                        $formatted = 'Extended from ' . $start->format('g:i A') . ' to ' . $end->format('g:i A');
                    }

                    return [
                        'extended_start' => $extension->extended_start,
                        'extended_until' => $extension->extended_until,
                        'formatted'      => $formatted,
                    ];
                }),
            ];
        });


            $current_reservations = Reservation::with('table')
            ->where('customer_id', Auth::user()->customer->id)
            ->where(function ($query) use ($now) {
                $query->where('reservation_start_time', '<=', $now)
                    ->where('reservation_end_time', '>=', $now);
            })
            ->where('is_canceled', false)
            ->orderBy('reservation_end_time', 'desc')
            ->get()
            ->map(function ($reservation) use ($lang) {
                $now = now()->addHours(3);
                $is_extendalbe = !$reservation->is_extended &&
                        ($now <= $reservation->reservation_end_time);

                return [
                    'reservation_id'       => $reservation->id,
                    'table_id'             => $reservation->table_id,
                    'price_table'          => $reservation->table->price,
                    'loaction_table'       => $reservation->table->getTranslation('location', $lang),
                    'reservation_start_time' => $reservation->reservation_start_time->toDateTimeString(),
                    'reservation_end_time'   => $reservation->reservation_end_time->toDateTimeString(),
                    'is_checked_in'        => $reservation->is_checked_in,
                    'is_delay_extendalbe' => $is_extendalbe,
                    'extension_count' => $reservation->extensions->count(),

                    'extensions' => $reservation->extensions->map(function ($extension) use ($lang) {
                        $start = Carbon::parse($extension->extended_start);
                        $end = Carbon::parse($extension->extended_until);

                        if ($lang === 'ar') {
                            $formatted = 'تم التمديد من ' . $start->translatedFormat('g:i A') . ' إلى ' . $end->translatedFormat('g:i A');
                        } else {
                            $formatted = 'Extended from ' . $start->format('g:i A') . ' to ' . $end->format('g:i A');
                        }

                        return [
                            'extended_start' => $extension->extended_start,
                            'extended_until' => $extension->extended_until,
                            'formatted'      => $formatted,
                        ];
                    }),

                ];
            });



            $next_reservations = Reservation::with('table')
            ->where('customer_id', Auth::user()->customer->id)
            ->where('is_canceled', false)
            ->where('is_checked_in', false)
            ->where('reservation_start_time', '>', $now)
            ->orderBy('reservation_start_time', 'asc')
            ->get()
            ->map(function ($reservation) use ($lang) {
                $now = now()->addHours(3); // للتوقيت المحلي
                $cancel_limit_time = Carbon::parse($reservation->reservation_start_time)->addMinutes(30);

                $is_cancelable = ($now <= $cancel_limit_time);
                $is_extendalbe = !$reservation->is_extended &&
                                ($now <= $reservation->reservation_end_time);

                return [
                    'reservation_id' => $reservation->id,
                    'table_id' => $reservation->table_id,
                    'price_table' => $reservation->table->price,
                    'loaction_table' => $reservation->table->getTranslation('location', $lang),
                    'reservation_start_time' => $reservation->reservation_start_time->toDateTimeString(),
                    'reservation_end_time' => $reservation->reservation_end_time->toDateTimeString(),
                    'is_checked_in' => $reservation->is_checked_in,
                    'is_cancelable' => $is_cancelable,
                    'is_delay_extendalbe' => $is_extendalbe,
                    'extensions' => $reservation->extensions->map(function ($extension) use ($lang) {
                        $start = Carbon::parse($extension->extended_start);
                        $end = Carbon::parse($extension->extended_until);

                        if ($lang === 'ar') {
                            $formatted = 'تم التمديد من ' . $start->translatedFormat('g:i A') . ' إلى ' . $end->translatedFormat('g:i A');
                        } else {
                            $formatted = 'Extended from ' . $start->format('g:i A') . ' to ' . $end->format('g:i A');
                        }

                        return [
                            'extended_start' => $extension->extended_start,
                            'extended_until' => $extension->extended_until,
                            'formatted'      => $formatted,
                        ];
                    }),
                ];
            });


        $data = [
            'next_reservations' => $next_reservations,
            'current_reservations' => $current_reservations,
            'previous_reservations' => $previous_reservations,
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
                    'reservation_start_time' => $reservation->reservation_start_time->toDateTimeString(),
                    'reservation_end_time' => $reservation->reservation_end_time->toDateTimeString(),
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

    public function get_nearest_reservation_info($request)
    {
        $now = now();
        $lang = Auth::user()->preferred_language;

        // جلب أقرب حجز حالي أو قادم للزبون الحالي
        $myReservation = Reservation::where('id', $request['reservation_id'])->first();


        $nextReservation = Reservation::where('reservation_start_time', '>', $myReservation->reservation_end_time)
            ->where('table_id',$myReservation->table_id)
            ->orderBy('reservation_start_time')
            ->first();

        if (!$nextReservation) {
            $data = [];
            $message = __('message.Free_Extend',[],$lang);
            $code = 200;

            return ['data' =>$data,'message'=>$message,'code'=>$code];
        }


        $diffInMinutes = $myReservation->reservation_end_time->diffInMinutes($nextReservation->reservation_start_time);
        $diffCarbon = $myReservation->reservation_end_time->diff($nextReservation->reservation_start_time);


        $diffDays = $diffCarbon->d;  // أيام
        $diffHours = $diffCarbon->h; // ساعات
        $diffMinutes = $diffCarbon->i; // دقائق


        $daysText = $diffDays > 0 ? trans_choice('message.days', $diffDays, ['count' => $diffDays], $lang) : '';
        $hoursText = $diffHours > 0 ? trans_choice('message.hours', $diffHours, ['count' => $diffHours], $lang) : '';
        $minutesText = $diffMinutes > 0 ? trans_choice('message.minutes', $diffMinutes, ['count' => $diffMinutes], $lang) : '';

        $diffFormatted = trim("$daysText, $hoursText, $minutesText", ", ");


        if ($diffInMinutes > 30)
        {
            $message = __('message.extend_session', ['time' => $diffFormatted], $lang);
            $data = [
                'new_start_time' => $myReservation->reservation_end_time->toDateTimeString(),
                'new_end_time' => $nextReservation->reservation_start_time->toDateTimeString(),
                'untile_date' => $message,
            ];
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.cannot_extend',[],$lang);
            $code = 200;
        }


        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }


    public function create_reservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        $customer = Auth::user()->customer;


        if (!$customer->my_wallet)
        {
            $data = [];
            $message = __('message.Wallet_Not_Found', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
            $code = 400;

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }

        $table_price = (int) Table::query()
            ->where('id', $request['table_id'])
            ->pluck('price')
            ->first() ;

        if($customer->my_wallet->amount < $table_price)
        {
            $data = [];
            $message = __('message.Table_Price_exceeds_Your_Wallet', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
            $code = 400;

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }


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

            $table_price = $new_reservation->table->price;

            $old_amount = $customer->my_wallet->amount;
            $customer->my_wallet()->update([
                'amount' => $old_amount - ($table_price / 2)
            ]);


            $data = [];
            $message = __('message.Reservation_Created',[],$lang);
            $code = 201;
        }
        else
        {
            $data = [];
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

            $order = Order::query()->where('reservation_id','=',$reservation->id)->first();

            if($order)
            {
                $order->delete();
            }

            $data = [];
            $message = __('message.Reservation_Canceled', [], $lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    private function calculate_extension_fee(Carbon $originalEndTime, Carbon $newExtendedUntil, float $basePrice): float
    {
        $extensionMinutes = $newExtendedUntil->diffInMinutes($originalEndTime);

        $pricePerMinute = $basePrice / 60;

        $extensionPrice = $pricePerMinute * $extensionMinutes;

        $extensionPrice = max($extensionPrice, 2);

        return round($extensionPrice, 2);
    }

    public function extend_resservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        $reservation = Reservation::query()->where('id','=',$request['reservation_id'])->first();

        $customer = Auth::user()->customer;

        if(!is_null($reservation))
        {

            $extension_fee = $this->calculate_extension_fee($reservation->reservation_end_time,Carbon::parse($request['extended_until']),$reservation->table->price);


            if($customer->my_wallet->amount < $extension_fee)
            {
                $data = [];
                $message = __('message.Extend_Price_exceeds_Your_Wallet', ['price' => $extension_fee], $lang);                $code = 400;

                return ['data' => $data, 'message' => $message, 'code' => $code];
            }

            $reservation->extensions()->create([
                'reservation_id' => $reservation->id,
                'extended_start' => $reservation->reservation_end_time,
                'extended_until' => $request['extended_until']
            ]);

            $reservation->update([
                'reservation_end_time'=> $request['extended_until']
            ]);


            $old_amount = $customer->my_wallet->amount;
            $customer->my_wallet()->update([
                'amount' => $old_amount - $extension_fee
            ]);

            $data = [];
            $message = __('message.Extend_Resrvation_Successfully', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
            $code = 200;
        }

        return ['data' => $data, 'message' => $message, 'code' => $code];


    }

    public function extend_resservation_delay_time($request):array
    {
        $lang = Auth::user()->preferred_language;
        $reservation = Reservation::query()->where('id','=',$request['reservation_id'])->first();

        $customer = Auth::user()->customer;

        if($customer->my_wallet->amount < 3)
        {
            $data = [];
            $message = __('message.Extend_Delay_Price_Exceeds_Your_Wallet ', [], $lang); // استخدم مفتاح حقيقي هنا مثل Wallet_Not_Found
            $code = 400;

            return ['data' => $data, 'message' => $message, 'code' => $code];
        }

        if(!is_null($reservation))
        {
            $data =[];

            if($reservation->is_extended_delay)
            {
                $message = __('message.Already_Extended_Reservation', [], $lang);
                $code = 400;
            }
            else
            {
                $reservation->update([
                    'is_extended_delay' => true
                ]);

                $old_amount = $customer->my_wallet->amount;
                $customer->my_wallet()->update([
                    'amount' => $old_amount - 3
                ]);


                $message = __('message.Delay_Extended_Reservation', [], $lang);
                $code = 200;
            }
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];


    }

    public function check_in_reservation($request):array
    {
        $lang = Auth::user()->preferred_language;

        $reservation = Reservation::query()->where('id','=',$request['reservation_id'])->first();
        $customer = $reservation->customer;

        if($reservation)
        {
            $reservation->update([
                'is_checked_in' => true,
            ]);

            $table_price = $reservation->table->price;

            $old_amount = $customer->myWalllet->amount;
            $customer->myWalllet()->update([
                'amount' => $old_amount - ($table_price / 2)
            ]);

            $data = [];
            $message = __('message.Checked_In_Successful', [], $lang);
            $code = 200;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

}
