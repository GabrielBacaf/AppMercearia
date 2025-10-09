<?php

namespace App\Models;

use App\Casts\ConvertDateToBrCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
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
            'expiration_date' => ConvertDateToBrCast::class,
        ];
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_item', 'product_id', 'purchase_id')
            ->withPivot('amount', 'purchase_value')
            ->withTimestamps();
    }
}
