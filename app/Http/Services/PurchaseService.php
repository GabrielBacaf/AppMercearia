<?php

namespace App\Http\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected DocumentService $documentService
    ) {}

    public function storePurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchase = Purchase::create($data);

            $this->paymentService->syncPayments($purchase, $data['payments'] ?? []);
            $this->documentService->syncDocuments($purchase, $data['documents'] ?? []);

            return $purchase;
        });
    }


    public function updatePurchase(array $data, Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($data, $purchase) {

            $purchase->update($data);

            $this->paymentService->syncPayments($purchase, $data['payments'] ?? []);
            $this->documentService->syncDocuments($purchase, $data['documents'] ?? []);

            $purchase->updateStatus();

            return $purchase;
        });
    }
}
