<?php

namespace App\Http\Controllers\Category;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestCategory extends FormRequest
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
            'name_en' =>'sometimes|string|regex:/^[a-zA-Z\s]+$/',
            'name_ar' =>'sometimes|string|regex:/^[\p{Arabic}\s]+$/u',
            'description_en' => 'sometimes|string|regex:/^[a-zA-Z\s]+$/',
            'description_ar' => 'sometimes|string|regex:/^[\p{Arabic}\s]+$/u',
            'table_Image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',


        ];
    }

    public function store():array
    {
        return [
            'name_en' =>'required|string|regex:/^[a-zA-Z\s]+$/',
            'name_ar' =>'required|string|regex:/^[\p{Arabic}\s]+$/u',
            'description_en' =>'required|string|regex:/^[a-zA-Z\s]+$/',
            'description_ar' => 'required|string|regex:/^[\p{Arabic}\s]+$/u',
            'table_Image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',


        ];
    }
}
