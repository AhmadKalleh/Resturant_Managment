<?php

namespace App\Http\Controllers\TranslateHelper;



trait TranslateHelper
{
    public function translate(string $field,?string $value) : string
    {
        if(empty($value))
        return "";

        $translated_value = __('preferences'. $field .$value ,[],app()->getLocale());

        return $translated_value;
    }
    
}
