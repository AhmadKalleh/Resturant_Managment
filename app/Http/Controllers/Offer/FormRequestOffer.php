<?php

namespace App\Http\Controllers\Offer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Support\Carbon;

class FormRequestOffer extends FormRequest
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
                     default => [],
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

    public function store(): array
    {
        return [
            'type' => 'required|in:normal_day,special_day',
            'title_en' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
            ],
            'title_ar' => [
                'required',
                'string',
                'regex:/^[\p{Arabic}0-9\s\p{P}]+$/u'
            ],
            'description_en' => [
                'nullable',
                'string',
                'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
            ],
            'description_ar' => [
                'nullable',
                'string',
                'regex:/^[\p{Arabic}0-9\s\p{P}]+$/u'
            ],
            'discount_value' => 'required|string|regex:/^\d+%$/',
            'start_date' => 'sometimes|date|after_or_equal:'. Carbon::now()->toDateString(),
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'products_ids' => 'required|array|min:1',
            'products_ids.*' => 'integer|exists:products,id',
            'image_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];

    }

    public function update(): array
    {
        return [
            'type' => 'sometimes|in:normal_day,special_day',
            'offer_id' => 'required|integer|exists:offers,id',
            'title_en' => [
                'sometimes',
                'string',
                'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
            ],
            'title_ar' => [
                'sometimes',
                'string',
                'regex:/^[\p{Arabic}0-9\s\p{P}]+$/u'
            ],
            'description_en' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
            ],
            'description_ar' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[\p{Arabic}0-9\s\p{P}]+$/u'
            ],
            'discount_value' => 'sometimes|string|regex:/^\d+%$/',
            'start_date' => 'sometimes|date|after_or_equal:'. Carbon::now()->toDateString(),
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'products_ids' => 'sometimes|array|min:1',
            'products_ids.*' => 'integer|exists:products,id',
            'image_file' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function show(): array
    {
        return [
            'offer_id' => 'required|integer|exists:offers,id',
        ];
    }

    public function delete(): array
    {
        return [
            'offer_id' => 'required|integer|exists:offers,id',
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
