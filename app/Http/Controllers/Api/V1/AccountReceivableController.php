<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Account\StoreAccountReceivableRequest;
use App\Http\Requests\Api\V1\Account\SettleAccountRequest;
use App\Http\Services\AccountReceivableService;
use App\Models\AccountReceivable;
use App\Http\Resources\V1\AccountReceivable\AccountReceivableResource;
use Illuminate\Http\Request;

class AccountReceivableController extends Controller
{
    public function __construct(protected AccountReceivableService $receivableService) {}

    public function index(Request $request)
    {
        $receivables = AccountReceivable::with('client')->latest()->paginate(5);

        return $this->successResponseCollection(
            AccountReceivableResource::collection($receivables),
            $receivables,
            'Contas a receber listadas com sucesso!',
            200
        );
    }

    public function store(StoreAccountReceivableRequest $request)
    {
        $receivable = $this->receivableService->createStandalone($request->validated());

        return $this->successResponse(
            new AccountReceivableResource($receivable),
            'Conta a receber criada com sucesso!',
            201
        );
    }

    public function settle(SettleAccountRequest $request, AccountReceivable $receivable)
    {
        $settledReceivable = $this->receivableService->settle($receivable, $request->validated());

        return $this->successResponse(
            new AccountReceivableResource($settledReceivable),
            'Conta recebida com sucesso!',
            200
        );
    }
}
