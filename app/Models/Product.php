<?php

namespace App\Models;

use App\Casts\ConvertDateToBrCast;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Exceptions\Product\InsufficientStockException;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'barcode',
        'name',
        'sale_value',
        'category_id',
        'stock_quantity',
    ];

    
    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class)
            ->using(PurchaseProduct::class)
            ->withPivot('amount', 'purchase_value', 'expiration_date')
            ->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

   
    public function scopeLockedByIds($query, array $ids)
    {
        return $query->whereIn('id', $ids)->lockForUpdate();
    }

   
    public function deductStock(int $quantity): void
    {
        throw_unless(
            $quantity > 0,
            \InvalidArgumentException::class,
            "A quantidade a ser deduzida deve ser maior que zero."
        );

        throw_unless(
            $this->stock_quantity >= $quantity,
            InsufficientStockException::class,
            $this, 
            $quantity
        );

        $this->stock_quantity -= $quantity;
        $this->save();
    }

    public function addStock(int $quantity): void
    {
        throw_unless(
            $quantity > 0,
            \InvalidArgumentException::class,
            "A quantidade a ser adicionada deve ser maior que zero."
        );

        $this->stock_quantity += $quantity;
        $this->save();
    }
}
