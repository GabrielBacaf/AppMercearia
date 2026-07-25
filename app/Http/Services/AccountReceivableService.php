<?php

namespace App\Http\Services;

use App\Models\Sale;
use App\Models\AccountReceivable;
use App\Enums\FinancialStatusEnum;
use Carbon\Carbon;

class AccountReceivableService
{
    public function __construct(protected PaymentService $paymentService) {}

    public function generateFromSale(Sale $sale, int $installments = 1): void
    {
        $totalValue = $sale->total_value ?? 0;
        
        if ($installments > 0 && $totalValue > 0) {
            $installmentValue = round($totalValue / $installments, 2);

            for ($i = 1; $i <= $installments; $i++) {
                AccountReceivable::create([
                    'title' => "Venda #{$sale->id} - Parcela {$i}/{$installments}",
                    'amount' => $installmentValue,
                    'due_date' => Carbon::now()->addMonths($i - 1)->format('Y-m-d'),
                    'status' => FinancialStatusEnum::PENDING->value,
                    'client_id' => $sale->client_id,
                    'receivable_id' => $sale->id,
                    'receivable_type' => Sale::class,
                ]);
            }
        }
    }

    public function createStandalone(array $data): AccountReceivable
    {
        return AccountReceivable::create([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
            'status' => FinancialStatusEnum::PENDING->value,
            'client_id' => $data['client_id'] ?? null,
        ]);
    }

    public function settle(AccountReceivable $receivable, array $paymentData): AccountReceivable
    {
        // Add the payment to the receivable
        $receivable->payments()->create([
            'value' => $paymentData['amount'],
            'payment_type' => $paymentData['payment_type'],
            'payment_status' => 'Pago' 
        ]);

        $totalReceived = $receivable->payments()->sum('value');

        if ($totalReceived >= $receivable->amount) {
            $receivable->update([
                'status' => FinancialStatusEnum::RECEIVED->value,
                'received_date' => Carbon::now()->format('Y-m-d')
            ]);
        } elseif ($totalReceived > 0) {
            $receivable->update([
                'status' => FinancialStatusEnum::PARTIALLY_PAID->value,
            ]);
        }

        return $receivable->refresh();
    }
}
