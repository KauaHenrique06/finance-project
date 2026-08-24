<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TransactionDashboardRequest;
use App\Services\Dashboard\GroupDashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TransactionDashboardController extends Controller
{
    public function __construct(
        protected GroupDashboardService $groupDashboardService
    ) {}

    public function groupDashboard(TransactionDashboardRequest $request)
    {
        $data = $this->groupDashboardService->dashboard($request->validated());
        return ApiResponse::success(
            $data,
            'Dashboard was indexed with success!',
            200
        );
    }
}
