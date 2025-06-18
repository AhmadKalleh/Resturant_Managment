<?php

namespace App\Http\Controllers\Reception;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;

class FormRequestReception extends FormRequest
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
        return match ($this->method()) {
            'POST' => match ($this->route()->getActionMethod()) {
                'show' => $this->show(),
                'store' => $this->store(),
                 default => []
            },
            'DELETE' => match ($this->route()->getActionMethod()) {
                'destroy' => $this->destroy(),
                default => []
            },

            default => []
        };
    }



    public function show(): array
    {
        return [
            'id' =>'integer'
        ];
    }

    public function destroy(): array
    {
        return [
            'id' => 'required|integer|exists:users,id'
        ];
    }


    public function store(): array
    {
        return
        [
            'gendor' =>'required|string',
            'date_of_birth'=>'required|date_format:Y-m-d',
            'first_name' =>'required|string|max:50|min:2',
            'last_name' =>'required|string|max:50|min:2',
            'mobile'=>'required|string|phone:US-SY,mobile,AUTO|unique:pending_users,mobile|unique:users,mobile',
            'email' =>'required|email|unique:pending_users,email|unique:users,email',
            'password' =>'required|min:6',
            'shift' => 'required|in:morning,evening,night',
            'years_of_experience' => 'required|integer|min:0|max:50',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->method() === 'POST'
        && $this->route()->getActionMethod() === 'store')
        {
            $this->merge([
            'first_name' => trim($this->first_name),
            'last_name' => trim($this->last_name),
            'email' => trim($this->email),
            'password' => trim($this->password),
            'date_of_birth' => trim($this->date_of_birth),
            'mobile' => $this->normalizePhone($this->mobile),
        ]);
        }
    }

    private function normalizePhone(string $mobile): string
    {

        $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);

        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        $fullPhone = '+963' . $cleanPhone;

        return $fullPhone;
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
