<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class NotificationController extends Controller
{
    use ResponseHelper;

    private NotificationService $_notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->_notificationService = $notificationService;
    }

    public function index_own_notifivations():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_notificationService->index_own_notifivations();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function mark_all_as_read():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_notificationService->mark_all_as_read();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function get_un_read_notification_counts():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_notificationService->get_un_read_notification_counts();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
