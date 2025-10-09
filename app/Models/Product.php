<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'name',
        'expiration_date',
        'sale_value',
        'category',
        'stock_quantity',

    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'data',
        ];
    }
}
