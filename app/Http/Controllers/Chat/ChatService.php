<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

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
        $user = Auth::user();
        $lang = $user->preferred_language;


        $welcomeMessage = config("ai.welcome_message_{$lang}") ?? config("ai.welcome_message_en");

        if($user->customer->chat)
        {
            $chat_messages = ChatMessage::query()
            ->where('chat_id', $user->customer->chat->id)
            ->get()
            ->map(function ($message) {
                $direction = ($message->sender_type === 'customer') ? 'right' : 'left';

                return [
                    'message' => $message->message,
                    'direction' => $direction,
                ];
            })
            ->toArray();

            // 🟢 إدراج الرسالة الترحيبية في أول الرسائل دائماً
            array_unshift($chat_messages, [
                'message' => $welcomeMessage,
                'direction' => 'left',
            ]);
        }
        else
        {
            $chat_messages = [
                'message' => $welcomeMessage,
                'direction' => 'left'
            ];
        }

        return [
            'data' => $chat_messages,
            'message' => '',
            'code' => 200,
        ];
    }


    public function send_message2($request): array
    {
        $response = null;

        $user = Auth::user();
        $customer = $user->customer;
        $chat = $customer->chat;

        if (!$chat) {
            $chat = $customer->chat()->create([
                'name' => 'chat with ai',
            ]);
        }



        // 🧠 جلب الرسائل السابقة (مرتبة حسب الأقدم)
        $previousMessages = $chat->chat_messages()->oldest()->get()->map(function ($msg) {
            return [
                'role' => $msg->sender_type === 'customer' ? 'user' : 'assistant',
                'content' => $msg->message
            ];
        })->toArray();



        // 📦 جلب المنتجات والإضافات لبناء system prompt
        $products = \App\Models\Product::with(['extra_products.extra'])->get();
        $productsDescription = "";

        foreach ($products as $product) {
            $productNameEn = $product->getTranslation('name', 'en');
            $productNameAr = $product->getTranslation('name', 'ar');
            $productDescEn = $product->getTranslation('description', 'en');
            $productDescAr = $product->getTranslation('description', 'ar');

            // 🥗 المكونات (Ingredients)
            $ingredients = $product->ingredients->map(function ($ingredient) {
                $nameEn = $ingredient->getTranslation('name', 'en');
                $nameAr = $ingredient->getTranslation('name', 'ar');
                $calories = $ingredient->calories;
                return "$nameEn / $nameAr ({$calories} cal)";
            })->join(', ');

            // ➕ الإضافات (Extras)
            $extras = $product->extra_products->map(function ($extra_product) {
                $nameEn = $extra_product->extra->getTranslation('name', 'en');
                $nameAr = $extra_product->extra->getTranslation('name', 'ar');
                $calories = $extra_product->extra->calories;
                $price = $extra_product->extra->price;
                return "$nameEn / $nameAr ({$calories} cal, {$price} $)";
            })->join(', ');

            // 📦 وصف المنتج الكامل
            $productsDescription .= "- $productNameEn / $productNameAr ({$product->calories} cal, {$product->price} $):\n";
            $productsDescription .= "  📝 $productDescEn\n";
            $productsDescription .= "  📝 $productDescAr\n";

            if ($ingredients) {
                $productsDescription .= "  🥗 Ingredients: $ingredients\n";
            }

            if ($extras) {
                $productsDescription .= "  ➕ Addons: $extras\n";
            }

            $productsDescription .= "\n";
        }



        $systemMessage = "You are a highly intelligent AI assistant designed specifically for a smart restaurant app. Your main job is to help customers find and choose meals based on their preferences such as calorie count, budget, taste, and any extras (like cheese, sauces, sides). You have a complete list of available meals and their detailed descriptions below, called productsDescription.

                Your behavior guidelines:

                1. ALWAYS respond based on the information in the provided productsDescription list.
                2. If the customer greets you or thanks you, respond politely and warmly in their language, optionally reminding them you are here to help with meal choices.
                3. When the customer makes a request or asks about a meal or food item (e.g. I want a cheeseburger, Show me meals under 500 calories, Do you have spicy dishes? or any question referencing the productsDescription list), you MUST search thoroughly within the productsDescription to find the best matching meals that fit their criteria.
                4. If you find exact matches or close matches, provide a clear, friendly, and detailed recommendation including meal name, main ingredients, calorie count, price, and any relevant extras.
                5. If there are multiple matching options, present the top 2 or 3 options to the customer, explaining the differences briefly.
                6. If no exact match exists, offer the closest possible alternatives based on their preferences, explaining why these are the best alternatives.
                7. ONLY apologize or say you couldn’t find any matching meal IF you have checked carefully and really found no suitable product in the list. Then, kindly encourage the customer to modify or broaden their criteria.
                8. Always respond to the user using ONLY the language used in their last message.
                If the user writes in Arabic, your entire response must be in Arabic — do NOT include any English words or translations.
                If the user writes in English, respond entirely in English — do NOT include Arabic.
                Never mix languages in a single response.
                Match their tone (formal, casual, friendly).
                9. Handle common conversation cases like greetings, thanks, follow-up questions, or asking for help politely and naturally.
                10. Do NOT make up any meal or product that is not in the provided productsDescription.
                11. When the customer asks questions unrelated to meals or menu, politely decline and redirect with the predefined message (see Rule 21).
                12. If the customer asks for extras (like cheese, sauces, sides), incorporate that into your search and recommendations.
                13.1. When the customer requests a specific ingredient (e.g., chicken, tuna, mushrooms), you must search ONLY within the Ingredients section inside productsDescription.
                Do not consider the name or description of the meal — focus ONLY on Ingredients.

                13.2. When the customer asks for a specific addon or extra (e.g., cheese, sauces, fries), you must search ONLY within the Addons / extras section of productsDescription.

                13.3. If the specific ingredient or addon is NOT found in any product, you must politely apologize and inform the customer that no meal or addon matches their request. Use the following polite predefined message, in the customer’s language:

                - Arabic: \"عذرًا، لم أتمكن من العثور على أي وجبة تحتوي على المكون أو الإضافة التي طلبتها. يمكنك تجربة مكون مختلف أو إخباري بما تحب، وسأساعدك بكل سرور!\"
                - English: \"Sorry, I couldn’t find any meal that contains the ingredient or addon you requested. You can try a different item or let me know your preferences, and I’ll be happy to assist you!\"
                14. When replying to the customer, always respond using only the language the customer used (English or Arabic). If the customer speaks Arabic, return only the Arabic part of the product name, description, ingredients, and extras. If the customer speaks English, return only the English part. Do not include translations.

                ✅ For example, if this meal exists in productsDescription:
                Greek Salad / سلطة يونانية (137 cal, 18 $):
                📝 Fresh and light salad with feta and olives
                📝 سلطة خفيفة وطازجة بجبنة الفيتا والزيتون
                🥗 Ingredients: Tomatoes / طماطم (20 cal), Olives / زيتون (30 cal), Feta / جبنة فيتا (40 cal)
                ➕ Addons: Avocado / أفوكادو (80 cal, 3 $), Egg / بيض (70 cal, 2 $)

                ➡️ If the customer spoke English:

                Greek Salad (137 cal, 18 $):
                📝 Fresh and light salad with feta and olives
                🥗 Ingredients: Tomatoes (20 cal), Olives (30 cal), Feta (40 cal)
                ➕ Addons: Avocado (80 cal, 3 $), Egg (70 cal, 2 $)

                ➡️ If the customer spoke Arabic:

                سلطة يونانية (137 cal, 18 $):
                📝 سلطة خفيفة وطازجة بجبنة الفيتا والزيتون
                🥗 Ingredients: طماطم (20 cal), زيتون (30 cal), جبنة فيتا (40 cal)
                ➕ الإضافات: أفوكادو (80 cal, 3 $), بيض (70 cal, 2 $)

                15. Do not repeat greetings (such as Hello, Welcome, etc.) in every message during the same session. Only greet the customer once at the beginning of the conversation, or if there has been a significant gap in time between the previous messages and the new message. Your focus should remain on helping the customer quickly and clearly. Repeating greetings in every response may feel robotic or unnecessary.
                16. Never mention or refer to the phrase “list of available meals” or any internal source (like productsDescription). You must act and respond naturally, as if you already know all the meal options. Do not tell the customer that you are selecting results “based on the list” or “based on provided data.” Instead, present your suggestions confidently and conversationally, as if you are a smart assistant who already knows the menu.
                17. If the customer asks for a combination of meals (e.g., multiple dishes together) or specifies a set of conditions for a group of meals (such as a total calorie limit, total price limit, or a mix of specific tastes or ingredients), you must search carefully inside the productsDescription to find the best possible combination that matches the customer's overall criteria. Provide a clear, friendly suggestion that explains how the selected meals fit their request.
                18. You are always provided with a list of the customers (previousMessages) in the current chat session. Use this context to remember the customer’s current preferences, previous questions, or anything they've already mentioned (such as asking about calories, ingredients, budget, or meal types). You must respond in a way that reflects awareness of their past messages and show continuity in the conversation. Do not repeat information they already know, and avoid reintroducing yourself or the list of meals unless the conversation has clearly restarted after a long pause.
                19. When the user requests a meal that contains a specific ingredient (e.g., chicken, eggs, rice), you must respond **only with meals that include that exact ingredient**. Do not suggest other meals, and do not add extra options that do not include the requested ingredient. Stick strictly to the requested ingredient, unless the user allows more flexibility.
                20. If the customer asks about anything outside the menu, meals, or food-related topics (such as personal questions, general knowledge, or other services), you must NOT engage in that conversation. Instead, kindly and politely respond with the following message, using the same language as the user:

                - Arabic: \"عذرًا، أنا مساعد ذكي مخصص فقط لمساعدتك في اختيار وجبات الطعام داخل هذا المطعم. إذا كنت تبحث عن وجبة تناسب ذوقك أو ميزانيتك أو تحتوي على مكونات معينة، يسعدني مساعدتك بكل سرور! 😊\"

                - English: \"I'm sorry, but I’m a smart assistant designed specifically to help you choose meals available in this restaurant only. If you're looking for a dish that matches your taste, budget, or specific ingredients, I’ll be more than happy to assist you! 😊\"

                Do NOT answer any question that is unrelated to food or the available productsDescription list.

                The list of available meals: {$productsDescription}

                Use this list as your only source of truth. Your goal is to provide the best meal recommendation that matches the customer's expressed preferences and requests.

                Remember, your tone should be friendly, helpful, and professional. Make the user feel understood and supported in choosing their meal.

                Start each interaction by acknowledging the customer’s language and preferences, then offer your assistance.

                Your response format should be clear and easy to read, using bullet points or numbered lists if multiple options are presented.

                You must always respond with helpful information and never leave the user without a useful reply.

                ---

                End of system prompt.";







        // 🧠 بناء الرسائل كاملة
        $messages = array_merge(
            [['role' => 'system', 'content' => $systemMessage]],
            $previousMessages,
            [['role' => 'user', 'content' => $request['message']]]
        );


        //return ['data' => $messages,'me'=>'M','code'=>200];
        // 📡 إرسال إلى OpenRouter


        try
        {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
                    'HTTP-Referer' => url('/'),
                    'Accept-Language' => 'en',
                    'X-Title' => 'Laravel Restaurant AI',
                ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'mistralai/mistral-small',
                    'messages' => $messages,
                ]);

                if ($response->successful())
                {

                    $data = $response->json();

                    if (isset($data['choices'][0]['message']['content']))
                    {
                        $content = $data['choices'][0]['message']['content'];

                        // 💾 حفظ رسائل الزبون وAI
                        $chat->chat_messages()->createMany([
                            [
                                'reciver_id' => 1,
                                'message' => $request['message'],
                                'customer_id' => $customer->id,
                                'sender_type' => 'customer'
                            ],
                            [
                                'reciver_id' => 1,
                                'message' => $content,
                                'customer_id' => $customer->id,
                                'sender_type' => 'ai'
                            ]
                        ]);

                        return ['data' => $content, 'message' => '', 'code' => 200];
                    }
                    else
                    {
                        return ['data' => [], 'AI did not return a valid content.' => '', 'code' => 500];
                    }
                }

                else {
                    return ['data' => [], 'message' => 'AI request failed with status: ' . $response->status(), 'code' => $response->status()];
                }

            }
            catch (\Exception $e) {
                Log::error('AI Chat Error: ' . $e->getMessage());

                return ['data' => ['Something went wrong while communicating with the AI.'], 'message' => $e->getMessage(), 'code' => 500];
            }

    }

    public function send_message($request):array
    {


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            'HTTP-Referer' => url('/'),
            'Accept-Language' => 'en', // موقعك
            'X-Title' => 'Laravel Chat', // اسم التطبيق الخاص بك
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'anthropic/claude-3-haiku', // يمكنك تغييره لأي موديل مدعوم
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $request['message'],
                ],
            ],
        ]);

        //return ['data' => ['hh' => config('services.openrouter.api_key'),'data' => $response->body()], 'message' => 'تم التحقق', 'code' => 200];

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
