<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory,HasTranslations;
    public $translatable= ['name','description'];

    protected $fillable = ['name','description','image_id','chef_id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
