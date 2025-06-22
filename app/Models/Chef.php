<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class Chef extends Model
{
    use HasFactory,HasTranslations,HasRoles;

    protected $fillable = ['speciality','years_of_experience','bio','certificates','user_id','rating_id'];

    protected $guard_name = 'web';
    public $translatable= ['speciality'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(Extra::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'created_by');
    }


    public function rating()
    {
        return $this->morphOne(Rating::class,'rateable');
    }

    public function getYearsOfExperienceTextAttribute(): string
    {
        $year = ($this->years_of_experience == 1) ? 'year' : 'years';
        return $this->years_of_experience . ' ' . $year;
    }

}
