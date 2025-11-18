<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
trait SyncPayments
{
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
