<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = ['leaveable_id','leaveable_type','type','start_date','end_date','reason','status'];

    public function leaveable()
    {
        return $this->morphTo();
    }

}
