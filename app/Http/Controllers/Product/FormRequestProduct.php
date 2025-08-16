<?php

namespace App\Http\Controllers\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestProduct extends FormRequest
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
                'index' => $this->index(),
                'show' => $this->show(),
                'show_product_by_chef' => $this->show(),
                'searchByCategory' => $this->searchByCategory(),
                'search' => $this->search(),
                'filter' => $this->filter(),
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

    public function show(): array
    {
        return [
            "product_id"=>'required|integer',
        ];
    }

    public function searchByCategory(): array
    {
        return [
            'category_id'=>'required|integer',
            'value' =>'required|string'
        ];
    }

    public function filter():array
    {
        return [
            'price_start' =>'required|numeric|between:1,2000',
            'price_end' =>'required|numeric|between:1,2000|gte:price_start',
            'category_id' =>'required|integer',
            'calories_start' =>'required|integer||between:1,800',
            'calories_end' =>'required|integer||between:1,800|gte:calories_start',
        ];
    }

    public function search(): array
    {
        return [
            'value' =>'required|string'
        ];
    }

    public function update(): array
    {
        return [
            'product_id'=>'required|integer',
            'name_en' =>'sometimes|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'name_ar' =>'sometimes|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'description_en' =>'sometimes|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'description_ar' =>'sometimes|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'price' => 'sometimes|numeric',
            'calories' =>'sometimes|numeric',
            'image_file' =>'sometimes|file|mimes:jpeg,png,jpg,gif,svg,ico'
        ];
    }

    public function store(): array
    {
        return [
            'category_id'=>'required|integer',
            'name_en' =>'required|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'name_ar' =>'required|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'description_en' =>'required|string|regex:/^[a-zA-Z\s\-\_\&\^\%\$\#\@]+$/',
            'description_ar' =>'required|string|regex:/^[\p{Arabic}\s\-\_\&\^\%\$\#\@]+$/u',
            'price' => 'required|numeric',
            'calories' =>'required|numeric',
            'image_file' =>'required|file|mimes:jpeg,png,jpg,gif,svg,ico'
        ];
    }

    public function delete(): array
    {
        return [
            'product_id'=>'required|integer',
        ];
    }

    public function index(): array
    {
        return [
            'category_id'=>'required|integer',
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
