<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($notifications, 200);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'array',
            'notification_ids.*' => 'exists:notifications,id,user_id,' . Auth::id()
        ]);

        Notification::whereIn('id', $request->notification_ids)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Notifications marquées comme lues'], 200);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues'], 200);
    }
}