<?php

namespace App\Http\Services;

use App\Models\Supplier;

class SupplierService
{
    public function storeSupplier(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier;
    }
}
