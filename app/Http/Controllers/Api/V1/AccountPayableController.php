<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Account\StoreAccountPayableRequest;
use App\Http\Requests\Api\V1\Account\SettleAccountRequest;
use App\Http\Services\AccountPayableService;
use App\Models\AccountPayable;
use App\Http\Resources\V1\AccountPayable\AccountPayableResource;
use Illuminate\Http\Request;

class AccountPayableController extends Controller
{
    public function __construct(protected AccountPayableService $payableService) {}

    public function index(Request $request)
    {
        $payables = AccountPayable::latest()->paginate(5);

        return $this->successResponseCollection(
            AccountPayableResource::collection($payables),
            $payables,
            'Contas a pagar listadas com sucesso!',
            200
        );
    }

    public function store(StoreAccountPayableRequest $request)
    {
        $payable = $this->payableService->createStandalone($request->validated());

        return $this->successResponse(
            new AccountPayableResource($payable),
            'Conta a pagar criada com sucesso!',
            201
        );
    }

    public function settle(SettleAccountRequest $request, AccountPayable $payable)
    {
        $settledPayable = $this->payableService->settle($payable, $request->validated());

        return $this->successResponse(
            new AccountPayableResource($settledPayable),
            'Conta paga com sucesso!',
            200
        );
    }
}
