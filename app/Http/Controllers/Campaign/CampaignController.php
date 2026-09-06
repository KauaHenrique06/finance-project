<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\ContributeCampaignRequest;
use App\Http\Requests\Campaign\DeleteCampaignRequest;
use App\Http\Requests\Campaign\IndexCampaignRequest;
use App\Http\Requests\Campaign\ShowCampaignRequest;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\Campaign\CampaignCollection;
use App\Http\Resources\Campaign\CampaignResource;
use App\Services\Campaign\CampaignService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(protected CampaignService $campaignService) {}

    public function index(IndexCampaignRequest $request) 
    {
        $data = $this->campaignService->index($request->validated());
        return ApiResponse::success(
            new CampaignCollection($data),
            'Campaigns was indexed with success!',
            200
        );
    }

    public function store(StoreCampaignRequest $request) 
    {
        $data = $this->campaignService->store($request->validated());
        return ApiResponse::success(
            new CampaignResource($data),
            'Campaign was created with success!',
            201
        );
    }

    public function show(ShowCampaignRequest $request) 
    {
        $data = $this->campaignService->show($request->validated());
        return ApiResponse::success(
            new CampaignResource($data),
            'Campaign was indexed with success!',
            200
        );
    }

    public function update(UpdateCampaignRequest $request) 
    {
        $data = $this->campaignService->update($request->validated());
        return ApiResponse::success(
            new CampaignResource($data),
            'Campaign was updated with success!',
            200
        );
    }

    public function destroy(DeleteCampaignRequest $request) 
    {
        $this->campaignService->destroy($request->validated());
        return ApiResponse::success(
            null,
            'Campaign was deleted with success!',
            200
        );
    }

    public function contribute(ContributeCampaignRequest $request)
    {
        $this->campaignService->contribute($request->validated());
        return ApiResponse::success(
            null,
            'Contribute send with success!',
            200
        );
    }
}
