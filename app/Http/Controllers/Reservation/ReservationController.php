<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class ReservationController extends Controller
{
    use ResponseHelper;

    private ReservationService $_reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->_reservationService = $reservationService;
    }

    public function index():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show_all_reservation_for_table(FormRequestReservation $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->show_all_reservation_for_table($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function create_reservation(FormRequestReservation $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->create_reservation($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function cancel_reservation(FormRequestReservation $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->cancel_reservation($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function extend_resservation_delay_time(FormRequestReservation $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->extend_resservation_delay_time($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function check_in_reservation(FormRequestReservation $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_reservationService->check_in_reservation($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
