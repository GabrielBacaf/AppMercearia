<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Auth;

class PurchaseObserver
{
    /**
     * Handle the Purchase "creating" event.
     */
    public function creating(Purchase $purchase): void
    {
        $purchase->status = StatusEnum::PENDENTE->value;

        if (Auth::check() && !$purchase->user_id) {
            $purchase->user_id = Auth::id();
        }
    }

    /**
     * Handle the Purchase "updating" event.
     */
    public function updating(Purchase $purchase): void
    {
        if (Auth::check()) {
            $purchase->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Purchase "deleting" event.
     */
    public function deleting(Purchase $purchase): void
    {
        $purchase->documents()->each(function ($document) {
            $document->delete();
        });
    }
}
