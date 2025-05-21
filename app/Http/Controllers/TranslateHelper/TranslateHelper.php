<?php

namespace App\Http\Controllers\TranslateHelper;

use Illuminate\Support\Facades\Auth;

trait TranslateHelper
{
    public function translate(string $field,?string $value) : string
    {
        if(empty($value))
        return "";

        $translated_value = __('preferences.' . $field . '.' . $value, [], Auth::user()->preferred_language);


        return $translated_value;
    }

}
