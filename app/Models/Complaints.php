<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaints extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','subject','description','response','responded_at','status'];


    public function customer():BelongsTo
    {
        return $this->belongsTo(customer::class);
    }

}
