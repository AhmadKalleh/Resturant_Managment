<?php

namespace App\Http\Controllers\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestWallet extends FormRequest
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
            'POST' => match ($this->route()->getActionMethod())
            {
                'ChargeMywallet' => $this->ChargeMywallet(),
                'check_password' => $this->check_password(),

                default => []
            },
            default => []
        };
    }



    public function ChargeMywallet(): array
    {
        return
        [
            'amount' => [
                'sometimes',
                'numeric',
                'min:0.1',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];
    }


    public function check_password(): array
    {
        return[
            'password' => 'required|min:6'
        ];
    }
}

