<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','amount'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getAmountTextAttribute():string
    {
        return rtrim(rtrim(number_format($this->amount, 2, '.', ','), '0'), '.') . ' $';
    }
}
