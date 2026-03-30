<?php

namespace App\Models;

use App\Casts\ConvertDateToBrCast;
use Exception;
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
        'purchase_id',
        'purchase_value',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => ConvertDateToBrCast::class,
        ];
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class)
            ->withPivot('amount', 'purchase_value')
            ->withTimestamps();
    }

    public static function updateStock(array $products)
    {

        $productIds = array_column($products, 'id');

        $lockedProducts = self::whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $updatedProducts = collect();

        foreach ($products as $productData) {
            $id = $productData['id'];
            $quantityToDeduct = $productData['quantity'] ?? 0;


            $product = $lockedProducts->get($id);

            if (!$product) {
                throw new \Exception("Produto ID {$id} não encontrado no sistema.");
            }

            if ($product->stock_quantity < $quantityToDeduct) {
                throw new \Exception("Estoque insuficiente para o produto: {$product->name}");
            }

            $product->stock_quantity -= $quantityToDeduct;
            $product->save();

            $updatedProducts->push($product);
        }

        return $updatedProducts;
    }
}
