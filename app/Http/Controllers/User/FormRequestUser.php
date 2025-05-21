<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestUser extends FormRequest
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
            'GET' => match ($this->route()->getActionMethod()) {
                'check_password' => $this->check_password(),
                default => []
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'change_mobile' => $this->change_mobile(),
                'update_password' => $this->update_password(),
                'update_image_profile' => $this->update_image_profile(),
                default => []
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'destroy' => $this->delete_account(),
            },
            default => []
        };
    }

    protected function prepareForValidation()
    {

        if ($this->method() === 'POST'
        && $this->route()->getActionMethod() === 'change_mobile')
        {
            $this->merge([
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

    public function change_mobile():array
    {
        return [
            'mobile'=>'required|string|phone:US-SY,mobile,AUTO',
        ];
    }

    public function check_password():array
    {
        return [
            'password' =>'required|min:6',
        ];
    }

    public function delete_account():array
    {
        return [
            'password' =>'required',
        ];
    }

    public function update_image_profile():array
    {
        return [
            'image' =>'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function update_password():array
    {
        return [
            'password' =>'required|min:6',
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
