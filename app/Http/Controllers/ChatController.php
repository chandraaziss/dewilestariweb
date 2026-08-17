<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class ChatController extends Controller
{
    // === API PELANGGAN ===
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string'
        ]);

        Message::create([
            'session_id' => $request->session_id,
            'sender' => 'customer',
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json(['success' => true]);
    }

    public function fetchMessages(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return response()->json([]);
        }

        // Tandai pesan dari admin sudah dibaca oleh pelanggan
        Message::where('session_id', $sessionId)
            ->where('sender', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // === HALAMAN ADMIN ===
    public function adminIndex()
    {
        $this->cleanupOldChats();

        // Ambil session_id unik dan pesan terakhirnya
        $sessions = Message::select('session_id')
            ->groupBy('session_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->get()
            ->map(function ($s) {
                $s->last_message = Message::where('session_id', $s->session_id)->orderBy('created_at', 'desc')->first();
                $s->unread_count = Message::where('session_id', $s->session_id)->where('sender', 'customer')->where('is_read', false)->count();
                return $s;
            });

        return view('admin.chat.index', compact('sessions'));
    }

    private function cleanupOldChats()
    {
        // Hapus chat tanpa id tiket (tidak berawalan TRK-) yang lebih dari 12 jam
        Message::where('session_id', 'not like', 'TRK-%')
            ->where('created_at', '<', now()->subHours(12))
            ->delete();

        // Hapus chat dengan id tiket (berawalan TRK-) yang lebih dari 3 hari
        Message::where('session_id', 'like', 'TRK-%')
            ->where('created_at', '<', now()->subDays(3))
            ->delete();
    }

    public function adminShow($sessionId)
    {
        // Tandai pesan pelanggan sudah dibaca oleh admin
        Message::where('session_id', $sessionId)
            ->where('sender', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chat.show', compact('messages', 'sessionId'));
    }

    public function adminFetch($sessionId)
    {
        // Tandai pesan pelanggan sudah dibaca oleh admin
        Message::where('session_id', $sessionId)
            ->where('sender', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function adminReply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        Message::create([
            'session_id' => $sessionId,
            'sender' => 'admin',
            'message' => $request->message,
            'is_read' => false
        ]);

        return redirect("/admin/chat/$sessionId");
    }

    public function unreadCount()
    {
        $count = Message::where('sender', 'customer')->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }
}
