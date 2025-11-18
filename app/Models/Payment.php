<?php

namespace App\Models;

use App\Http\Traits\SyncPayments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Payment extends Model
{
    use HasFactory, SyncPayments;

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
