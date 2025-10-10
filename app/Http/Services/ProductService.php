<?php

namespace App\Http\Services;

use App\Enums\StatusEnum;
use App\Models\Product;
use App\Models\Purchase;
use DB;
use Exception;

class ProductService
{
    public function __construct()
    {
    }

    public function storeProduct($data)
    {

        try {
            DB::beginTransaction();

            $productCreated = Product::create($data->exception('purchase_id', 'purchase_value'));

            $this->dataPivot($data, $productCreated);

            $this->dataPurchase($data);

            db::commit();

            return $productCreated;
        } catch (Exception $e) {

            Log::error();

            db::rollBack();

            return null;

        }

    }

    public function dataPivot($data, $productCreated)
    {
        $pivotData = $data->only('purchase_id', 'purchase_value');

        $productCreated->purchases()->syncWithPivotValues(array_merge($pivotData, [
            'product_id' => $productCreated->id,
            'amount' => $productCreated->stock_quantity,
        ]));
    }

    public function dataPurchase($data)
    {
        $purchase = Purchase::findOrFail($data->purchase_id);
        $totalItemValue = $purchase->products()->sum('purchase_value');
        $purchase->count_value = $purchase->value - $totalItemValue;

        if($purchase->count_value === 0){
            $purchase->status = StatusEnum::FINALIZADO->value;
        }else if($purchase->count_value < 0){
                $purchase->status = StatusEnum::ERRORESTOQUE->value;
        }
        $purchase->save();

    }

}
