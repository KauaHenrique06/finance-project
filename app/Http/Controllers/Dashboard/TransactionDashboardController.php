<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TransactionDashboardRequest;
use App\Services\Dashboard\CampaignDashboardService;
use App\Services\Dashboard\GroupDashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TransactionDashboardController extends Controller
{
    public function __construct(
        protected GroupDashboardService $groupDashboardService,
        protected CampaignDashboardService $campaignDashboardService
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

    public function campaignDashboard()
    {
        $data = $this->campaignDashboardService->dashboard();
        return ApiResponse::success(
            $data,
            'Dashboard was indexed with success!',
            200
        );
    }
}
