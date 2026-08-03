<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseProductService
{
    
    public function linkProductToPurchase(Purchase $purchase, Product $product, array $data): Product
    {
        return DB::transaction(function () use ($purchase, $product, $data) {
            
            $oldPivot = $product->purchases()->where('purchase_id', $purchase->id)->first();
            $oldAmount = $oldPivot ? $oldPivot->pivot->amount : 0;
            
            $pivotData = $this->buildPivotData($data);

            $product->purchases()->syncWithoutDetaching([$purchase->id => $pivotData]);
          
            $newAmount = $data['amount'];
            
            $difference = (int) $newAmount - (int) $oldAmount;
            
            if ($difference > 0) {
                $product->addStock($difference);
            } elseif ($difference < 0) {
                $product->deductStock(abs($difference));
            }

            $purchase->updateStatus();

            return $product;
        });
    }

  
    public function detachProductFromPurchase(Purchase $purchase, Product $product): void
    {
        DB::transaction(function () use ($purchase, $product) {

            $oldPivot = $product->purchases()->where('purchase_id', $purchase->id)->first();
            $oldAmount = $oldPivot ? $oldPivot->pivot->amount : 0;

            $product->purchases()->detach($purchase->id);

            if ($oldAmount > 0) {
                $product->deductStock((int) $oldAmount);
            }

            $purchase->updateStatus();
        });
    }

    private function buildPivotData(array $data): array
    {
        return [
            'purchase_value' => $data['purchase_value'],
            'amount' => $data['stock_quantity'],
            'expiration_date' => isset($data['expiration_date'])
                ? Carbon::parse($data['expiration_date'])->format('Y-m-d')
                : null,
        ];
    }
}
