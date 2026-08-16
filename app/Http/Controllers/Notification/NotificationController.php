<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\IndexNotificarionRequest;
use App\Http\Requests\Notification\MarkNotificarionAsReadRequest;
use App\Http\Resources\Notification\NotificationResource;
use App\Services\Notification\NotificationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Notification
 */
class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * List notifications
     *
     * Returns every notification that belongs to the authenticated user.
     */
    public function index(IndexNotificarionRequest $request): JsonResponse
    {
        $data = $this->notificationService->index($request->validated());
        return ApiResponse::success(
            NotificationResource::collection($data),
            'Notifications was indexed with success!',
            200
        );
    }

    /**
     * Mark every notification as read
     */
    public function markAllNotificationAsRead(): JsonResponse
    {
        $this->notificationService->markAllNotificationAsRead();
        return ApiResponse::success(
            null,
            'Notifications was readed with success!',
            200
        );
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationAsRead(MarkNotificarionAsReadRequest $request): JsonResponse
    {
        $this->notificationService->markNotificationAsRead($request->validated());
        return ApiResponse::success(
            null,
            'Notification was readed with success!',
            200
        );
    }
}
