<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\FCM_SERVICE\FcmService;
use App\Http\Controllers\Notification\NotificationService;
use App\Models\Complaints;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class ComplaintService
{
    public function index_complaints():array
    {

        $lang = Auth::user()->preferred_language;


         // Helper function to map leaves with translations
        $mapComplaints = function ($Complaints) use ($lang) {
            return $Complaints->map(function ($complaint) use ($lang) {
                $base = [
                    'id' => $complaint->id,
                    'full_name' => $complaint->customer->user?->full_name ?? 'Unknown',
                    'subject' => $complaint->subject,
                    'description' => $complaint->description,
                    'status' => __('preferences.status.' . $complaint->status, [], $lang),
                ];

                $additional = [];

                if ($complaint->status != 'pending') {
                    $additional = [
                        'response' => $complaint->response,
                        'responded_at' => $complaint->responded_at,
                    ];
                }

                return array_merge($base, $additional);
            });
        };

        // Fetching leaves grouped by status
        $pendingComplaints = Complaints::with('customer.user')->where('status', 'pending')->get();
        $resolvedComplaints = Complaints::with('customer.user')->where('status', 'resolved')->get();
        $dismissedComplaints = Complaints::with('customer.user')->where('status', 'dismissed')->get();



        $data = [
            'pendings_complaints' => $mapComplaints($pendingComplaints),
            'resolved_complaints' => $mapComplaints($resolvedComplaints),
            'dismissed_complaints' => $mapComplaints($dismissedComplaints),
        ];

        $message = __('message.Complaints_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function create_complaint($request):array
    {

        $lang = Auth::user()->preferred_language;
        $user = Auth::user();


        $exist_complaints = Complaints::query()
        ->where('subject','=',$request['subject'])
        ->where('customer_id', $user->customer->id)
        ->exists();

        if(!$exist_complaints)
            {
                $new_complaint = $user->customer->complaints()->create([
                    'subject' => $request['subject'],
                    'description' => $request['description'],
                    'status' =>'pending'
                ]);

            $admin = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'resturant_manager'))->first();

            if ($admin && $admin->fcm_token)
            {
                // 🔄 تعيين لغة الإشعار إلى لغة الـ admin
                $adminLang = $admin->preferred_language ?? 'ar';

                $title = __('message.new_complaint_title',[],$adminLang); // مثال: "New Complaint Received"
                $body = __('message.new_complaint_body', [
                    'customer' => $user->full_name,
                    'subject' => $new_complaint->subject,
                ].$adminLang);

                $fcmService = new FcmService();
                $fcmService->sendNotification($admin->fcm_token, $title, $body, [
                    'complaint_id' => $new_complaint->id,
                    'type' => 'new_complaint',
                ]);

            }
            $data = [];
            $message = __('message.Complaint_Created', [], $lang); // تأكد من وجود هذا النص في ملف الترجمة
            $code = 200; // Conflict
        }
        else
        {
            $data = [];
            $message = __('message.Complaint_Conflict', [], $lang); // تأكد من وجود هذا النص في ملف الترجمة
            $code = 409; // Conflict
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }



    public function resolve_complaint($request):array
    {
        $lang = Auth::user()->preferred_language;

        $complaint = Complaints::query()->where('id',$request['complaint_id'])->first();

        if(!is_null($complaint))
        {
            $complaint->update([
                'status' => 'resolved',
                'response' => $request['response'],
                'responded_at' => now()
            ]);

            if ($complaint->customer && $complaint->customer->user && $complaint->customer->user->fcm_token)
            {
                $customerUser = $complaint->customer->user;
                $customerLang = $customerUser->preferred_language ?? 'ar';

                $title = __('message.complaint_resolved_title',[],$customerLang);
                $body = __('message.complaint_resolved_body', [
                    'subject' => $complaint->subject,
                    'response' => $request['response'],
                ],$customerLang);

                $fcmService = new FcmService();
                $fcmService->sendNotification($customerUser->fcm_token, $title, $body, [
                    'type' => 'complaint_resolved',
                    'complaint_id' => $complaint->id,
                ]);

                $notification_service = new NotificationService();
                $notification_service->send_private_notification([
                    'receiverId' => $complaint->customer->user->id,
                    'title' => [
                        'en' => 'Complaint Resolved',
                        'ar' => 'تم حل الشكوى'
                    ],
                    'body' => [
                        'en' => 'Your complaint titled:'.$complaint->subject.'has been resolved. Response:'.$request['response'],
                        'ar' => 'تم حل الشكوى بعنوان :'.$complaint->subject.'الرد :'.$request['response']
                    ]
                ]);
            }
            $data = [];
            $message = __('message.Complaint_Resolved',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Complaint_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function dismiss_complaint($request):array
    {
        $lang = Auth::user()->preferred_language;

        $complaint = Complaints::query()->where('id',$request['complaint_id'])->first();

        if(!is_null($complaint))
        {
            $complaint->update([
                'status' => 'dismissed',
                'response' => $request['response'],
                'responded_at' => now()
            ]);

            if ($complaint->customer && $complaint->customer->user && $complaint->customer->user->fcm_token)
            {
                $customerUser = $complaint->customer->user;
                $customerLang = $customerUser->preferred_language ?? 'ar';

                $title = __('message.complaint_dismissed_title',[],$customerLang);
                $body = __('message.complaint_dismissed_body', [
                    'subject' => $complaint->subject,
                    'response' => $request['response'],
                ],$customerLang);

                $fcmService = new FcmService();
                $fcmService->sendNotification($customerUser->fcm_token, $title, $body, [
                    'type' => 'complaint_dismissed',
                    'complaint_id' => $complaint->id,
                ]);

                $notification_service = new NotificationService();
                $notification_service->send_private_notification([
                    'receiverId' => $complaint->customer->user->id,
                    'title' => [
                        'en' => 'Complaint Dismissed',
                        'ar' => 'تم رفض الشكوى'
                    ],
                    'body' => [
                        'en' => 'Your complaint titled:'.$complaint->subject.'has been dismissed. Response:'.$request['response'],
                        'ar' => 'تم رفض الشكوى بعنوان :'.$complaint->subject.'الرد :'.$request['response']
                    ]
                ]);
            }



            $data = [];
            $message = __('message.Complaint_Dismissed',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Complaint_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }
}
