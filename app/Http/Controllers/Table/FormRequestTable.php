<?php

namespace App\Http\Controllers\Table;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestTable extends FormRequest
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
            'location_en' => 'required|string|max:255',
            'location_ar' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'table_Image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function updateTable(): array
    {
        return [

            'seats' => 'sometimes|integer|min:1',
            'location_en' => 'required|string|max:255',
            'location_ar' => 'required|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'table_Image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
