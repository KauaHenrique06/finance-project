<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\IndexNotificarionRequest;
use App\Http\Requests\Notification\MarkNotificarionAsReadRequest;
use App\Services\Notification\NotificationService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(IndexNotificarionRequest $request) 
    {
        $data = $this->notificationService->index($request->validated());
        return ApiResponse::success(
            $data,
            'Notifications was indexed with success!',
            200
        );
    }

    public function markAllNotificationAsRead() 
    {
        $this->notificationService->markAllNotificationAsRead();
        return ApiResponse::success(
            null,
            'Notifications was readed with success!',
            200
        );
    }
    
    public function markNotificationAsRead(MarkNotificarionAsReadRequest $request) 
    {
        $this->notificationService->markNotificationAsRead($request->validated());
        return ApiResponse::success(
            null,
            'Notification was readed with success!',
            200
        );
    }
}
