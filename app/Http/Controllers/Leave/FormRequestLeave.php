<?php

namespace App\Http\Controllers\Leave;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestLeave extends FormRequest
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
                'create_leave' => $this->create_leave(),
                'approve_leave'=> $this->approve_leave(),
                'reject_leave'=> $this->approve_leave(),
            },
            default => []
        };
    }


    public function create_leave()
    {
        return [
            'type' => 'required|string',
            'start_date'     => 'required|date|before_or_equal:end_date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'reason'         => 'nullable|string|max:1000',
        ];
    }


    public function approve_leave()
    {
        return [
            'leave_id' => 'required|integer'
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
