<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Support\Ownership;
use Auth;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function index(array $data)
    {
        $authUserId = Auth::id();
        return Notification::where('user_id', $authUserId)->get();
    }

    public function markAllNotificationAsRead()
    {
        $authUserId = Auth::id();
        $notifications = Notification::where('user_id', $authUserId)->get();

        return DB::transaction(function() use ($notifications) {
            $notifications->map(function($notification) {
                $notification->update([
                    'read_at' => now()
                ]);
            });
        });
    }

    public function markNotificationAsRead(array $data)
    {
        $notification = Notification::findOrFail($data['id']);
        Ownership::verify($notification->user_id, "You can't mark this notification as read!");

        DB::transaction(function() use ($notification) {
            $notification->update(['read_at' => now()]);
        });
    }
}
