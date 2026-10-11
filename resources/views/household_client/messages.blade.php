<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Household Client Messages</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; }

        /* HEADER */
        .header {
            position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
            background-color: #0033a0; color: white; display: flex;
            align-items: center; justify-content: space-between; padding: 10px 25px;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .menu-icon { font-size: 20px; cursor: pointer; }
        .header-left img.logo { width: 42px; height: 42px; border-radius: 50%; }
        .header-left h1 { font-size: 18px; }
        .header-left p { font-size: 11px; }

        .page-content { padding: 80px 25px 40px 25px; position: relative; min-height: 100vh; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(20, 55, 130, 0.65); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1250px; margin: 0 auto; }
        
        .page-title {
            color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
        }

        /* 2-COLUMN MESSENGER DASHBOARD GRID */
        .messenger-container {
            display: flex;
            gap: 20px;
            height: calc(100vh - 180px);
            min-height: 560px;
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 14px;
            backdrop-filter: blur(8px);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* LEFT COLUMN: CONVERSATIONS LIST */
        .convos-sidebar {
            width: 340px;
            flex-shrink: 0;
            background: rgba(10, 30, 70, 0.7);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
        }

        .convos-header {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .convos-header h3 {
            color: white;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .convos-search-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 7px 14px 7px 32px;
            color: white;
            font-size: 12.5px;
            outline: none;
        }
        .convos-search-input:focus {
            border-color: #60a5fa;
            background: rgba(0, 0, 0, 0.45);
        }

        .convos-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .convo-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid transparent;
            text-decoration: none;
            color: white;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .convo-card:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(96, 165, 250, 0.4);
        }
        .convo-card.active {
            background: rgba(37, 99, 235, 0.35);
            border-color: #60a5fa;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
        }

        .convo-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #60a5fa;
            flex-shrink: 0;
            background: white;
        }

        .convo-info {
            flex: 1;
            min-width: 0;
        }
        .convo-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 2px;
        }
        .convo-name {
            font-size: 13.5px;
            font-weight: bold;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .convo-time {
            font-size: 10.5px;
            color: #93c5fd;
            white-space: nowrap;
            margin-left: 6px;
        }
        .convo-snippet {
            font-size: 12px;
            color: #cbd5e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .convo-badge {
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 10px;
            margin-left: 6px;
            flex-shrink: 0;
        }

        /* RIGHT COLUMN: ACTIVE CHAT PANE */
        .chat-main-pane {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(15, 23, 42, 0.3);
            min-width: 0;
        }

        .chat-main-header {
            padding: 12px 20px;
            background: rgba(10, 30, 70, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .chat-messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 18px 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: rgba(5, 15, 35, 0.25);
        }

        .chat-bubble-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 4px;
        }
        .chat-bubble-row.me {
            align-items: flex-end;
        }
        .chat-bubble-row.partner {
            align-items: flex-start;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 10px 15px;
            font-size: 13.5px;
            line-height: 1.45;
            word-break: break-word;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .chat-bubble-row.me .chat-bubble {
            background: #0033a0;
            color: white;
            border-radius: 16px 16px 2px 16px;
            border: 1px solid rgba(96, 165, 250, 0.4);
        }
        .chat-bubble-row.partner .chat-bubble {
            background: #ffffff;
            color: #0f172a;
            border-radius: 16px 16px 16px 2px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .chat-bubble-meta {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 3px;
            padding: 0 4px;
        }

        .chat-quick-pills {
            padding: 8px 16px;
            background: rgba(10, 25, 60, 0.7);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            gap: 8px;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
        }
        .quick-pill-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #e2e8f0;
            border-radius: 14px;
            padding: 4px 11px;
            font-size: 11.5px;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .quick-pill-btn:hover {
            background: rgba(37, 99, 235, 0.4);
            border-color: #60a5fa;
            color: white;
        }

        .chat-input-bar {
            padding: 12px 18px;
            background: rgba(10, 30, 70, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chat-input-bar input {
            flex: 1;
            padding: 11px 16px;
            font-size: 13.5px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(0, 0, 0, 0.35);
            color: white;
            outline: none;
        }
        .chat-input-bar input:focus {
            border-color: #60a5fa;
            background: rgba(0, 0, 0, 0.5);
        }
        .chat-send-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #2563eb;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
            transition: all 0.2s;
        }
        .chat-send-btn:hover {
            background: #1d4ed8;
            transform: scale(1.05);
        }

        /* SIDEBAR STYLES */
        .sidebar {
            position: fixed; top: 0; left: -280px; width: 260px; height: 100vh;
            background: #0033a0; z-index: 2000; transition: left 0.3s ease;
            padding-top: 65px; box-shadow: 2px 0 10px rgba(0,0,0,0.35);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .sidebar.active { left: 0; }
        .sidebar-profile {
            display: flex; flex-direction: column; align-items: center;
            padding: 15px 15px 10px 15px; border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 6px; flex-shrink: 0;
        }
        .sidebar-avatar {
            width: 54px; height: 54px; border-radius: 50%; background: white;
            padding: 3px; margin-bottom: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            object-fit: cover;
        }
        .sidebar-profile .name { font-size: 13.5px; font-weight: bold; color: white; text-align: center; }
        .sidebar-profile .role { margin-top: 3px; font-size: 10.5px; }
        .role-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9.5px; font-weight: bold; color: white; }
        .sidebar-menu { list-style: none; padding: 8px 12px; margin: 0; flex: 1; }
        .sidebar-menu li { margin-bottom: 4px; }
        .sidebar-menu a {
            display: flex; align-items: center; gap: 10px; padding: 10px 14px;
            color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 500;
            border-radius: 8px; transition: all 0.2s ease;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff !important;
            font-weight: bold;
            border-left: 4px solid #60a5fa;
        }
        .sidebar-footer { padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.2); }
        .sidebar-footer .logout-btn {
            display: flex; align-items: center; gap: 10px; color: lightcoral;
            text-decoration: none; font-size: 13.5px; font-weight: bold;
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); z-index: 1500; }
        .sidebar-overlay.active { display: block; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; padding: 5px 14px; border-radius: 20px; font-size: 11.5px; color: #6ee7b7; font-weight: 600;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                <span>Live PH Time (Asia/Manila)</span>
            </div>
        </div>
    </div>

    <!-- UNIFIED HOUSEHOLD CLIENT SIDEBAR -->
    @include('partials.sidebar_household_client', ['active' => 'messages'])

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <div>
                    <h2 style="font-size: 24px; color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-comments" style="color: #60a5fa;"></i> HOUSEHOLD CLIENT MESSAGES
                    </h2>
                    <p style="font-size: 13px; color: #cbd5e1; font-weight: normal; margin-top: 4px;">
                        Direktang pakikipag-ugnayan sa mga Accredited Skilled Workers (Pre-booking inquiry, gamit, presyo, at iskedyul).
                    </p>
                </div>
                <a href="{{ route('dashboard.HouseholdClient') }}" style="color: #93c5fd; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left"></i> Bumalik sa Dashboard
                </a>
            </div>

            <!-- MESSENGER DASHBOARD CONTAINER -->
            <div class="messenger-container">

                <!-- LEFT COLUMN: CONVERSATION LIST -->
                <div class="convos-sidebar">
                    <div class="convos-header">
                        <h3>
                            <span><i class="fa-solid fa-inbox" style="color: #93c5fd;"></i> Inbox</span>
                            <span style="font-size: 11.5px; background: rgba(59,130,246,0.3); padding: 2px 8px; border-radius: 12px; font-weight: normal; border: 1px solid #3b82f6;">
                                {{ count($conversations) }} Conversations
                            </span>
                        </h3>
                        <div style="position: relative;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 11px; top: 9px; font-size: 12px; color: #94a3b8;"></i>
                            <input type="text" class="convos-search-input" id="searchWorkersInput" placeholder="Search worker or skill..." onkeyup="filterConversationCards(this.value)">
                        </div>
                    </div>

                    <div class="convos-list" id="convosListContainer">
                        @if(count($conversations) > 0)
                            @foreach($conversations as $c)
                                <a href="{{ route('household_client.messages', ['partner' => $c->partner_username]) }}"
                                   class="convo-card {{ $selectedPartner === $c->partner_username ? 'active' : '' }}"
                                   data-name="{{ strtolower($c->partner_name) }}"
                                   data-username="{{ strtolower($c->partner_username) }}"
                                   data-skill="{{ strtolower($c->partner_skills ?? '') }}"
                                   data-msg="{{ strtolower($c->last_message) }}">
                                    <div style="position: relative; flex-shrink: 0;">
                                        <img src="{{ $c->partner_avatar }}" alt="{{ $c->partner_name }}" class="convo-avatar"
                                             onerror="this.src='{{ asset('image/MP_Profile.png') }}'">
                                        <span style="position: absolute; bottom: 0; right: 0; width: 11px; height: 11px; border-radius: 50%; background: {{ $c->is_online ? '#10b981' : '#94a3b8' }}; border: 2px solid #0a1e46; {{ $c->is_online ? 'box-shadow: 0 0 6px #10b981;' : '' }}" title="{{ $c->is_online ? 'Online / Active' : $c->last_seen_display }}"></span>
                                    </div>
                                    <div class="convo-info">
                                        <div class="convo-top">
                                            <span class="convo-name">{{ $c->partner_name }}</span>
                                            <span class="convo-time">{{ $c->last_time }}</span>
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <span class="convo-snippet" style="{{ $c->unread_count > 0 ? 'color: #ffffff; font-weight: bold;' : '' }}">
                                                {{ $c->last_message }}
                                            </span>
                                            @if($c->unread_count > 0)
                                                <span class="convo-badge">{{ $c->unread_count }}</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 10px; color: #93c5fd; margin-top: 2px; display: flex; align-items: center; justify-content: space-between;">
                                            <span><i class="fa-solid fa-wrench"></i> {{ $c->partner_skills ?: 'Skilled Worker' }}</span>
                                            <span style="color: {{ $c->is_online ? '#86efac' : '#94a3b8' }}; font-weight: 500;">
                                                &bull; {{ $c->is_online ? 'Active' : 'Offline' }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div style="text-align: center; padding: 40px 16px; color: #94a3b8; font-size: 12.5px;">
                                <i class="fa-regular fa-comment-dots" style="font-size: 32px; color: #64748b; margin-bottom: 8px; display: inline-block;"></i>
                                <p style="font-weight: bold; color: white;">Walang Aktibong Mensahe</p>
                                <p style="font-size: 11px; margin-top: 4px;">Pumunta sa listahan ng Skilled Workers o sa iyong hiring bookings upang simulan ang pakikipag-ugnayan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: ACTIVE CHAT PANE -->
                <div class="chat-main-pane">
                    @if($activePartner)
                        <!-- Active Header -->
                        <div class="chat-main-header">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="position: relative; flex-shrink: 0;">
                                    <img src="{{ $activePartner->avatar }}" alt="{{ $activePartner->full_name }}" class="convo-avatar"
                                         onerror="this.src='{{ asset('image/MP_Profile.png') }}'">
                                    <span id="activePartnerDot" style="position: absolute; bottom: 0; right: 0; width: 11px; height: 11px; border-radius: 50%; background: {{ $activePartner->is_online ? '#10b981' : '#94a3b8' }}; border: 2px solid #0a1e46; {{ $activePartner->is_online ? 'box-shadow: 0 0 6px #10b981;' : '' }}" title="{{ $activePartner->is_online ? 'Online / Active' : $activePartner->last_seen_display }}"></span>
                                </div>
                                <div style="min-width: 0;">
                                    <h4 style="color: white; font-size: 15px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $activePartner->full_name }}
                                    </h4>
                                    <div style="font-size: 11.5px; color: #93c5fd; display: flex; align-items: center; gap: 8px;">
                                        <span><i class="fa-solid fa-wrench"></i> {{ $activePartner->skills ?: 'Accredited Worker' }}</span>
                                        <span>&bull;</span>
                                        <span><i class="fa-solid fa-location-dot"></i> Brgy. {{ $activePartner->barangay }}</span>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                @if(!empty($activePartner->phone))
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $activePartner->phone) }}"
                                       title="Call {{ $activePartner->full_name }} ({{ $activePartner->phone }})"
                                       style="background: rgba(34, 197, 94, 0.25); border: 1px solid #22c55e; color: #86efac; padding: 6px 14px; border-radius: 20px; font-size: 12px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-phone"></i> {{ $activePartner->phone }}
                                    </a>
                                @endif
                                @if($activePartner->is_online)
                                    <span id="activePartnerStatusPill" style="font-size: 11px; color: #86efac; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; padding: 4px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981;"></span> Online / Active
                                    </span>
                                @else
                                    <span id="activePartnerStatusPill" style="font-size: 11px; color: #cbd5e1; background: rgba(148, 163, 184, 0.2); border: 1px solid #94a3b8; padding: 4px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;"></span> {{ $activePartner->last_seen_display ?? 'Offline' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Messages Box -->
                        <div class="chat-messages-container" id="dashboardChatMessagesBox">
                            <!-- Safety Notice -->
                            <div style="text-align: center; margin-bottom: 12px; padding: 8px 14px; background: rgba(2, 132, 199, 0.25); border: 1px solid #38bdf8; border-radius: 8px; font-size: 11.5px; color: #bae6fd;">
                                <i class="fa-solid fa-shield-halved" style="color: #38bdf8;"></i> <strong>Magalang PESO Official In-App Channel:</strong>
                                Lahat ng mensahe at transaksyon ay nakatala sa Philippine Standard Time (PHT). Makipag-usap nang maayos at pormal.
                            </div>

                            <div id="messagesListInner" style="display: flex; flex-direction: column; gap: 10px;">
                                @if(count($messages) > 0)
                                    @php $lastDateHeader = null; @endphp
                                    @foreach($messages as $msg)
                                        @php
                                            $msgDate = $msg->created_at ? (clone $msg->created_at)->setTimezone('Asia/Manila')->format('M d, Y') : '';
                                            $msgTime = $msg->created_at ? (clone $msg->created_at)->setTimezone('Asia/Manila')->format('h:i A') : '';
                                            $isMe = (strtolower($msg->sender_username) === strtolower($user->name));
                                        @endphp

                                        @if($msgDate !== $lastDateHeader)
                                            <div style="text-align: center; margin: 8px 0;">
                                                <span style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #cbd5e1; font-size: 10.5px; padding: 3px 12px; border-radius: 12px;">
                                                    {{ $msgDate }}
                                                </span>
                                            </div>
                                            @php $lastDateHeader = $msgDate; @endphp
                                        @endif

                                        <div class="chat-bubble-row {{ $isMe ? 'me' : 'partner' }}">
                                            <div class="chat-bubble">
                                                {{ $msg->message_text }}
                                            </div>
                                            <span class="chat-bubble-meta">
                                                {{ $isMe ? 'Ikaw' : $activePartner->full_name }} &bull; {{ $msgTime }} (PH Time)
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div id="noMsgYetNotice" style="text-align: center; padding: 40px 10px; color: #94a3b8; font-size: 12.5px;">
                                        <i class="fa-regular fa-comments" style="font-size: 32px; margin-bottom: 8px; display: inline-block;"></i>
                                        <p style="font-weight: bold; color: white;">Walang nakaraang mensahe.</p>
                                        <p style="font-size: 11px; margin-top: 4px;">Magtanong ukol sa serbisyo, availability, o kagamitan gamit ang mga quick replies sa ibaba.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Pre-booking Quick Replies Tailored for Household Clients -->
                        <div class="chat-quick-pills">
                            <button type="button" class="quick-pill-btn" onclick="sendDashQuickReply('Available po ba kayo para sa serbisyo ngayong linggo?')">
                                Available po ba kayo?
                            </button>
                            <button type="button" class="quick-pill-btn" onclick="sendDashQuickReply('Magkano po ang inyong tinatayang service rate o budget estimate?')">
                                Magkano ang rate?
                            </button>
                            <button type="button" class="quick-pill-btn" onclick="sendDashQuickReply('Kailangan po ba ako ang bibili ng materyales at gamit, o may dala kayong sarili?')">
                                Gamit / Materyales
                            </button>
                            <button type="button" class="quick-pill-btn" onclick="sendDashQuickReply('Pwede po ba magpa-ocular inspection bago simulan ang gawain?')">
                                Ocular Inspection
                            </button>
                            <button type="button" class="quick-pill-btn" onclick="sendDashQuickReply('Sige po, kumpirmado na po ang ating napag-usapan. I-book ko na po kayo.')">
                                Kumpirmado / Mag-book
                            </button>
                        </div>

                        <!-- Input Bar -->
                        <div class="chat-input-bar">
                            <input type="text" id="dashChatInput" placeholder="Mag-type ng mensahe o inquiry para sa manggagawa..." maxlength="1000"
                                   onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); sendDashboardMessage(); }">
                            <button type="button" class="chat-send-btn" id="dashSendBtn" onclick="sendDashboardMessage()" title="Send Message">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    @else
                        <!-- No Selected Conversation -->
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; color: #94a3b8;">
                            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; border: 1px solid rgba(96, 165, 250, 0.4);">
                                <i class="fa-solid fa-comments" style="font-size: 32px; color: #60a5fa;"></i>
                            </div>
                            <h3 style="color: white; font-size: 18px; margin-bottom: 6px;">Pumili ng Manggagawa mula sa Inbox</h3>
                            <p style="font-size: 13px; max-width: 400px; line-height: 1.5; color: #cbd5e1;">
                                Piliin ang manggagawa sa kaliwang listahan upang simulan ang direktang pag-uusap ukol sa serbisyo.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>

    <!-- AUDIO NOTIFICATION CHIME SCRIPT -->
    <script>
    function playNotificationChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.1);

            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start();
            osc.stop(ctx.currentTime + 0.36);
        } catch (e) {
            console.debug('Chime error:', e);
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    }

    // Client Search Filter
    function filterConversationCards(keyword) {
        const q = (keyword || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.convo-card');
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const user = card.getAttribute('data-username') || '';
            const skill = card.getAttribute('data-skill') || '';
            const msg = card.getAttribute('data-msg') || '';
            if (name.includes(q) || user.includes(q) || skill.includes(q) || msg.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Dashboard Messaging Logic
    const currentClientUsername = "{{ $user->name }}";
    const activePartnerUsername = "{{ $activePartner ? $activePartner->username : '' }}";
    let isSendingDashMsg = false;
    let lastLoadedMessageCount = {{ count($messages) }};

    function sendDashQuickReply(text) {
        const input = document.getElementById('dashChatInput');
        if (input) {
            input.value = text;
            sendDashboardMessage();
        }
    }

    function sendDashboardMessage() {
        const input = document.getElementById('dashChatInput');
        const text = input ? input.value.trim() : '';
        if (!text || !activePartnerUsername || isSendingDashMsg) return;

        isSendingDashMsg = true;
        const btn = document.getElementById('dashSendBtn');
        if (btn) btn.disabled = true;

        // Render optimistically with PH Time
        const phTimeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        appendBubbleToDashList(text, true, phTimeStr);
        input.value = '';

        // Hide "no messages" notice if present
        const noNotice = document.getElementById('noMsgYetNotice');
        if (noNotice) noNotice.style.display = 'none';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                sender_username: currentClientUsername,
                receiver_username: activePartnerUsername,
                message_text: text
            })
        })
        .then(res => res.json())
        .then(data => {
            isSendingDashMsg = false;
            if (btn) btn.disabled = false;
            if (data && data.success) {
                lastLoadedMessageCount++;
                scrollDashMessagesToBottom();
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
            isSendingDashMsg = false;
            if (btn) btn.disabled = false;
        });
    }

    function appendBubbleToDashList(text, isMe, timeStr) {
        const inner = document.getElementById('messagesListInner');
        if (!inner) return;

        const row = document.createElement('div');
        row.className = 'chat-bubble-row ' + (isMe ? 'me' : 'partner');

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.innerText = text;

        const meta = document.createElement('span');
        meta.className = 'chat-bubble-meta';
        meta.innerText = (isMe ? 'Ikaw' : '{{ addslashes($activePartner->full_name ?? '') }}') + ' • ' + timeStr + ' (PH Time)';

        row.appendChild(bubble);
        row.appendChild(meta);
        inner.appendChild(row);

        scrollDashMessagesToBottom();
    }

    function scrollDashMessagesToBottom() {
        const box = document.getElementById('dashboardChatMessagesBox');
        if (box) {
            setTimeout(() => {
                box.scrollTop = box.scrollHeight;
            }, 50);
        }
    }

    // Auto-polling for active conversation messages (Every 3 seconds)
    function pollActiveConversation() {
        if (!activePartnerUsername) return;

        const url = "{{ route('chat.messages') }}?user1=" + encodeURIComponent(currentClientUsername) + "&user2=" + encodeURIComponent(activePartnerUsername);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // Update active presence dynamically
                if (data.receiver) {
                    const isOnline = !!data.receiver.is_online;
                    const pill = document.getElementById('activePartnerStatusPill');
                    const dot = document.getElementById('activePartnerDot');
                    if (dot) {
                        dot.style.background = isOnline ? '#10b981' : '#94a3b8';
                        dot.style.boxShadow = isOnline ? '0 0 6px #10b981' : 'none';
                        dot.title = isOnline ? 'Online / Active' : (data.receiver.last_seen_display || 'Offline');
                    }
                    if (pill) {
                        pill.style.color = isOnline ? '#86efac' : '#cbd5e1';
                        pill.style.background = isOnline ? 'rgba(16, 185, 129, 0.2)' : 'rgba(148, 163, 184, 0.2)';
                        pill.style.borderColor = isOnline ? '#10b981' : '#94a3b8';
                        pill.innerHTML = isOnline 
                            ? '<span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981;"></span> Online / Active'
                            : `<span style="width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;"></span> ${data.receiver.last_seen_display || 'Offline'}`;
                    }
                }

                if (Array.isArray(data.messages) && data.messages.length > lastLoadedMessageCount) {
                    const diff = data.messages.length - lastLoadedMessageCount;
                    const newIncoming = data.messages.slice(-diff).some(m => !m.is_me);

                    if (newIncoming) {
                        playNotificationChime();
                    }

                    renderUpdatedMessagesList(data.messages);
                    lastLoadedMessageCount = data.messages.length;
                    scrollDashMessagesToBottom();
                }
            }
        })
        .catch(err => console.debug('Polling error:', err));
    }

    function renderUpdatedMessagesList(msgs) {
        const inner = document.getElementById('messagesListInner');
        if (!inner) return;

        inner.innerHTML = '';
        let lastDate = '';

        msgs.forEach(m => {
            if (m.date && m.date !== lastDate) {
                const dateSep = document.createElement('div');
                dateSep.style.textAlign = 'center';
                dateSep.style.margin = '8px 0';
                dateSep.innerHTML = `<span style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #cbd5e1; font-size: 10.5px; padding: 3px 12px; border-radius: 12px;">${m.date}</span>`;
                inner.appendChild(dateSep);
                lastDate = m.date;
            }

            const isMe = m.is_me;
            const row = document.createElement('div');
            row.className = 'chat-bubble-row ' + (isMe ? 'me' : 'partner');

            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.innerText = m.message_text;

            const meta = document.createElement('span');
            meta.className = 'chat-bubble-meta';
            meta.innerText = (isMe ? 'Ikaw' : '{{ addslashes($activePartner->full_name ?? '') }}') + ' • ' + (m.timestamp || '') + ' (PH Time)';

            row.appendChild(bubble);
            row.appendChild(meta);
            inner.appendChild(row);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        scrollDashMessagesToBottom();
        if (activePartnerUsername) {
            setInterval(pollActiveConversation, 3000);
        }
    });
    </script>

    @include('partials.privacy_consent_modal')
    @include('partials.chat_popup')

</body>
</html>
