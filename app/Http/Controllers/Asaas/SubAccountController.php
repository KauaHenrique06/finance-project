<?php

namespace App\Http\Controllers\Asaas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asaas\StoreSubAccountRequest;
use App\Services\Asaas\SubAccountService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SubAccountController extends Controller
{

    public function __construct(protected SubAccountService $subAccountService) {}

    public function store(StoreSubAccountRequest $request): JsonResponse
    {
        $data = $this->subAccountService->store($request->validated());
        return ApiResponse::success(
            $data,
            'Sub account was created with success!',
            201
        );
    }
}
