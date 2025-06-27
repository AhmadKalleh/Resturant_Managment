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
            'GET' => match ($this->route()->getActionMethod()) {
                'show_own_extra_for_product' =>$this->show_own_extra_for_product()
            },
            'POST' => match ($this->route()->getActionMethod()) {
                'store' => $this->store(),
                'update_quantity' => $this->update_quantity(),
                'update_cart_item' => $this->update_cart_item(),
                default => []
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'destroy' => $this->delete(),
                'destroy_extra' => $this->destroy_extra(),
            },
            default => []
        };
    }

    public function show_own_extra_for_product(): array
    {
        return [
            'cart_item_id' =>'required|integer'
        ];
    }

    public function update_cart_item(): array
    {
        return [
            'cart_item_id' => 'required|integer',
            'extra_product_ids'   => 'sometimes|array',
            'extra_product_ids.*' => 'integer|exists:extra_products,id',
        ];
    }

    public function delete(): array
    {
        return [
            'cart_item_id' => 'required|integer',
        ];
    }

    public function destroy_extra(): array
    {
        return [
            'cart_item_id' => 'required|integer',
            'extra_product_id' => 'required|integer',
        ];
    }

    public function update_quantity(): array
    {
        return [
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer'
        ];
    }

    public function store():array
    {
        return [
            'product_id' => 'required_without:offer_id|nullable|integer',
            'offer_id'   => 'required_without:product_id|nullable|integer',

            'quantity' => 'required|min:1|max:20|integer',

            'extra_product_ids'   => 'sometimes|array',
            'extra_product_ids.*' => 'integer|exists:extra_products,id',
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
