<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;

    public function customer():HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function chatmessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

}
