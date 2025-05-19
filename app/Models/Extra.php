<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Extra extends Model
{
    use HasFactory,HasTranslations;

    public $translatable= ['name'];
    protected $fillable = ['name','chef_id','price','calories'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'extra_products');
    }


    public function getPriceTextAttribute():string
    {
        return $this->price.' $';
    }
}
