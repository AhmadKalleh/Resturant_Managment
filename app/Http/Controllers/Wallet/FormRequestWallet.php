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
            'POST' => match ($this->route()->getActionMethod()) {
                'ChargeMywallet' => $this->ChargeMywallet(),
                'show_my_wallet' => $this->show_my_wallet(),

                 default => []
            },
            default => []
        };
    }



        public function ChargeMywallet(): array
    {
        return
        [
        'cvc' => 'required|digits:3',
        'card_number' => 'required|digits_between:13,19',
        'amount' => [
            'sometimes',
            'numeric',
            'min:0.1',
            'regex:/^\d+(\.\d{1,2})?$/'
        ],
        'email' => 'required|email',

        ];
    }


            public function show_my_wallet(): array
    {
        return[
       'password' => 'required|min:6'
        ];
    }
}

