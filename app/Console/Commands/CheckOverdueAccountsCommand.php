<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueAccountsCommand extends Command
{
    protected $signature = 'app:check-overdue-accounts';
    protected $description = 'Check and mark accounts as overdue, triggering notifications';

    public function handle()
    {
        $admin = \App\Models\User::first();
        if (!$admin) {
            return;
        }

        $this->processAccounts(
            \App\Models\AccountPayable::query(),
            $admin,
            [
                'tomorrow' => \App\Notifications\PaymentDueTomorrowNotification::class,
                'today'    => \App\Notifications\PaymentDueTodayNotification::class,
                'overdue'  => \App\Notifications\PaymentOverdueNotification::class,
            ]
        );

        $this->processAccounts(
            \App\Models\AccountReceivable::query(),
            $admin,
            [
                'tomorrow' => \App\Notifications\ReceivableDueTomorrowNotification::class,
                'today'    => \App\Notifications\ReceivableDueTodayNotification::class,
                'overdue'  => \App\Notifications\ReceivableOverdueNotification::class,
            ]
        );
    }

    private function processAccounts($query, $admin, array $notifications)
    {
        $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');

        $accounts = $query->whereIn('status', [
                \App\Enums\FinancialStatusEnum::PENDING->value,
                \App\Enums\FinancialStatusEnum::PARTIALLY_PAID->value
            ])
            ->whereDate('due_date', '<=', $tomorrow)
            ->get();

        foreach ($accounts as $account) {
            $dueDate = $account->due_date->format('Y-m-d');

            $type = match (true) {
                $dueDate === $tomorrow => 'tomorrow',
                $dueDate === $today => 'today',
                $dueDate <= $yesterday => 'overdue',
                default => null,
            };

            if ($type) {
                if ($type === 'overdue') {
                    $account->update(['status' => \App\Enums\FinancialStatusEnum::OVERDUE->value]);
                }

                $admin->notify(new $notifications[$type]($account));
            }
        }
    }
}
