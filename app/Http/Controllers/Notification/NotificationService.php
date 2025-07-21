<?php

namespace App\Http\Controllers\Notification;

use App\Events\GlobalNotificationEvent;
use App\Events\NewNotificationEvent;
use App\Models\Notification;
use App\Models\Notification_Read;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function index_own_notifivations()
    {
        $userId = Auth::user()->id;
        $lang = Auth::user()->preferred_language;



        $privateNotifications = Notification::where('user_id', $userId)
            ->get()
            ->map(function($notification) use ($lang) {
                return [
                    'notification_id' => $notification->id,
                    'title' => $notification->data['title'][$lang] ?? $notification->data['title']['en'],
                    'body' => $notification->data['body'][$lang] ?? $notification->data['body']['en'],
                    'created_at' => Carbon::parse($notification->created_at)->format('D M Y'),
                    'is_read' => $notification->is_read, // مهم للاستخدام في التصنيف لاحقًا
                ];
            });

            $publicNotifications = Notification::whereNull('user_id')
            ->get()
            ->map(function($notification) use ($lang) {
                return [
                    'notification_id' => $notification->id,
                    'title' => $notification->data['title'][$lang] ?? $notification->data['title']['en'],
                    'body' => $notification->data['body'][$lang] ?? $notification->data['body']['en'],
                    'created_at' => Carbon::parse($notification->created_at)->format('D M Y'),
                    'is_read' => false, // سيتم فحصه لاحقًا من جدول Notification_Read
                ];
            });


            $readPublicIds = Notification_Read::where('user_id', $userId)->pluck('notification_id')->toArray();

            $read = collect();
            $unread = collect();


            foreach ($privateNotifications as $notification) {
                if ($notification['is_read']) {
                    $read->push($notification);
                } else {
                    $unread->push($notification);
                }
            }


            foreach ($publicNotifications as $notification) {
                if (in_array($notification['notification_id'], $readPublicIds)) {
                    $read->push($notification);
                } else {
                    $unread->push($notification);
                }
            }

        $data = [
            'read_notifications' => $read,
            'unread_notifications' => $unread,
        ];

        $message = __('message.Notifications_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function mark_all_as_read()
    {
        $userId = Auth::id();

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $publicNotifications = Notification::whereNull('user_id')->pluck('id')->toArray();

        $alreadyRead = Notification_Read::where('user_id', $userId)
            ->pluck('notification_id')
            ->toArray();

        $unreadPublic = array_diff($publicNotifications, $alreadyRead);

        $insertData = [];
        foreach ($unreadPublic as $notificationId) {
            $insertData[] = [
                'user_id' => $userId,
                'notification_id' => $notificationId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            Notification_Read::insert($insertData);
        }

        $data = [];

        $message ='';
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function send_global_notification($data)
    {
        $excludedRoles = $data['exclude_roles'] ?? [];


        $excludedUsers = User::role($excludedRoles)->pluck('id')->toArray();

        $notification = Notification::create([
            'user_id' => null,
            'created_by' => Auth::user()->id,
            'channel' => 'public-notifications',
            'data' => [
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => 'global',
                'created_at' => now(),
            ],
            'is_read' => false,
        ]);

        // 🎯 بث الحدث فقط للمستخدمين المسموحين

        broadcast(new GlobalNotificationEvent($excludedUsers));

        return ['data' => 'success'];
    }


    public function send_private_notification($data)
    {
        $notification = Notification::create([
            'user_id' => $data['receiverId'],
            'created_by' => Auth::user()->id,
            'channel' => 'private-notifications',
            'data' => [
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => 'private',
                'created_at' => now(),
            ],
            'is_read' => false,
        ]);

        // 🎯 بث الحدث فقط للمستخدمين المسموحين

        broadcast(new NewNotificationEvent($data['receiverId']));

        return ['data' => 'success'];
    }


    public function get_un_read_notification_counts()
    {
        $userId = auth()->id();

        $privateUnread = Notification::query()
        ->where('user_id',$userId)
        ->where('is_read',false)
        ->count();

        $publicUnread = Notification::whereNull('user_id')
        ->whereNotIn('id', function ($query) use ($userId) {
            $query->select('notification_id')
                ->from('notification__reads')
                ->where('user_id', $userId);
        })
        ->count();

        $data = [
            'notifications_count' => $privateUnread + $publicUnread
        ];
        $message = '';
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
}



