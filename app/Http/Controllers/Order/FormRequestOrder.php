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
                'show_all_reservation_for_table' => $this->show_all_reservation_for_table(),
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'create_reservation' => $this->create_reservation(),
                'check_in_reservation' => $this->cancel_reservation(),
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'cancel_reservation' => $this->cancel_reservation(),
            },
            default => []
        };
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
