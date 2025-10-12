<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'value',
        'payment_status',
        'payable_id',
        'payment_type',
        'payable'
    ];
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
