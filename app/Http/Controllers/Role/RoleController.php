<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleResource;
use App\Services\Role\RoleService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Role
 */
class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}

    /**
     * List roles
     *
     * Read-only listing, meant for development. Roles are created by the
     * `RoleSeeder`, not through the API.
     */
    public function index(): JsonResponse
    {
        $data = $this->roleService->index();
        return ApiResponse::success(
            RoleResource::collection($data),
            'Roles indexed with success!',
            200
        );
    }
}
