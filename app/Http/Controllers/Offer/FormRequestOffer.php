<?php

namespace App\Http\Controllers\Offer;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestOffer extends FormRequest
{
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
public function store(): array
{
    return [
        'title_en' => [
            'required',
            'string',
            'unique:offers,title->en',
            'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
        ],
        'title_ar' => [
            'required',
            'string',
            'unique:offers,title->ar',
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
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'products_ids' => 'required|array|min:1',
        'products_ids.*' => 'integer|exists:products,id',
        'image_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
    ];
}

public function update(): array
{
    return [
        'offer_id' => 'required|integer|exists:offers,id',
        'title_en' => [
            'sometimes',
            'string',
            'unique:offers,title->en,' . $this->offer_id,
            'regex:/^[a-zA-Z0-9\s\p{P}]+$/u'
        ],
        'title_ar' => [
            'sometimes',
            'string',
            'unique:offers,title->ar,' . $this->offer_id,
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
        'start_date' => 'sometimes|date',
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


}
