<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Reception extends Model
{
    use HasFactory,HasRoles;
    protected $fillable = ['shift','years_of_experience','user_id'];


    protected $guard_name = 'web';
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getYearsOfExperienceTextAttribute(): string
    {
        $year = ($this->years_of_experience == 1) ? 'year' : 'years';
        return $this->years_of_experience . ' ' . $year;
    }

}
