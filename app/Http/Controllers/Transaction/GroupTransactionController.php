<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\AssignInstanceToGroupRequest;
use App\Http\Requests\Transaction\AssignUserToTransactionGroupRequest;
use App\Http\Requests\Transaction\DeleteTransactionGroupRequest;
use App\Http\Requests\Transaction\IndexTransactionByGroupIdRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Services\Transaction\GroupTransactionService;
use App\Support\ApiResponse;

class GroupTransactionController extends Controller
{
    public function __construct(protected GroupTransactionService $groupTransactionService) {}

    public function store(StoreTransactionRequest $request)
    {
        $data = $this->groupTransactionService->store($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transaction group was created with success!',
            201
        );
    }

    public function show(IndexTransactionByGroupIdRequest $request)
    {
        $data = $this->groupTransactionService->show($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transactions was indexed with success!',
            200
        );
    }

    public function destroy(DeleteTransactionGroupRequest $request)
    {
        $this->groupTransactionService->destroy($request->validated());
        return ApiResponse::success(
            null,
            'Transaction group was deleted with success!',
            200
        );
    }

    public function assignParticipant(AssignUserToTransactionGroupRequest $request)
    {
        $this->groupTransactionService->assignParticipant($request->validated());
        return ApiResponse::success(
            null,
            'User was assigned to group with success!',
            200
        );
    }

    public function assignInstanceToGroup(AssignInstanceToGroupRequest $request)
    {
        $this->groupTransactionService->assignInstanceToGroup($request->validated());
        return ApiResponse::success(
            null,
            'Instance was assigned to group with success!',
            200
        );
    }
}
