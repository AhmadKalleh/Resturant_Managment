<?php

namespace App\Http\Controllers\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestOrder extends FormRequest
{
    use ResponseHelper;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method())
        {
            'GET' => match ($this->route()->getActionMethod()) {
                'show_pre_order' => $this->show_pre_order(),
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'create_pre_order' => $this->create_pre_order(),
                'create_order_now' => $this->create_order_now(),
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'cancel_pre_order' => $this->cancel_pre_order(),
            },
            default => []
        };
    }

    public function create_pre_order():array
    {
        return [
            'prepare_at' => 'required|string',
            'reservation_id' =>'required|integer',
            'cart_id' =>'required|integer',
            'cart_item_ids'=>'required|array',
            'cart_item_ids.*' =>'required|integer'
        ];
    }


    public function create_order_now():array
    {
        return [
            'cart_id' =>'required|integer',
            'cart_item_ids'=>'required|array',
            'cart_item_ids.*' =>'required|integer'
        ];
    }

    public function show_pre_order():array
    {
        return [
            'cart_id'=>'required|integer'
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        // الحصول على اللغة الحالية
        $language = app()->getLocale();

        // تحميل الرسائل بناءً على اللغة
        $messages = $validator->errors()->toArray();

        // تحويل الرسائل إلى اللغة المطلوبة
        $translatedMessages = [];
        foreach ($messages as $field => $messageArray) {
            foreach ($messageArray as $message) {
                $translatedMessages[$field][] = __($message, [], $language);
            }
        }

        // إرجاع الاستجابة مع الرسائل المترجمة
        throw new ValidationException($validator, $this->Validation([], $translatedMessages));
    }
}
