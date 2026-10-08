<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show the authenticated user's notifications (in-app feed).
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->paginate(15);

        return view('frontend.profile.notifications', compact('notifications'));
    }

    /**
     * Mark every unread notification as read.
     */
    public function markAllRead()
    {
        Auth::user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi ditandai terbaca');
    }

    /**
     * Mark a single notification as read (owner only).
     */
    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->route('notifications.index')
            ->with('success', 'Notifikasi ditandai terbaca');
    }
}
