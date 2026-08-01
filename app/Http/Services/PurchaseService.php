<?php

namespace App\Http\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected DocumentService $documentService,
        protected AccountPayableService $payableService
    ) {}

    public function storePurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchase = Purchase::create($data);

            $installments = $data['installments'] ?? 1;
            
            $this->payableService->generateFromPurchase($purchase, $installments);

            $firstPayable = $purchase->accountsPayable()->orderBy('due_date', 'asc')->first();

            if ($firstPayable && !empty($data['payments'])) {
                foreach ($data['payments'] as $paymentData) {
                    $this->payableService->settle($firstPayable, $paymentData);
                }
            }

            $this->documentService->syncDocuments($purchase, $data['documents'] ?? []);

            $purchase->updateStatus();

            return $purchase;
        });
    }

    public function updatePurchase(array $data, Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($data, $purchase) {

            $purchase->update($data);

            $firstPayable = $purchase->accountsPayable()->orderBy('due_date', 'asc')->first();
            
            if ($firstPayable && !empty($data['payments'])) {
                foreach ($data['payments'] as $paymentData) {
                    $this->payableService->settle($firstPayable, $paymentData);
                }
            }

            $this->documentService->syncDocuments($purchase, $data['documents'] ?? []);

            $purchase->updateStatus();

            return $purchase;
        });
    }

    public function deletePurchase(Purchase $purchase): void
    {
        if ($purchase->products()->count() > 0) {
            abort(400, 'A compra possui vínculo com produtos');
        }

        $purchase->delete();
    }
}
