<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\FCM_SERVICE\FcmService;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    public function index_leaves():array
    {

        $lang = Auth::user()->preferred_language;


         // Helper function to map leaves with translations
        $mapLeaves = function ($leaves) use ($lang) {
            return $leaves->map(function ($leave) use ($lang) {
                return [
                    'id' => $leave->id,
                    'full_name' => $leave->leaveable->user?->full_name ?? 'Unknown',
                    'type' => __('preferences.type_leaves.' . $leave->type, [], $lang),
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'reason' => $leave->reason,
                    'status' => __('preferences.status.' . $leave->status, [], $lang),
                ];
            });
        };

        // Fetching leaves grouped by status
        $pendingLeaves = \App\Models\Leave::with('leaveable.user')->where('status', 'pending')->get();
        $approvedLeaves = \App\Models\Leave::with('leaveable.user')->where('status', 'approved')->get();
        $rejectedLeaves = \App\Models\Leave::with('leaveable.user')->where('status', 'rejected')->get();



        $data = [
            'pendings_leaves' => $mapLeaves($pendingLeaves),
            'approved_leaves' => $mapLeaves($approvedLeaves),
            'rejected_leaves' => $mapLeaves($rejectedLeaves),
        ];

        $message = __('message.Leaves_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }

    public function create_leave($request):array
    {
        $lang = Auth::user()->preferred_language;
        $user = Auth::user();

        $role = $user->roles->pluck('name')->first();

        $roleData = null;

        switch ($role) {
            case 'chef':
                $roleData = $user->chef;
                break;
            case 'reception':
                $roleData = $user->reception;
                break;
        }


        $exist_leave = Leave::query()
        ->where('status','=','pending')
        ->where('leaveable_id', $roleData->id)
        ->where(function ($query) use ($request) {
            $query->where('start_date', '<', $request['end_date'])
                ->where('end_date', '>', $request['start_date']);
        })
        ->exists();

        if(!$exist_leave)
        {
            $new_leave = $roleData->leaves()->create([
                'type' => $request['type'],
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'reason'  => $request['reason'],
                'status' =>'pending'
            ]);

            $admin = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'resturant_manager'))->first();

            if ($admin && $admin->fcm_token)
            {
                // 🔄 تعيين لغة الإشعار إلى لغة الـ admin
                $adminLang = $admin->preferred_language ?? 'ar';

                $title = __('message.new_leave_title',[], $adminLang); // مثال: "طلب إجازة جديد"
                $body = __('message.new_leave_body', [
                    'employee' => Auth::user()->full_name,
                    'start' => $new_leave->start_date,
                    'end' => $new_leave->end_date,
                ], $adminLang);

                $fcmService = new FcmService();
                $fcmService->sendNotification($admin->fcm_token, $title, $body, [
                    'leave_id' => $new_leave->id,
                    'type' => 'new_leave',
                ]);
            }
            $data = [];
            $message = __('message.Leave_Created', [], $lang); // تأكد من وجود هذا النص في ملف الترجمة
            $code = 200; // Conflict
        }
        else
        {
            $data = [];
            $message = __('message.Leave_Conflict', [], $lang); // تأكد من وجود هذا النص في ملف الترجمة
            $code = 409; // Conflict
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function get_my_leaves():array
    {
        $lang = Auth::user()->preferred_language;


        $user = Auth::user();

        $role = $user->roles->pluck('name')->first();

        $roleData = null;

        switch ($role) {
            case 'chef':
                $roleData = $user->chef;
                break;
            case 'reception':
                $roleData = $user->reception;
                break;
        }

         // Helper function to map leaves with translations
        $mapLeaves = function ($leaves) use ($lang,$roleData) {
            return $leaves->map(function ($leave) use ($lang,$roleData) {
                return [
                    'id' => $leave->id,
                    'full_name' => $roleData->user?->full_name ?? 'Unknown',
                    'type' => __('preferences.type_leaves.' . $leave->type, [], $lang),
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'reason' => $leave->reason,
                    'status' => __('preferences.status.' . $leave->status, [], $lang),587.                ];
            });
        };

        // Fetching leaves grouped by status
        $pendingLeaves = \App\Models\Leave::with('leaveable.user')
            ->where('status', 'pending')
            ->where('leaveable_id', $roleData->id)
            ->where('leaveable_type', get_class($roleData))
            ->get();

        $approvedLeaves = \App\Models\Leave::with('leaveable.user')
            ->where('status', 'approved')
            ->where('leaveable_id', $roleData->id)
            ->where('leaveable_type', get_class($roleData))
            ->get();

        $rejectedLeaves = \App\Models\Leave::with('leaveable.user')
            ->where('status', 'rejected')
            ->where('leaveable_id', $roleData->id)
            ->where('leaveable_type', get_class($roleData))
            ->get();



        $data = [
            'pendings_leaves' => $mapLeaves($pendingLeaves),
            'approved_leaves' => $mapLeaves($approvedLeaves),
            'rejected_leaves' => $mapLeaves($rejectedLeaves),
        ];

        $message = __('message.Leaves_Retrived',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function approve_leave($request):array
    {
        $lang = Auth::user()->preferred_language;

        $leave = Leave::query()->where('id',$request['leave_id'])->first();

        if(!is_null($leave))
        {
            $leave->update([
                'status' => 'approved'
            ]);

            $data = [];
            $message = __('message.Leave_Approved',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Leave_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }

    public function reject_leave($request):array
    {
        $lang = Auth::user()->preferred_language;

        $leave = Leave::query()->where('id',$request['leave_id'])->first();

        if(!is_null($leave))
        {
            $leave->update([
                'status' => 'rejected'
            ]);

            $data = [];
            $message = __('message.Leave_Rejected',[],$lang);
            $code = 200;
        }
        else
        {
            $data = [];
            $message = __('message.Leave_Not_Found',[],$lang);
            $code = 404;
        }

        return ['data' =>$data,'message'=>$message,'code'=>$code];
    }
}
