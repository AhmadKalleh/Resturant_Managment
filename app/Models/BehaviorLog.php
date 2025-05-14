<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class BehaviorLog extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = ['customer_id','type','action_date'];
    public $translatable= ['type'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
