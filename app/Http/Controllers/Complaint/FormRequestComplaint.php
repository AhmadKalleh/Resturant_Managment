<?php

namespace App\Http\Controllers\Complaint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestComplaint extends FormRequest
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
            'POST' => match ($this->route()->getActionMethod()) {
                'create_complaint' => $this->create_complaint(),
                'resolve_complaint'=> $this->resolve_complaint(),
                'dismiss_complaint'=> $this->resolve_complaint(),
            },
            default => []
        };
    }


    public function create_complaint()
    {
        return [
            'subject' => 'required|string',
            'description' => 'required|string',
        ];
    }


    public function resolve_complaint()
    {
        return [
            'complaint_id' => 'required|integer',
            'response' =>'nullable|string'
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
