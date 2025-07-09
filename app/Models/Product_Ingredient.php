<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','ingredient_id'];
}
