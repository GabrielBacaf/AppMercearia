<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\FinancialStatusEnum;
use App\Enums\AccountPayableTypeEnum;

class AccountPayable extends Model
{
    use HasFactory;

    protected $table = 'accounts_payable';

    protected $fillable = [
        'title',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'type',
        'payable_id',
        'payable_type',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_date' => 'date',
            'status' => FinancialStatusEnum::class,
            'type' => AccountPayableTypeEnum::class,
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function scopePending($query)
    {
        return $query->where('status', FinancialStatusEnum::PENDING);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', FinancialStatusEnum::OVERDUE);
    }
}
