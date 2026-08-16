<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\PermissionResource;
use App\Services\Permission\PermissionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Permission
 */
class PermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService) {}

    /**
     * List permissions
     *
     * Read-only listing, meant for development. Permissions are created by
     * the `PermissionSeeder`, not through the API.
     */
    public function index(): JsonResponse
    {
        $data = $this->permissionService->index();
        return ApiResponse::success(
            PermissionResource::collection($data),
            'Permissions indexed with success!',
            200
        );
    }
}
