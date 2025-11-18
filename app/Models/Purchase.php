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


    /**
     * se for um crete ele deixa por padrão o status pendente.
     * se for update ele só autaliza o usuario
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($purchase) {

            $purchase->status = StatusEnum::PENDENTE->value;

            if (Auth::check() && !$purchase->user_id) {
                $purchase->user_id = Auth::id();
            }

        });

        static::updating(function ($purchase) {
            if (Auth::check()) {
                $purchase->updated_by = Auth::id();
            }
        });

        // Quando uma Purchase for deletada...
        static::deleting(function ($purchase) {
            // ...pegue todos os documentos e delete CADA UM.
            $purchase->documents()->each(function ($document) {
                $document->delete();
            });
        });
    }


    public function updateStatus(): void
    {

        $pivotData = $this->products()->get(['product_purchase.purchase_value', 'product_purchase.amount']);

        $totalCostOfProducts = $pivotData->sum(function ($pivot) {
            return $pivot->purchase_value * $pivot->amount;
        });


        $totalPaid = $this->payments()->sum('value');

        $this->count_value = $totalPaid - $totalCostOfProducts;

        $hasPendingPayments = $this->payments()->where('payment_status', PaymentStatusEnum::DEVENDO->value)->exists();

        if ($this->count_value == 0 && !$hasPendingPayments) {

            $this->status = StatusEnum::FINALIZADO->value;
        } elseif ($this->count_value == 0 && $hasPendingPayments) {

            $this->status = StatusEnum::PAGAMENTO_PENDENTE->value;
        } elseif ($this->count_value < 0) {

            $this->status = StatusEnum::ERRO_ESTOQUE->value;
        } elseif ($this->count_value > 0) {

            $this->status = StatusEnum::PENDENTE->value;
        }

        $this->save();
    }
}
