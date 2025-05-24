<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestCart extends FormRequest
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

    public function delete(): array
    {
        return [
            'cart_item_id' => 'required|integer',
        ];
    }

    public function update(): array
    {
        return [
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer'
        ];
    }

    public function store():array
    {
        return [
            'product_id' =>'required|integer',
            'quantity' => 'required|min:1|max:20|integer'
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
