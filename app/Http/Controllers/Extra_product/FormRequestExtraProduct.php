<?php

namespace App\Http\Controllers\Extra_product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestExtraProduct extends FormRequest
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
                'show_extra_product_details' => $this->show_extra_product_details(),
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'store_extra_product' => $this->store_extra_product(),
                },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'delete_extra_product' => $this->delete_extra_product(),
            },
                default => []
        };
    }

    public function delete_extra_product(): array
    {
        return [
            'product_id' => 'required|integer',
            'extra_product_id' => 'required|integer'
        ];
    }

    public function show_extra_product_details(): array
    {
        return [
            'product_id' => 'required|integer'
        ];
    }

    public function store_extra_product(): array
    {
        return [
            'product_id' => 'required|integer',
            'extra_ids' =>'required|array',
            'extra_ids.*' =>'required|integer',

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
