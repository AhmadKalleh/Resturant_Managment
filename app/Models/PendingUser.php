<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'preferred_language',
        'preferred_theme',
        'last_name',
        'first_name',
        'email',
        'mobile',
        'gendor',
        'date_of_birth',
        'password',
        'verfication_code',

    ];
}
