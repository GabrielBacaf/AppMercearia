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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount', 'purchase_value')
            ->withTimestamps();
    }



    public function updateStatus(): void
    {
        $totalItemValue = $this->products->sum(fn($product) =>

        $product->pivot->purchase_value * $product->pivot->amount);

        $totalPayments = $this->payments()->sum('value');

        $this->count_value = $totalPayments - $totalItemValue;

        $hasPendingPayments = $this->payments()->where('payment_status', PaymentStatusEnum::DEVENDO->value)->exists();

        if ($this->count_value < 0) {
            $this->status = StatusEnum::ERRORESTOQUE->value;
        } elseif ($this->count_value == 0 && !$hasPendingPayments) {
            $this->status = StatusEnum::FINALIZADO->value;
        } else {
        }

        $this->save();
    }
}
