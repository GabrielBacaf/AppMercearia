<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountReceivableRequest;
use App\Http\Requests\SettleAccountRequest;
use App\Http\Services\AccountReceivableService;
use App\Models\AccountReceivable;
use Illuminate\Http\Request;

class AccountReceivableController extends Controller
{
    public function __construct(protected AccountReceivableService $receivableService) {}

    public function index(Request $request)
    {
        $query = AccountReceivable::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        return response()->json($query->with('client')->get());
    }

    public function store(StoreAccountReceivableRequest $request)
    {
        $receivable = $this->receivableService->createStandalone($request->validated());

        return response()->json($receivable, 201);
    }

    public function settle(SettleAccountRequest $request, AccountReceivable $receivable)
    {
        $settledReceivable = $this->receivableService->settle($receivable, $request->validated());

        return response()->json($settledReceivable);
    }
}
