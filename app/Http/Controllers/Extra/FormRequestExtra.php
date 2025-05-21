<?php

namespace App\Http\Controllers\Extra;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestExtra extends FormRequest
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
                'update' => $this->update(),
                default => []
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'destroy' => $this->delete(),
            },
            default => []
        };
    }

    public function show():array
    {
        return [
            'extra_id' => 'required'
        ];
    }

    public function delete():array
    {
        return [
            'extra_id' => 'required'
        ];
    }

    public function update():array
    {
        return [
            'extra_id' =>'required|integer|exists:extras,id',
            'name_en' =>'sometimes|string',
            'name_ar' =>'sometimes|string',
            'price' => 'sometimes|numeric|min:0|max:999999.99',
            'calories' => 'sometimes|integer|min:0',
        ];
    }

    public function store():array
    {
        return [
            'name_en' =>'required|string',
            'name_ar' =>'required|string',
            'price' => 'required|numeric|min:0|max:999999.99',
            'calories' => 'required|integer|min:0',
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
