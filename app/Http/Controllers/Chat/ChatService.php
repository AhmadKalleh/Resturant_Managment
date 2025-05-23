<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatService
{
    public function index_chat(): array
    {
        $lang = Auth::user()->preferred_language;
        $chat = Chat::query()
        ->where('customer_id', Auth::user()->customer->id)
        ->with(['chat_messages' => function ($query) {
            $query->where('sender_type','ai')->latest()->limit(1);
        }])
        ->first();


        if(is_null($chat))
        {
            $message =__('message.Chat_Empty',[],$lang);
            return ['data' => [], 'message' => $message, 'code' => 200];
        }
        else
        {
            $lastMessage = $chat->chat_messages->first();

            $data = [
                'id' => $chat->id,
                'customer_id' => Auth::user()->customer->id,
                'name' => $chat->name,
                'latest_message' => $lastMessage ? [
                    'id' => $lastMessage->id,
                    'message' => $lastMessage->message,
                    'created_at' => Carbon::parse($lastMessage->created_at)->format('Y-m-d'),
                ] : null,
            ];


            return ['data' => $data, 'message' => '', 'code' => 200];
        }


    }

    public function index_chat_message(): array
    {
        $lang = Auth::user()->preferred_language;

        $chat_messages = ChatMessage::query()
        ->where('chat_id',Auth::user()->customer->chat->id)
        ->get()
        ->map(function ($message) use ($lang)
        {
            $direction = ($message->sender_type === 'customer') ? 'right' : 'left';

            return [
                'message' => $message->message,
                'direction' => $direction,
            ];
        });

        return ['data'=> $chat_messages, 'message'=> '','code' =>200];

    }
    public function send_message($request):array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer' => url('/'),
            'Accept-Language' => 'en', // موقعك
            'X-Title' => 'Laravel Chat', // اسم التطبيق الخاص بك
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo', // يمكنك تغييره لأي موديل مدعوم
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $request['message'],
                ],
            ],
        ]);

        if ($response->successful())
        {

            $content = $response->json()['choices'][0]['message']['content'];
            $user = Auth::user();
            $customer = $user->customer;
            $chat = $customer->chat; // بدون () لأنه علاقة hasOne أو belongsTo

            if ($chat)
            {
                $chat->chat_messages()->createMany([
                    [
                        'reciver_id' => 1,
                        'message' => $request['message'],
                        'customer_id' => $customer->id,
                        'sender_type' =>'customer'
                    ],
                    [
                        'reciver_id' => 1,
                        'message' => $content,
                        'customer_id' => $customer->id,
                        'sender_type' =>'ai'
                    ]
                ]);
            }
            else
            {
                $chat = $customer->chat()->create([
                    'name' => 'chat with ai',
                ]);

                $chat->chat_messages()->createMany([
                    [
                        'reciver_id' => 1,
                        'message' => $request['message'],
                        'customer_id' => $customer->id,
                        'sender_type' =>'customer'
                    ],
                    [
                        'reciver_id' => 1,
                        'message' => $content,
                        'customer_id' => $customer->id,
                        'sender_type' =>'ai'
                    ]
                ]);
            }


            return ['data' => $content, 'message' => '', 'code' => 200];
        } else {
            return ['data' => [], 'message' => 'حدث خطأ أثناء الاتصال بالسيرفر.', 'code' => 500];
        }
    }
}
