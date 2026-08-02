<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionsRequest;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use App\Services\Permission\PermissionService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService) {}

    public function index() {

        $data = $this->permissionService->index();
        return ApiResponse::success(
            PermissionResource::collection($data),
            'Permissions indexed with success!',
            200
        );

    }

    public function store(StorePermissionRequest $request) {

        $data = $this->permissionService->store($request->validated());
        return ApiResponse::success(
            new PermissionResource($data),
            'Permission created with success!',
            201
        );
    }

    public function show(Permission $permission) {

        $data = $this->permissionService->show($permission);
        return ApiResponse::success(
            new PermissionResource($data),
            'Permission indexed with success!',
            200
        );

    }

    public function update(UpdatePermissionsRequest $request, Permission $permission) {

        $data = $this->permissionService->update($request->validated(), $permission);
        return ApiResponse::success(
            new PermissionResource($data),
            'Permission updated with success!',
            200
        );

    }

    public function destroy(Permission $permission) {

        $this->permissionService->destroy($permission);
        return ApiResponse::success(
            null,
            'Permission deleted with success!',
            200
        );

    }
}
