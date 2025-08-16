<?php

namespace App\Http\Controllers\Table;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestTable extends FormRequest
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
                'store' => $this->createTable(),
                'update' => $this->updateTable(),

                default => []
            },

            default => []
        };
    }

    public function createTable(): array
    {
        return [
            'seats' => 'required|integer|min:1',
            'location_en' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'location_ar' => 'required|string|max:255|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'price' => 'required|numeric|min:0',
            'table_Image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function updateTable(): array
    {
        return [

            'seats' => 'sometimes|integer|min:1',
            'location_en' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'location_ar' => 'required|string|max:255|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'price' => 'sometimes|numeric|min:0',
            'table_Image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico',
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
