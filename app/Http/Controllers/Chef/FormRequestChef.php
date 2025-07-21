<?php

namespace App\Http\Controllers\Chef;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestChef extends FormRequest
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
            'GET' => match ($this->route()->getActionMethod())
            {
                'show' => $this->show(),
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'store' => $this->store(),
                'transfer_ownership' => $this->transfer_ownership(),
                default => []
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'delete' => $this->delete(),
            },
            default => []
        };
    }

    public function transfer_ownership(): array
    {
        return [
            'from_chef_id' => 'required|integer',
            'to_chef_id'=> 'required|integer',
        ];
    }

    public function store(): array
    {
        return [
            'preferred_language' =>'nullable|string',
            'preferred_theme' =>'nullable|string',
            'gendor' =>'required|string',
            'date_of_birth'=>'required|date_format:Y-m-d',
            'first_name' =>'required|string|max:50|min:2',
            'last_name' =>'required|string|max:50|min:2',
            'mobile'=>'required|string|phone:US-SY,mobile,AUTO',
            'email' =>'required|email|unique:pending_users,email',
            'password' =>'required|min:6',
            'speciality_en' => 'required|array',
            'speciality_en.*' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'speciality_ar' => 'required|array',
            'speciality_ar.*' =>'required|string|regex:/^[\p{Arabic}\s]+$/u',
            'years_of_experience'=> 'required|min:1|integer',
            'bio' => 'nullable|string',
            'certificates' => 'required|array',
            'certificates.*' => 'required|string|max:255',
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

    public function delete(): array
    {
        return [
            'chef_id' => 'required|integer'
        ];
    }
    public function show(): array
    {
        return [
            'chef_id' => 'required|integer'
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
