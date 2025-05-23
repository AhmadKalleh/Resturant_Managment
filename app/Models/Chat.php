<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['name','customer_id'];
    public function customer():BelongsTo
    {
        return $this->BelongsTo(Customer::class);
    }

    public function chat_messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

}
