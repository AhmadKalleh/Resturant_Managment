<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Extra extends Model
{
    use HasFactory;
    protected $fillable = ['name','chef_id','price'];

    public function extra_products() : HasMany
    {
        return $this->hasMany(ExtraProduct::class);
    }
}
