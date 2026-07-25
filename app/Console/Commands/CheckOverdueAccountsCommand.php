<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueAccountsCommand extends Command
{
    protected $signature = 'app:check-overdue-accounts';
    protected $description = 'Check and mark accounts as overdue, triggering notifications';

    public function handle()
    {
        $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');

        $pendingPayables = \App\Models\AccountPayable::whereIn('status', [
                \App\Enums\FinancialStatusEnum::PENDING->value,
                \App\Enums\FinancialStatusEnum::PARTIALLY_PAID->value
            ])
            ->whereDate('due_date', '<=', $yesterday)
            ->get();

        foreach ($pendingPayables as $payable) {
            $payable->update(['status' => \App\Enums\FinancialStatusEnum::OVERDUE->value]);
            
            // Notify the super admin or store owner via user 1 (assuming tenant owner)
            $admin = \App\Models\User::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\PaymentOverdueNotification($payable));
            }
        }

        $pendingReceivables = \App\Models\AccountReceivable::whereIn('status', [
                \App\Enums\FinancialStatusEnum::PENDING->value,
                \App\Enums\FinancialStatusEnum::PARTIALLY_PAID->value
            ])
            ->whereDate('due_date', '<=', $yesterday)
            ->get();

        foreach ($pendingReceivables as $receivable) {
            $receivable->update(['status' => \App\Enums\FinancialStatusEnum::OVERDUE->value]);

            $admin = \App\Models\User::first();
            if ($admin) {
                $admin->notify(new \App\Notifications\ReceivableOverdueNotification($receivable));
            }
        }
    }
}
