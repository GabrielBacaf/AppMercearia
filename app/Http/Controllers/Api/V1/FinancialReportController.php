<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Http\Requests\Api\V1\Financial\FinancialReportRequest;

class FinancialReportController extends Controller
{
    public function __construct(private FinancialReportService $financialReportService)
    {
    }

    public function dashboard(FinancialReportRequest $request): JsonResponse
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $report = $this->financialReportService->getDashboardReport($month, $year);

        return $this->successResponse($report, 'Relatório listado com sucesso!', 200);
    }
}
