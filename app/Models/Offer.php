<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

class Offer extends Model
{

    use HasTranslations;
    use HasFactory;


    public $translatable= ['title','description'];
    protected $fillable = ['created_by','title','description','total_price','price_after_discount','discount_value','start_date','end_date'];

    public function chef():BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }


    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'offer_products');
    }

    protected static function booted()
    {
        static::addGlobalScope('notExpired', function (Builder $builder)
        {
            $builder->where('end_date', '>=', now()->toDateString());
        });
    }
    public function getTotalPriceTextAttribute(): string
    {
        return number_format($this->total_price, 0, ',', ',') . ' $';
    }

    public function getPriceAfterDiscountTextAttribute(): string
    {
        return number_format($this->price_after_discount, 0, ',', ',') . ' $';
    }
    public function getDiscountRateAttribute(): float
    {
        return (float) str_replace('%', '', $this->discount_value) / 100;
    }

    public function getTotalCaloriesTextAttribute(): string
    {
        $total = $this->products->sum('calories');
        return $total . ' calories';
    }



}
