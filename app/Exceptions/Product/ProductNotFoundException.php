<?php

namespace App\Exceptions\Product;

use App\Exceptions\BaseApiException;

class ProductNotFoundException extends BaseApiException
{
    protected int $statusCode = 404;
    protected string $title = 'O produto solicitado não existe ou foi removido.';

    public function __construct(public readonly int $productId)
    {
        parent::__construct("Produto ID {$this->productId} não encontrado no sistema.");
    }
}
