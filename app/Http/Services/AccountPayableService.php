<?php

namespace App\Http\Services;

use App\Models\Purchase;
use App\Models\AccountPayable;
use App\Enums\FinancialStatusEnum;
use App\Enums\AccountPayableTypeEnum;
use Carbon\Carbon;

class AccountPayableService
{
    public function __construct(protected PaymentService $paymentService) {}

    public function generateFromPurchase(Purchase $purchase, int $installments = 1): void
    {
        $totalValue = $purchase->count_value ?? 0;
        
        if ($installments > 0 && $totalValue > 0) {
            $installmentValue = round($totalValue / $installments, 2);

            for ($i = 1; $i <= $installments; $i++) {
                AccountPayable::create([
                    'title' => "Compra #{$purchase->id} - Parcela {$i}/{$installments}",
                    'amount' => $installmentValue,
                    'due_date' => Carbon::now()->addMonths($i - 1)->format('Y-m-d'),
                    'status' => FinancialStatusEnum::PENDING->value,
                    'type' => AccountPayableTypeEnum::SUPPLIER->value,
                    'payable_id' => $purchase->id,
                    'payable_type' => Purchase::class,
                ]);
            }
        }
    }

    public function createStandalone(array $data): AccountPayable
    {
        return AccountPayable::create([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
            'status' => FinancialStatusEnum::PENDING->value,
            'type' => $data['type'] ?? AccountPayableTypeEnum::OPERATIONAL->value,
        ]);
    }

    public function settle(AccountPayable $payable, array $paymentData): AccountPayable
    {
        // Add the payment to the payable
        $payable->payments()->create([
            'value' => $paymentData['amount'],
            'payment_type' => $paymentData['payment_type'],
            'payment_status' => 'Pago' // Hardcoded or dynamic
        ]);

        $totalPaid = $payable->payments()->sum('value');

        if ($totalPaid >= $payable->amount) {
            $payable->update([
                'status' => FinancialStatusEnum::PAID->value,
                'paid_date' => Carbon::now()->format('Y-m-d')
            ]);
        } elseif ($totalPaid > 0) {
            $payable->update([
                'status' => FinancialStatusEnum::PARTIALLY_PAID->value,
            ]);
        }

        return $payable->refresh();
    }
}
