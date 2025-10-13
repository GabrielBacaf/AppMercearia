<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'value',
        'payment_status',
        'payable_id',
        'payment_type',
        'payable'
    ];
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Sincroniza (cria, atualiza, deleta) os pagamentos para um modelo pai.
     *
     * @param Model $parent O objeto pai (ex: $purchase, $sale)
     * @param array $paymentsData O array de dados dos pagamentos
     */

    public static function syncPayments(Model $model, array $paymentsData): void
    {
        $incomingIds = array_filter(Arr::pluck($paymentsData, 'id'));

        if (count($incomingIds) > 0) {
            $model->payments()->whereNotIn('id', $incomingIds)->delete();
        } else {
            $model->payments()->delete();
        }

        foreach ($paymentsData as $paymentData) {
            $model->payments()->updateOrCreate(
                [
                    'id' => $paymentData['id'] ?? null
                ],
                $paymentData
            );
        }
    }
}
