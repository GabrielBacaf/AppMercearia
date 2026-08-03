<?php

namespace App\Exceptions\Product;

use App\Exceptions\BaseApiException;
use App\Models\Product;

class InsufficientStockException extends BaseApiException
{
    protected int $statusCode = 422;
    protected string $title = 'Um dos itens da venda não possui estoque suficiente.';

    public function __construct(public readonly Product $product, public readonly int $attemptedQuantity)
    {
        parent::__construct("Estoque insuficiente para o produto: {$this->product->name}");
        
        $this->details = [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'available_stock' => $this->product->stock_quantity,
            'attempted_quantity' => $this->attemptedQuantity
        ];
    }
}
