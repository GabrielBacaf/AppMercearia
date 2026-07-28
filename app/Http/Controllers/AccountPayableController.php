<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\SettleAccountRequest;
use App\Http\Services\AccountPayableService;
use App\Models\AccountPayable;
use Illuminate\Http\Request;

class AccountPayableController extends Controller
{
    public function __construct(protected AccountPayableService $payableService) {}

    public function index(Request $request)
    {
        $query = AccountPayable::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        return response()->json($query->get());
    }

    public function store(StoreAccountPayableRequest $request)
    {
        $payable = $this->payableService->createStandalone($request->validated());

        return response()->json($payable, 201);
    }

    public function settle(SettleAccountRequest $request, AccountPayable $payable)
    {
        $settledPayable = $this->payableService->settle($payable, $request->validated());

        return response()->json($settledPayable);
    }
}
