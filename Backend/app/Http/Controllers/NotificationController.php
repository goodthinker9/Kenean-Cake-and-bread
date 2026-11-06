<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\AppSetting;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Notification::latest('created_at');

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        $notifications = $query->get()->map(function ($n) {
            return [
                'notification_id' => $n->notification_id,
                'category' => $n->category,
                'title' => $n->title,
                'message' => $n->message,
                'link' => $n->link,
                'is_read' => (bool) $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(null, true) . ' ago',
            ];
        });

        $alertRules = [
            'low_stock_goes_to' => AppSetting::get('low_stock_notification', 'Branch Admin and Main Manager'),
            'unreceived_reminder' => AppSetting::get('unreceived_transfer_reminder', 'After 24 hours'),
            'daily_summary' => AppSetting::get('daily_summary_email', 'Owner, 20:00'),
        ];

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('is_read', false)->count(),
            'alert_rules' => $alertRules,
        ]);
    }

    public function markAllRead(Request $request)
    {
        Notification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function saveAlertRules(Request $request)
    {
        $validated = $request->validate([
            'low_stock_goes_to' => 'required|string',
            'unreceived_reminder' => 'required|string',
            'daily_summary' => 'required|string',
        ]);

        AppSetting::set('low_stock_notification', $validated['low_stock_goes_to'], 'alerts');
        AppSetting::set('unreceived_transfer_reminder', $validated['unreceived_reminder'], 'alerts');
        AppSetting::set('daily_summary_email', $validated['daily_summary'], 'alerts');

        return response()->json(['message' => 'Alert rules updated successfully']);
    }
}
