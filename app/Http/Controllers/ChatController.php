<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;

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

        // Ambil info relasi pelanggan agar histori dari ticket_id / user_id / order_number ikut terbaca
        $info = self::resolveCustomerInfo($sessionId);
        $relatedSessionIds = [$sessionId];
        if ($info['ticket_id']) $relatedSessionIds[] = $info['ticket_id'];
        if ($info['order_number']) $relatedSessionIds[] = $info['order_number'];
        if ($info['user_id']) {
            $relatedSessionIds[] = 'user_' . $info['user_id'];
            $relatedSessionIds[] = 'cust_' . $info['user_id'];
            $relatedSessionIds[] = (string) $info['user_id'];
        }
        $relatedSessionIds = array_values(array_unique(array_filter($relatedSessionIds)));

        // Tandai pesan dari admin sudah dibaca oleh pelanggan
        Message::whereIn('session_id', $relatedSessionIds)
            ->where('sender', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::whereIn('session_id', $relatedSessionIds)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // === HALAMAN ADMIN ===
    public function adminIndex()
    {
        $rawSessions = Message::select('session_id')
            ->groupBy('session_id')
            ->get();

        $sessionGroups = [];

        foreach ($rawSessions as $s) {
            $sessId = $s->session_id;
            $info = self::resolveCustomerInfo($sessId);

            // Tentukan groupKey unik agar sesi dari pelanggan/order yang sama tergabung
            $groupKey = $info['order_number'] 
                ?: ($info['ticket_id'] 
                ?: ($info['user_id'] ? 'user_' . $info['user_id'] : $sessId));

            if (!isset($sessionGroups[$groupKey])) {
                $sessionGroups[$groupKey] = [
                    'group_key' => $groupKey,
                    'session_ids' => [$sessId],
                    'customer' => $info,
                    'last_message' => null,
                    'unread_count' => 0,
                    'latest_timestamp' => 0,
                ];
            } else {
                if (!in_array($sessId, $sessionGroups[$groupKey]['session_ids'])) {
                    $sessionGroups[$groupKey]['session_ids'][] = $sessId;
                }
                if ($info['order_number'] && !$sessionGroups[$groupKey]['customer']['order_number']) {
                    $sessionGroups[$groupKey]['customer'] = $info;
                }
            }
        }

        foreach ($sessionGroups as &$group) {
            $sessIds = $group['session_ids'];

            $latestMsg = Message::whereIn('session_id', $sessIds)
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = Message::whereIn('session_id', $sessIds)
                ->where('sender', 'customer')
                ->where('is_read', false)
                ->count();

            $group['last_message'] = $latestMsg;
            $group['unread_count'] = $unreadCount;
            $group['latest_timestamp'] = $latestMsg ? $latestMsg->created_at->timestamp : 0;
            $group['primary_session_id'] = $sessIds[0];
        }
        unset($group);

        usort($sessionGroups, function ($a, $b) {
            return $b['latest_timestamp'] <=> $a['latest_timestamp'];
        });

        return view('admin.chat.index', compact('sessionGroups'));
    }

    public function adminShow($sessionId)
    {
        $info = self::resolveCustomerInfo($sessionId);

        $relatedSessionIds = [$sessionId];
        if ($info['ticket_id']) $relatedSessionIds[] = $info['ticket_id'];
        if ($info['order_number']) $relatedSessionIds[] = $info['order_number'];
        if ($info['user_id']) {
            $relatedSessionIds[] = 'user_' . $info['user_id'];
            $relatedSessionIds[] = 'cust_' . $info['user_id'];
            $relatedSessionIds[] = (string) $info['user_id'];
        }
        $relatedSessionIds = array_values(array_unique(array_filter($relatedSessionIds)));

        // Tandai pesan pelanggan sudah dibaca oleh admin
        Message::whereIn('session_id', $relatedSessionIds)
            ->where('sender', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::whereIn('session_id', $relatedSessionIds)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chat.show', compact('messages', 'sessionId', 'info', 'relatedSessionIds'));
    }

    public function adminFetch($sessionId)
    {
        $info = self::resolveCustomerInfo($sessionId);

        $relatedSessionIds = [$sessionId];
        if ($info['ticket_id']) $relatedSessionIds[] = $info['ticket_id'];
        if ($info['order_number']) $relatedSessionIds[] = $info['order_number'];
        if ($info['user_id']) {
            $relatedSessionIds[] = 'user_' . $info['user_id'];
            $relatedSessionIds[] = 'cust_' . $info['user_id'];
            $relatedSessionIds[] = (string) $info['user_id'];
        }
        $relatedSessionIds = array_values(array_unique(array_filter($relatedSessionIds)));

        Message::whereIn('session_id', $relatedSessionIds)
            ->where('sender', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::whereIn('session_id', $relatedSessionIds)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function adminReply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $info = self::resolveCustomerInfo($sessionId);

        // Simpan pesan balasan admin ke session_id utama dan ticket_id agar terbaca oleh semua channel
        $targetSessionIds = array_unique(array_filter([
            $sessionId,
            $info['ticket_id'],
            $info['order_number'],
            $info['user_id'] ? 'user_' . $info['user_id'] : null,
        ]));

        foreach ($targetSessionIds as $sId) {
            Message::create([
                'session_id' => $sId,
                'sender' => 'admin',
                'message' => $request->message,
                'is_read' => false
            ]);
        }

        return redirect("/admin/chat/$sessionId");
    }

    public function unreadCount()
    {
        $count = Message::where('sender', 'customer')->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    public static function resolveCustomerInfo($sessionId)
    {
        $info = [
            'name' => 'Pengunjung / Tamu',
            'phone' => '-',
            'email' => '-',
            'order_number' => null,
            'ticket_id' => null,
            'user_id' => null,
            'avatar' => null,
            'is_registered' => false,
        ];

        // 1. Cari berdasarkan tracking_ticket_id atau order_number
        $order = Order::where('tracking_ticket_id', $sessionId)
            ->orWhere('order_number', $sessionId)
            ->orWhere('order_number', 'ORD-' . $sessionId)
            ->orWhere('order_number', '#' . $sessionId)
            ->first();

        if ($order) {
            $info['name'] = $order->customer_name;
            $info['phone'] = $order->customer_phone;
            $info['order_number'] = $order->order_number;
            $info['ticket_id'] = $order->tracking_ticket_id;
            $info['user_id'] = $order->user_id;
            if ($order->user) {
                $info['email'] = $order->user->email;
                $info['avatar'] = $order->user->avatar;
                $info['is_registered'] = true;
            }
            return $info;
        }

        // 2. Cari berdasarkan ID User (misal: user_1, cust_1, atau 1)
        $userId = null;
        if (preg_match('/^(?:user_|cust_)?([0-9]+)$/', $sessionId, $matches)) {
            $userId = (int) $matches[1];
        }

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $info['name'] = $user->name;
                $info['email'] = $user->email;
                $info['phone'] = $user->phone ?? '-';
                $info['avatar'] = $user->avatar;
                $info['user_id'] = $user->id;
                $info['is_registered'] = true;

                $latestOrder = $user->orders()->first();
                if ($latestOrder) {
                    $info['order_number'] = $latestOrder->order_number;
                    $info['ticket_id'] = $latestOrder->tracking_ticket_id;
                    if (!empty($latestOrder->customer_phone)) {
                        $info['phone'] = $latestOrder->customer_phone;
                    }
                }
                return $info;
            }
        }

        // 3. Fallback format nama berdasarkan prefix session_id
        if (str_starts_with($sessionId, 'TKT-') || str_starts_with($sessionId, 'TRK-')) {
            $info['name'] = 'Pelanggan (Tiket ' . $sessionId . ')';
            $info['ticket_id'] = $sessionId;
        } elseif (str_starts_with($sessionId, 'sess_')) {
            $info['name'] = 'Pelanggan (Sesi ' . substr($sessionId, 5, 8) . ')';
        } else {
            $info['name'] = 'Pelanggan (' . $sessionId . ')';
        }

        return $info;
    }
}
