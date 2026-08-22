<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TransactionDashboardRequest;
use App\Services\Dashboard\GroupTransactionDashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TransactionDashboardController extends Controller
{
    public function __construct(
        protected GroupTransactionDashboardService $groupTransactionDashboardService
    ) {}

    public function groupDashboard(TransactionDashboardRequest $request)
    {
        $data = $this->groupTransactionDashboardService->dashboard($request->validated());
        return ApiResponse::success(
            $data,
            'Dashboard was indexed with success!',
            200
        );
    }
}
