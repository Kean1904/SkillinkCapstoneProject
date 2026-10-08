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
            return [
                'id' => $m->id,
                'sender_username' => $m->sender_username,
                'receiver_username' => $m->receiver_username,
                'message_text' => $m->message_text,
                'is_read' => (bool)$m->is_read,
                'is_me' => strtolower($m->sender_username) === strtolower($user1),
                'timestamp' => $m->created_at ? $m->created_at->format('h:i A') : '',
                'date' => $m->created_at ? $m->created_at->format('M d, Y') : '',
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : '',
            ];
        });

        // Also fetch receiver info
        $receiverUser = User::where('name', $user2)->first();
        $receiverInfo = [
            'username' => $user2,
            'full_name' => $receiverUser ? $receiverUser->full_name : $user2,
            'skills' => $receiverUser->skills ?? 'General Handyman',
            'barangay' => $receiverUser->barangay ?? 'Magalang',
            'phone_number' => $receiverUser->contact_number ?? $receiverUser->phone_number ?? '09171234567',
            'avatar' => $receiverUser->profile_image_uri ? asset($receiverUser->profile_image_uri) : asset('image/MP_Profile.png'),
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

        $msg = ChatMessage::create([
            'sender_username' => $sender,
            'receiver_username' => $receiver,
            'message_text' => $text,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender_username' => $msg->sender_username,
                'receiver_username' => $msg->receiver_username,
                'message_text' => $msg->message_text,
                'is_read' => false,
                'is_me' => true,
                'timestamp' => $msg->created_at->format('h:i A'),
                'date' => $msg->created_at->format('M d, Y'),
                'created_at' => $msg->created_at->toIso8601String(),
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

            $conversations[] = [
                'partner_username' => $partner,
                'partner_name' => $partnerUser ? $partnerUser->full_name : $partner,
                'partner_role' => $partnerUser->role ?? 'User',
                'partner_barangay' => $partnerUser->barangay ?? 'Magalang',
                'partner_skills' => $partnerUser->skills ?? '',
                'partner_phone' => $partnerUser->contact_number ?? $partnerUser->phone_number ?? '09171234567',
                'last_message' => $lastMsg ? $lastMsg->message_text : '',
                'last_time' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->format('h:i A') : '',
                'unread_count' => $unreadCount,
            ];
        }

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }
}
