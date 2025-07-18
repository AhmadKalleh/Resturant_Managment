<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','table_id','reservation_start_time','reservation_end_time','is_canceled','is_checked_in','canceled_by','is_extended_delay'];
    protected $dates = [
        'reservation_start_time',
        'reservation_end_time',
    ];

    protected $casts = [
        'reservation_start_time' => 'datetime',
        'reservation_end_time' => 'datetime',
    ];


    public function extensions(): HasMany
    {
        return $this->hasMany(ReservationExtension::class);
    }

    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class, 'created_by');
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table():BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

}
