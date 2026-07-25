<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FinancialStatusEnum;

class AccountReceivable extends Model
{
    use HasFactory;

    protected $table = 'accounts_receivable';

    protected $fillable = [
        'title',
        'amount',
        'due_date',
        'received_date',
        'status',
        'client_id',
        'receivable_id',
        'receivable_type',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'received_date' => 'date',
            'status' => FinancialStatusEnum::class,
        ];
    }

    public function receivable(): MorphTo
    {
        return $this->morphTo();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
