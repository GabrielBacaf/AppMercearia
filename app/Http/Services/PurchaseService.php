<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Enums\StatusEnum;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{

    public function storePurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchaseData = collect($data)->except(['payment_type', 'payment_status', 'value', 'count_value'])->all();
            $paymentData = collect($data)->only(['payment_type', 'payment_status', 'value'])->all();

            $purchase = Purchase::create(
                array_merge($purchaseData, [
                    'status' => StatusEnum::PENDENTE->value,
                    'user_id' => Auth::id(),
                ])
            );
            $purchase->payments()->create($paymentData);

            return $purchase;
        });
    }


    public function updatePurchase(array $data, Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($data, $purchase) {

            $purchaseData = collect($data)->except(['payment_type', 'payment_status', 'value', 'count_value'])->all();
            $paymentData = collect($data)->only(['payment_type', 'payment_status', 'value'])->all();

            $purchase->update(
                array_merge($purchaseData, [
                    'user_id' => Auth::id(),
                ])
            );

            $purchase->payments()->update([$paymentData]);

            $purchase->updateStatus();

            return $purchase;
        });
    }
}
