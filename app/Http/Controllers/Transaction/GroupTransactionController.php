<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\AssignInstanceToGroupRequest;
use App\Http\Requests\Transaction\AssignUserToTransactionGroupRequest;
use App\Http\Requests\Transaction\DeleteTransactionGroupRequest;
use App\Http\Requests\Transaction\IndexGroupTransactionRequest;
use App\Http\Requests\Transaction\IndexTransactionByGroupIdRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionGroupRequest;
use App\Http\Resources\Transaction\GroupTransactionCollection;
use App\Http\Resources\Transaction\GroupTransactionResource;
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
     * Index a user transaction group
     * 
     * 
     */
    public function index(IndexGroupTransactionRequest $request)
    {
        $data = $this->groupTransactionService->index($request->validated());
        return ApiResponse::success(
            new GroupTransactionCollection($data),
            'Transactions group was indexed with success!',
            200
        );
    }

    /**
     * Create a transaction group
     *
     * Splits the total amount across the instalments and returns the group
     * with the transactions that were generated.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $this->groupTransactionService->store($request->validated());
        return ApiResponse::success(
            new GroupTransactionResource($data),
            'Transaction group was created with success!',
            201
        );
    }

    /**
     * List the transactions of a group
     */
    public function indexTransactionByGroupId(IndexTransactionByGroupIdRequest $request): JsonResponse
    {
        $data = $this->groupTransactionService->indexTransactionByGroupId($request->validated());
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
     * Update a transaction group
     *
     * Update group and your transactions when send on request. Returns the
     * group with the resulting transactions.
     */
    public function update(UpdateTransactionGroupRequest $request): JsonResponse
    {
        $data = $this->groupTransactionService->update($request->validated());
        return ApiResponse::success(
            new GroupTransactionResource($data),
            'Transaction group was updated with success!',
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
