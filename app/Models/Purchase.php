<?php

namespace App\Models;

use App\Casts\ConvertDateToBrCast;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\PurchaseObserver;
use App\Http\Services\PurchaseStatusResolver;

#[ObservedBy([PurchaseObserver::class])]
class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'supplier_id',
        'invoice_id',
        'user_id',
        'count_value',
        'status',
        'purchase_date',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => ConvertDateToBrCast::class,
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accountsPayable(): MorphMany
    {
        return $this->morphMany(AccountPayable::class, 'payable');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount', 'purchase_value', 'expiration_date')
            ->withTimestamps();
    }

    public function updateStatus(): void
    {
        $pivotData = $this->products()->get(['product_purchase.purchase_value', 'product_purchase.amount']);

        $totalCostOfProducts = $pivotData->sum(function ($pivot) {
            return $pivot->purchase_value * $pivot->amount;
        });

        $totalPaid = $this->accountsPayable()->with('payments')->get()->pluck('payments')->flatten()->sum('value');

        $this->count_value = $totalPaid - $totalCostOfProducts;

        $hasPendingPayments = $this->accountsPayable()->whereIn('status', [
            \App\Enums\FinancialStatusEnum::PENDING->value,
            \App\Enums\FinancialStatusEnum::OVERDUE->value
        ])->exists();

        $this->status = PurchaseStatusResolver::resolve($this->count_value, $hasPendingPayments);

        $this->save();
    }
}
