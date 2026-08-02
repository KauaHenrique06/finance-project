<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StorePermissionToRoleRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Role\RoleService;
use App\Support\ApiResponse;

class RoleController extends Controller
{

    public function __construct(protected RoleService $roleService) {}

    public function index() {

        $data = $this->roleService->index();
        return ApiResponse::success(
            RoleResource::collection($data),
            'Roles indexed with success!',
            200
        );
    }

    public function store(StoreRoleRequest $request) {

        $data = $this->roleService->store($request->validated());
        return ApiResponse::success(
            new RoleResource($data),
            'Role created with success!',
            201
        );
    }

    public function show(Role $role) {

        $data = $this->roleService->show($role);
        return ApiResponse::success(
            new RoleResource($data),
            'Role indexed with success!',
            200
        );
    }

    public function update(UpdateRoleRequest $request, Role $role) {

        $data = $this->roleService->update($request->validated(), $role);
        return ApiResponse::success(
            new RoleResource($data),
            'Role updated with success!',
            200
        );
    }

    public function destroy(Role $role) {

        $this->roleService->destroy($role);
        return ApiResponse::success(
            null,
            'Role deleted with success!',
            200
        );
    }

    public function storePermissionToRole(Role $role, StorePermissionToRoleRequest $request) {

        $data = $this->roleService->storePermissionToRole($request->validated(), $role);
        return ApiResponse::success(
            new RoleResource($data),
            'Permissions assigned to role with success!',
            200
        );
    }

    public function storeRoleToUser(User $user, Role $role) {

        $data = $this->roleService->storeRoleToUser($user, $role);
        return ApiResponse::success(
            new AuthResource($data),
            'Role assigned for user with success!',
            200
        );
    }

}
