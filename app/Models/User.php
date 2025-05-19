<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

/*
    @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
     * @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
 * @mixin \Eloquent
 *
 * * @method bool delete() Delete the current access token.
 * @see \Laravel\Sanctum\PersonalAccessToken::delete
 *
 * /** @var PersonalAccessToken|null $token
*/

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guard_name = 'web';
    protected $fillable = [
        'preferred_language',
        'preferred_theme',
        'last_name',
        'first_name',
        'email',
        'fcm-token',
        'mobile',
        'gendor',
        'date_of_birth',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function chef():HasOne
    {
        return $this->hasOne(Chef::class);
    }

    public function reception():HasOne
    {
        return $this->hasOne(Reception::class);
    }

    public function customer():HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    public function getFullNameAttribute(): string
    {
        return $this->firstname .' '. $this->lastname;
    }

    public function getMobileTextAttribute(): string
    {
        $mobile = substr($this->mobile,1);

        $mobileFormatted = implode(' ', str_split($mobile, 3));

        return '+963'. $mobileFormatted;
    }
}
