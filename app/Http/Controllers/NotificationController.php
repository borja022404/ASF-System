<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    // Show notifications page
    public function index()
    {
        $notifications = Notification::where('receiver_id', Auth::id())
            ->latest()
            ->paginate(10);

        $view = match (Auth::user()->roles[0]->name) {
            'admin' => 'admin.notification',
            'vet' => 'vet.notification',
            default => 'farmer.notification',
        };

        return view($view, compact('notifications'));
    }

    // Mark a single notification as read
    public function markAsRead($id)
    {
        try {
            Log::info('=== MARK AS READ REQUEST ===', [
                'notification_id' => $id,
                'user_id' => Auth::id(),
                'method' => request()->method(),
                'is_ajax' => request()->ajax(),
            ]);

            // Find the notification
            $notif = Notification::where('id', $id)
                ->where('receiver_id', Auth::id())
                ->first();

            if (!$notif) {
                Log::error('Notification not found or not authorized', [
                    'notification_id' => $id,
                    'user_id' => Auth::id(),
                ]);
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Notification not found or you are not authorized',
                    ], 404);
                }
                return back()->with('error', 'Notification not found');
            }

            Log::info('Notification found', [
                'id' => $notif->id,
                'read_at' => $notif->read_at ? $notif->read_at->toDateTimeString() : 'null',
            ]);

            $wasAlreadyRead = !is_null($notif->read_at);

            if (!$wasAlreadyRead) {
                // Attempt to update read_at
                $updated = $notif->update(['read_at' => now()]);
                $notif->refresh(); // Reload to confirm update

                if ($updated && $notif->read_at) {
                    Log::info('Notification marked as read', [
                        'id' => $notif->id,
                        'new_read_at' => $notif->read_at->toDateTimeString(),
                    ]);
                } else {
                    Log::error('Failed to update read_at', [
                        'id' => $notif->id,
                        'new_read_at' => $notif->read_at ? $notif->read_at->toDateTimeString() : 'null',
                    ]);
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to mark notification as read',
                        ], 500);
                    }
                    return back()->with('error', 'Failed to mark notification as read');
                }
            } else {
                Log::info('Notification already read', ['id' => $notif->id]);
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read',
                    'url' => $notif->url ?? '#',
                    'notification_id' => $notif->id,
                    'was_already_read' => $wasAlreadyRead,
                ]);
            }

            if ($notif->url && $notif->url !== '#') {
                return redirect($notif->url);
            }

            return redirect()->route('notifications.index');
        } catch (\Exception $e) {
            Log::error('=== MARK AS READ ERROR ===', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Error marking notification as read');
        }
    }
    // Mark all notifications as read
    public function markAllAsRead()
    {
        try {
            Log::info('=== MARK ALL AS READ REQUEST ===');
            Log::info('User ID: ' . Auth::id());

            $updated = Notification::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            Log::info('Updated ' . $updated . ' notifications at: ' . now());

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All notifications marked as read',
                    'updated_count' => $updated
                ]);
            }

            return back()->with('success', "All {$updated} notifications marked as read.");

        } catch (\Exception $e) {
            Log::error('=== MARK ALL AS READ ERROR ===');
            Log::error('Error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to mark all notifications as read');
        }
    }

    // Return unread count (for AJAX badge updates)
    public function unreadCount()
    {
        try {
            $count = Notification::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'unread_count' => $count,
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching unread count: ' . $e->getMessage());
            return response()->json([
                'unread_count' => 0,
                'success' => false,
                'message' => 'Failed to fetch notification count'
            ], 500);
        }
    }

    // Get latest notifications for dropdown (AJAX)
    public function getLatest()
    {
        try {
            $notifications = Notification::where('receiver_id', Auth::id())
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'data' => \Str::limit($notif->data, 80),
                        'url' => $notif->url,
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at->diffForHumans(),
                        'is_unread' => is_null($notif->read_at)
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching latest notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }
}