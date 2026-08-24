<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\AssignInstanceToGroupRequest;
use App\Http\Requests\Group\AssignUserToGroupRequest;
use App\Http\Requests\Group\DeleteGroupRequest;
use App\Http\Requests\Group\IndexGroupRequest;
use App\Http\Requests\Group\IndexTransactionByGroupIdRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\Group\GroupCollection;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Services\Group\GroupService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Group
 */
class GroupController extends Controller
{
    public function __construct(protected GroupService $groupService) {}

    /**
     * Index a user transaction group
     * 
     * 
     */
    public function index(IndexGroupRequest $request)
    {
        $data = $this->groupService->index($request->validated());
        return ApiResponse::success(
            new GroupCollection($data),
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
        $data = $this->groupService->store($request->validated());
        return ApiResponse::success(
            new GroupResource($data),
            'Transaction group was created with success!',
            201
        );
    }

    /**
     * List the transactions of a group
     */
    public function indexTransactionByGroupId(IndexTransactionByGroupIdRequest $request): JsonResponse
    {
        $data = $this->groupService->indexTransactionByGroupId($request->validated());
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
    public function destroy(DeleteGroupRequest $request): JsonResponse
    {
        $this->groupService->destroy($request->validated());
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
    public function update(UpdateGroupRequest $request): JsonResponse
    {
        $data = $this->groupService->update($request->validated());
        return ApiResponse::success(
            new GroupResource($data),
            'Transaction group was updated with success!',
            200
        );
    }

    /**
     * Add a participant to the group
     *
     * Attaches a user to the group so the amount is split with them.
     */
    public function assignParticipant(AssignUserToGroupRequest $request): JsonResponse
    {
        $this->groupService->assignParticipant($request->validated());
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
        $this->groupService->assignInstanceToGroup($request->validated());
        return ApiResponse::success(
            null,
            'Instance was assigned to group with success!',
            200
        );
    }
}
