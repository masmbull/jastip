<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Conversation list: one row per customer with at least one message.
     */
    public function index()
    {
        $conversations = User::query()
            ->whereHas('chatMessages')
            ->with('latestChatMessage')
            ->get()
            ->sortByDesc(fn ($u) => $u->latestChatMessage?->created_at)
            ->values();

        // Unread = customer messages not yet read by an admin.
        $unreadByUser = ChatMessage::query()
            ->where('sender', ChatMessage::SENDER_CUSTOMER)
            ->whereNull('read_at')
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return view('admin.chat.index', compact('conversations', 'unreadByUser'));
    }

    /**
     * Open a customer's thread and mark their messages read.
     */
    public function show(User $user)
    {
        $messages = $user->chatMessages()->oldest()->get();

        $user->chatMessages()
            ->where('sender', ChatMessage::SENDER_CUSTOMER)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.chat.show', compact('user', 'messages'));
    }

    /**
     * Reply to a customer's thread.
     */
    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'Pesan tidak boleh kosong.',
            'body.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        $user->chatMessages()->create([
            'sender' => ChatMessage::SENDER_ADMIN,
            'body' => $data['body'],
        ]);

        return redirect()->route('admin.chat.show', $user)->with('success', 'Balasan terkirim');
    }
}
