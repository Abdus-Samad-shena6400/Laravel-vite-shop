<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Notification::where(
            'user_id',
            auth()->id()
        )
        ->latest()
        ->get();
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    public function clearAll()
{
    Notification::where(
        'user_id',
        auth()->id()
    )->delete();

    return response()->json([
        'message' => 'All notifications deleted successfully.'
    ]);
}

    public function destroy(Notification $notification)
{
    // Security check
    if ($notification->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $notification->delete();

    return response()->json([
        'message' => 'Notification deleted successfully.'
    ]);
}
}