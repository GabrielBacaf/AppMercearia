<?php

namespace App\Models;

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
        'value',
        'purchase_date',
    ];

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
        return $this->belongsToMany(Product::class, 'purchase_item', 'purchase_id', 'product_id')
            ->withPivot('amount', 'purchase_value')
            ->withTimestamps();
    }
}
