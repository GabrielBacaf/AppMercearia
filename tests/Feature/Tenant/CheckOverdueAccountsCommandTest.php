<?php

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Enums\FinancialStatusEnum;
use App\Notifications\PaymentDueTomorrowNotification;
use App\Notifications\PaymentDueTodayNotification;
use App\Notifications\PaymentOverdueNotification;
use App\Notifications\ReceivableDueTomorrowNotification;
use App\Notifications\ReceivableDueTodayNotification;
use App\Notifications\ReceivableOverdueNotification;

class CheckOverdueAccountsCommandTest extends TestCase
{
    protected bool $initializeTenancy = false;

    public function test_it_dispatches_correct_notifications_and_updates_status()
    {
        Notification::fake();

        // Fix the current date to ensure predictable testing
        $now = Carbon::create(2026, 7, 25, 12, 0, 0);
        Carbon::setTestNow($now);

        $tomorrow = $now->copy()->addDay()->format('Y-m-d');
        $today = $now->format('Y-m-d');
        $yesterday = $now->copy()->subDay()->format('Y-m-d');
        $future = $now->copy()->addDays(5)->format('Y-m-d');

        $tenantId = 'test-tenant-' . uniqid();
        $tenant = Tenant::create(['id' => $tenantId]);
        $tenant->domains()->create(['domain' => $tenantId . '.test']);

        $adminUser = null;
        $payableTomorrow = null;
        $payableToday = null;
        $payableYesterday = null;
        $payableFuture = null;
        
        $receivableTomorrow = null;
        $receivableToday = null;
        $receivableYesterday = null;

        $tenant->run(function () use (
            &$adminUser,
            &$payableTomorrow, &$payableToday, &$payableYesterday, &$payableFuture,
            &$receivableTomorrow, &$receivableToday, &$receivableYesterday,
            $tomorrow, $today, $yesterday, $future
        ) {
            // Admin user (first user)
            $adminUser = User::factory()->create([
                'email' => 'admin@test.com'
            ]);

            // --- ACCOUNTS PAYABLE ---
            $payableTomorrow = AccountPayable::factory()->create([
                'due_date' => $tomorrow,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            $payableToday = AccountPayable::factory()->create([
                'due_date' => $today,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            $payableYesterday = AccountPayable::factory()->create([
                'due_date' => $yesterday,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            $payableFuture = AccountPayable::factory()->create([
                'due_date' => $future,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            // --- ACCOUNTS RECEIVABLE ---
            $receivableTomorrow = AccountReceivable::factory()->create([
                'due_date' => $tomorrow,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            $receivableToday = AccountReceivable::factory()->create([
                'due_date' => $today,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            $receivableYesterday = AccountReceivable::factory()->create([
                'due_date' => $yesterday,
                'status' => FinancialStatusEnum::PENDING->value,
            ]);

            // Run the command inside the tenant context
            Artisan::call('app:check-overdue-accounts');
        });

        // Assert Notifications were sent to the admin user
        Notification::assertSentTo(
            $adminUser,
            PaymentDueTomorrowNotification::class,
            fn ($notification) => $notification->payable->id === $payableTomorrow->id
        );

        Notification::assertSentTo(
            $adminUser,
            PaymentDueTodayNotification::class,
            fn ($notification) => $notification->payable->id === $payableToday->id
        );

        Notification::assertSentTo(
            $adminUser,
            PaymentOverdueNotification::class,
            fn ($notification) => $notification->payable->id === $payableYesterday->id
        );
        
        // Assert no notification for future payable
        Notification::assertNotSentTo(
            $adminUser,
            PaymentDueTomorrowNotification::class,
            fn ($notification) => $notification->payable->id === $payableFuture->id
        );

        // Receivables Notifications
        Notification::assertSentTo(
            $adminUser,
            ReceivableDueTomorrowNotification::class,
            fn ($notification) => $notification->receivable->id === $receivableTomorrow->id
        );

        Notification::assertSentTo(
            $adminUser,
            ReceivableDueTodayNotification::class,
            fn ($notification) => $notification->receivable->id === $receivableToday->id
        );

        Notification::assertSentTo(
            $adminUser,
            ReceivableOverdueNotification::class,
            fn ($notification) => $notification->receivable->id === $receivableYesterday->id
        );

        // Assert DB status updates
        $tenant->run(function () use ($payableYesterday, $payableTomorrow, $receivableYesterday) {
            $this->assertEquals(FinancialStatusEnum::OVERDUE->value, $payableYesterday->fresh()->status->value ?? $payableYesterday->fresh()->status);
            $this->assertEquals(FinancialStatusEnum::OVERDUE->value, $receivableYesterday->fresh()->status->value ?? $receivableYesterday->fresh()->status);
            
            // Tomorrow should still be pending
            $this->assertEquals(FinancialStatusEnum::PENDING->value, $payableTomorrow->fresh()->status->value ?? $payableTomorrow->fresh()->status);
        });
    }
}
