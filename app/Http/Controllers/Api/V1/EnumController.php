<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CategoryEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Http\JsonResponse;

class EnumController
{
    public function index(): JsonResponse
    {
        $mapEnum = function (string $enumClass) {
            return array_map(function ($case) {
                return [
                    'value' => $case->value,
                    'name' => $case->name,
                ];
            }, $enumClass::cases());
        };

        return response()->json([
            'categories' => $mapEnum(CategoryEnum::class),
            'payment_statuses' => $mapEnum(PaymentStatusEnum::class),
            'payment_types' => $mapEnum(PaymentTypeEnum::class),
            'statuses' => $mapEnum(StatusEnum::class),
        ]);
    }
}
