<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Model
{
    use HasFactory,HasRoles;

    protected $guard_name = 'web';

    protected $fillable = ['person_height','person_weight','blocked_until','no_show_count','block_reservation'];
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function myWallet():HasOne
    {
        return $this->hasOne(MyWallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratings():MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function chat()
    {
        return $this->hasOne(Chat::class); // تأكد من هذا
    }


    public function chat_messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function behaviorloges(): HasMany
    {
        return $this->hasMany(BehaviorLog::class);
    }

    public function getPersonHeightTextAttribute(): string
    {
        return $this->personheight.' cm';
    }

    public function getPersonWeightTextAttribute(): string
    {
        return $this->person_weight.' kg';
    }
}
