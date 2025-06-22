<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;

class FormRequestAuth extends FormRequest
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
                'register_pendding_user' => $this->register_pendding_user(),
                'login' => $this->login(),
                'register' => $this->register(),
                'send_varification_code_to_email' => $this->send_varification_code_to_email(),
                'is_varification_code_right' => $this->is_varification_code_right(),
                'reset_password' => $this->reset_password(),
                'logout' => [],
                default => []
            },
            default => []
        };
    }



    public function send_varification_code_to_email(): array
    {
        return [
            'email' =>'required|email'
        ];
    }

    public function is_varification_code_right(): array
    {
        return [
            'verfication_code' =>'required'
        ];
    }

    public function reset_password(): array
    {
        return [
            'email' =>'required|email',
            'password' => 'required|min:6'
        ];
    }

    public function register_pendding_user(): array
    {
        return
        [
            'preferred_language' =>'required|string',
            'preferred_theme' =>'required|string',
            'gendor' =>'required|string',
            'date_of_birth'=>'required|date_format:Y-m-d',
            'first_name' =>'required|string|max:50|min:2',
            'last_name' =>'required|string|max:50|min:2',
            'mobile'=>'required|string|phone:US-SY,mobile,AUTO|unique:pending_users,mobile|unique:users,mobile',
            'email' =>'required|email|unique:pending_users,email|unique:users,email',
            'password' =>'required|min:6',
        ];
    }

    public function register():array
    {
        return [
            'verification_code' => 'required|string|regex:/^\d{6}$/',
            'payment_method' => 'required|string',
            'fcm_Token' =>'string'
        ];
    }

    public function login():array
    {
        return [
            'email'=>'required|string|email',
            'password' =>'required|min:6',
            'fcm_Token' =>'string'
        ];
    }

    protected function prepareForValidation()
    {

        if ($this->method() === 'POST'
        && $this->route()->getActionMethod() === 'register_pendding_user')
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
