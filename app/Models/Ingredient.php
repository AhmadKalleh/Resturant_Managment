<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
class Ingredient extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = ['name','calories'];

    public $translatable= ['name'];

    public function products():BelongsToMany
    {
        return $this->belongsToMany(Product::class,'product__ingredients');
    }
}
