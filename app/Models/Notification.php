<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'created_by', 'channel', 'data', 'is_read'];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function notification_reads():HasMany
    {
        return $this->hasMany(Notification_Read::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
