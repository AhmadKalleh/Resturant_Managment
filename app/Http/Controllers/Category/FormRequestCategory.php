<?php

namespace App\Http\Controllers\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;



class FormRequestCategory extends FormRequest
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
            'category_id' => 'required'
        ];
    }

    public function delete():array
    {
        return [
            'category_id' => 'required'
        ];
    }

    public function update():array
    {
        return [
            'category_id' =>'required|integer|exists:extras,id',
            'name_en' =>'sometimes|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'name_ar' =>'sometimes|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'description_en' => 'sometimes|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'description_ar' => 'sometimes|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'category_Image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function store():array
    {
        return [
            'name_en' =>'required|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'name_ar' =>'required|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'description_en' =>'required|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'description_ar' => 'required|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'category_Image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
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
