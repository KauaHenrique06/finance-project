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
use Illuminate\Http\JsonResponse;

/**
 * @tags GroupTransaction
 */
class GroupTransactionController extends Controller
{
    public function __construct(protected GroupTransactionService $groupTransactionService) {}

    /**
     * Create a transaction group
     *
     * Splits the total amount across the instalments and returns the
     * transactions that were generated.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $this->groupTransactionService->store($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transaction group was created with success!',
            201
        );
    }

    /**
     * List the transactions of a group
     */
    public function show(IndexTransactionByGroupIdRequest $request): JsonResponse
    {
        $data = $this->groupTransactionService->show($request->validated());
        return ApiResponse::success(
            new TransactionCollection($data),
            'Transactions was indexed with success!',
            200
        );
    }

    /**
     * Delete a transaction group
     *
     * Removes the group along with every transaction attached to it.
     */
    public function destroy(DeleteTransactionGroupRequest $request): JsonResponse
    {
        $this->groupTransactionService->destroy($request->validated());
        return ApiResponse::success(
            null,
            'Transaction group was deleted with success!',
            200
        );
    }

    /**
     * Add a participant to the group
     *
     * Attaches a user to the group so the amount is split with them.
     */
    public function assignParticipant(AssignUserToTransactionGroupRequest $request): JsonResponse
    {
        $this->groupTransactionService->assignParticipant($request->validated());
        return ApiResponse::success(
            null,
            'User was assigned to group with success!',
            200
        );
    }

    /**
     * Attach a WhatsApp instance to the group
     *
     * Defines which instance sends the reminders for this group.
     */
    public function assignInstanceToGroup(AssignInstanceToGroupRequest $request): JsonResponse
    {
        $this->groupTransactionService->assignInstanceToGroup($request->validated());
        return ApiResponse::success(
            null,
            'Instance was assigned to group with success!',
            200
        );
    }
}
