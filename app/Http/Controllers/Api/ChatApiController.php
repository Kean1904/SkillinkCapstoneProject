<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChatApiController extends Controller
{
    /**
     * Get all messages between two users in chronological order.
     */
    public function getMessages(Request $request)
    {
        $user1 = trim($request->input('user1') ?? $request->input('sender') ?? Session::get('user_name', ''));
        $user2 = trim($request->input('user2') ?? $request->input('receiver') ?? '');

        if (empty($user1) || empty($user2)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing sender or receiver parameters.',
                'messages' => [],
            ], 400);
        }

        // Touch sender's last_seen_at in database
        User::where('name', $user1)->update(['last_seen_at' => now()]);

        // Mark incoming messages as read
        ChatMessage::where('sender_username', $user2)
            ->where('receiver_username', $user1)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Retrieve conversation
        $messages = ChatMessage::where(function ($q) use ($user1, $user2) {
                $q->where('sender_username', $user1)->where('receiver_username', $user2);
            })
            ->orWhere(function ($q) use ($user1, $user2) {
                $q->where('sender_username', $user2)->where('receiver_username', $user1);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $messages->map(function ($m) use ($user1) {
            $cAt = $m->created_at ? (clone $m->created_at)->setTimezone('Asia/Manila') : null;
            return [
                'id' => $m->id,
                'sender_username' => $m->sender_username,
                'receiver_username' => $m->receiver_username,
                'message_text' => $m->message_text,
                'is_read' => (bool)$m->is_read,
                'is_me' => strtolower($m->sender_username) === strtolower($user1),
                'timestamp' => $cAt ? $cAt->format('h:i A') : '',
                'date' => $cAt ? $cAt->format('M d, Y') : '',
                'created_at' => $cAt ? $cAt->toIso8601String() : '',
            ];
        });

        // Also fetch receiver info with real-time online presence
        $receiverUser = User::where('name', $user2)->first();
        $receiverInfo = [
            'username' => $user2,
            'full_name' => $receiverUser ? $receiverUser->full_name : $user2,
            'skills' => $receiverUser->skills ?? 'General Handyman',
            'barangay' => $receiverUser->barangay ?? 'Magalang',
            'phone_number' => $receiverUser->contact_number ?? $receiverUser->phone_number ?? '09171234567',
            'avatar' => $receiverUser && $receiverUser->profile_image_uri ? asset($receiverUser->profile_image_uri) : asset('image/MP_Profile.png'),
            'is_online' => $receiverUser ? $receiverUser->is_online : false,
            'status_label' => $receiverUser ? $receiverUser->status_label : 'Offline',
            'last_seen_display' => $receiverUser ? $receiverUser->last_seen_display : 'Offline',
        ];

        return response()->json([
            'success' => true,
            'receiver' => $receiverInfo,
            'messages' => $formatted,
        ]);
    }

    /**
     * Send a new chat message.
     */
    public function sendMessage(Request $request)
    {
        $sender = trim($request->input('sender_username') ?? Session::get('user_name', ''));
        $receiver = trim($request->input('receiver_username') ?? '');
        $text = trim($request->input('message_text') ?? '');

        if (empty($sender) || empty($receiver) || empty($text)) {
            return response()->json([
                'success' => false,
                'message' => 'Sender, receiver, and message text are required.',
            ], 422);
        }

        // Touch sender's last_seen_at in database
        User::where('name', $sender)->update(['last_seen_at' => now()]);

        $msg = ChatMessage::create([
            'sender_username' => $sender,
            'receiver_username' => $receiver,
            'message_text' => $text,
            'is_read' => false,
        ]);

        $cAt = $msg->created_at ? (clone $msg->created_at)->setTimezone('Asia/Manila') : null;

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender_username' => $msg->sender_username,
                'receiver_username' => $msg->receiver_username,
                'message_text' => $msg->message_text,
                'is_read' => false,
                'is_me' => true,
                'timestamp' => $cAt ? $cAt->format('h:i A') : '',
                'date' => $cAt ? $cAt->format('M d, Y') : '',
                'created_at' => $cAt ? $cAt->toIso8601String() : '',
            ],
        ]);
    }

    /**
     * Get recent active conversations for a user.
     */
    public function getConversations(Request $request)
    {
        $user = trim($request->input('username') ?? Session::get('user_name', ''));

        if (empty($user)) {
            return response()->json(['success' => false, 'conversations' => []], 400);
        }

        // Touch current user's last_seen_at
        User::where('name', $user)->update(['last_seen_at' => now()]);

        // Find all partners
        $sentPartners = ChatMessage::where('sender_username', $user)->pluck('receiver_username');
        $receivedPartners = ChatMessage::where('receiver_username', $user)->pluck('sender_username');
        $allPartners = $sentPartners->merge($receivedPartners)->unique()->filter()->values();

        $conversations = [];
        foreach ($allPartners as $partner) {
            $lastMsg = ChatMessage::where(function ($q) use ($user, $partner) {
                    $q->where('sender_username', $user)->where('receiver_username', $partner);
                })
                ->orWhere(function ($q) use ($user, $partner) {
                    $q->where('sender_username', $partner)->where('receiver_username', $user);
                })
                ->latest('created_at')
                ->first();

            $unreadCount = ChatMessage::where('sender_username', $partner)
                ->where('receiver_username', $user)
                ->where('is_read', false)
                ->count();

            $partnerUser = User::where('name', $partner)->first();
            $lastTime = $lastMsg && $lastMsg->created_at ? (clone $lastMsg->created_at)->setTimezone('Asia/Manila')->format('h:i A') : '';
            $lastDate = $lastMsg && $lastMsg->created_at ? (clone $lastMsg->created_at)->setTimezone('Asia/Manila')->format('M d, Y') : '';

            $conversations[] = [
                'partner_username' => $partner,
                'partner_name' => $partnerUser ? $partnerUser->full_name : $partner,
                'partner_role' => $partnerUser ? ($partnerUser->role ?? 'User') : 'User',
                'partner_barangay' => $partnerUser ? ($partnerUser->barangay ?? 'Magalang') : 'Magalang',
                'partner_skills' => $partnerUser ? ($partnerUser->skills ?? '') : '',
                'partner_phone' => $partnerUser ? ($partnerUser->contact_number ?? $partnerUser->phone_number ?? '') : '',
                'partner_avatar' => $partnerUser && $partnerUser->profile_image_uri ? asset($partnerUser->profile_image_uri) : asset('image/MP_Profile.png'),
                'is_online' => $partnerUser ? $partnerUser->is_online : false,
                'status_label' => $partnerUser ? $partnerUser->status_label : 'Offline',
                'last_seen_display' => $partnerUser ? $partnerUser->last_seen_display : 'Offline',
                'last_message' => $lastMsg ? $lastMsg->message_text : '',
                'last_time' => $lastTime,
                'last_date' => $lastDate,
                'unread_count' => $unreadCount,
            ];
        }

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get real-time unread messages count and latest notification snippet.
     */
    public function getUnreadCount(Request $request)
    {
        $user = trim($request->input('username') ?? Session::get('user_name', ''));
        if (empty($user)) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'latest_sender_username' => null,
                'latest_sender_name' => null,
                'latest_message' => null,
                'latest_time' => null,
            ]);
        }

        // Keep current active user's presence fresh in database
        User::where('name', $user)->update(['last_seen_at' => now()]);

        $unreadCount = ChatMessage::where('receiver_username', $user)
            ->where('is_read', false)
            ->count();

        $latestMsg = ChatMessage::where('receiver_username', $user)
            ->where('is_read', false)
            ->latest('created_at')
            ->first();

        $latestSenderName = null;
        $latestSenderPhone = null;
        $latestSenderRole = 'User';
        $latestSenderOnline = false;
        if ($latestMsg) {
            $senderUser = User::where('name', $latestMsg->sender_username)->first();
            $latestSenderName = $senderUser ? $senderUser->full_name : $latestMsg->sender_username;
            $latestSenderPhone = $senderUser ? ($senderUser->contact_number ?? $senderUser->phone_number ?? '') : '';
            $latestSenderRole = $senderUser ? ($senderUser->role ?? 'User') : 'User';
            $latestSenderOnline = $senderUser ? $senderUser->is_online : false;
        }

        $cAt = $latestMsg && $latestMsg->created_at ? (clone $latestMsg->created_at)->setTimezone('Asia/Manila') : null;

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest_sender_username' => $latestMsg ? $latestMsg->sender_username : null,
            'latest_sender_name' => $latestSenderName,
            'latest_sender_role' => $latestSenderRole,
            'latest_sender_phone' => $latestSenderPhone,
            'latest_sender_is_online' => $latestSenderOnline,
            'latest_message' => $latestMsg ? $latestMsg->message_text : null,
            'latest_time' => $cAt ? $cAt->format('h:i A') : null,
        ]);
    }
}
