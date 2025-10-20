<?php

namespace App\Http\Services;

use App\Models\Document;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{

    public function storePurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchase = Purchase::create($data);

            Payment::syncPayments($purchase, $data['payments'] ?? []);

            return $purchase;
        });
    }


    public function updatePurchase(array $data, Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($data, $purchase) {

            $purchase->update($data);

            Payment::syncPayments($purchase, $data['payments'] ?? []);

            Document::syncDocuments($purchase, $data['documents'] ?? []);

            $purchase->updateStatus();

            return $purchase;
        });
    }
}
