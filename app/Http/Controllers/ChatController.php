<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show the customer's support chat thread (single thread per user).
     */
    public function index()
    {
        $user = Auth::user();

        $messages = $user->chatMessages()->oldest()->get();

        // Opening the thread marks admin replies as read.
        $user->chatMessages()
            ->where('sender', ChatMessage::SENDER_ADMIN)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('frontend.profile.chat', compact('messages'));
    }

    /**
     * Append a customer message to their thread.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'Pesan tidak boleh kosong.',
            'body.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        Auth::user()->chatMessages()->create([
            'sender' => ChatMessage::SENDER_CUSTOMER,
            'body' => $data['body'],
        ]);

        return redirect()->route('chat.index')->with('success', 'Pesan terkirim');
    }
}
