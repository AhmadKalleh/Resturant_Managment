<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class ChatController extends Controller
{


    use ResponseHelper;
    private ChatService  $_chatService;

    public function __construct(ChatService  $chatService)
    {
        $this->_chatService = $chatService;
    }


    public function index_chat():JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chatService->index_chat();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function index_chat_message():JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chatService->index_chat_message();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function send_message(FormRequestChat $request):JsonResponse
    {
        $data=[];
        try
        {
            $data = $this->_chatService->send_message($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
