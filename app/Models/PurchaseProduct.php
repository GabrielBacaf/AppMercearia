<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseProduct extends Pivot
{
    protected $table = 'product_purchase';

    protected $casts = [
        'amount' => 'integer',
        'purchase_value' => 'decimal:2',
        'expiration_date' => 'date',
    ];
}
