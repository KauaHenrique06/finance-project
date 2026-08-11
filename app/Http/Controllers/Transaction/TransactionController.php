<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\AssignUserToTransactionGroupRequest;
use App\Http\Requests\Transaction\DeleteTransactionGroupRequest;
use App\Http\Requests\Transaction\IndexTransactionByGroupIdRequest;
use App\Http\Requests\Transaction\IndexTransactionGroupParticipantsRequest;
use App\Http\Requests\Transaction\MarkTransactionAsPaidRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Http\Resources\Transaction\TransactionResource;
use App\Services\Transaction\TransactionService;
use App\Support\ApiResponse;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function store(StoreTransactionRequest $request) 
    {
        $data = $this->transactionService->store($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transaction was created with success!',
            201
        );
    }

    public function markTransactionAsPaid(MarkTransactionAsPaidRequest $request)
    {
        $data = $this->transactionService->markTransactionAsPaid($request->validated());
        return ApiResponse::success(
            new TransactionResource($data),
            'Transaction mark as paid with success!',
            200
        );
    }

    public function indexTransactionByGroupId(IndexTransactionByGroupIdRequest $request)
    {
        $data = $this->transactionService->indexTransactionByGroupId($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transactions was indexed with success!',
            200
        );
    }

    public function assignUserToTransaction(AssignUserToTransactionGroupRequest $request) 
    {
        $this->transactionService->assignUserToTransaction($request->validated());
        return ApiResponse::success(
            null,
            'User was assigned to group with success!',
            200
        );
    }

    public function deleteGroup(DeleteTransactionGroupRequest $request)
    {
        $this->transactionService->deleteGroup($request->validated());
        return ApiResponse::success(
            null,
            'Transaction group was deleted with success!',
            200
        );
    }

}
