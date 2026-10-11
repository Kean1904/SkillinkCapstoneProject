<!-- SKILLINK In-App Messaging Pop-up (Facebook Messenger Style with Live Notification & PH Time) -->
<div id="skillinkChatFloatingContainer" style="position: fixed; bottom: 0; right: 25px; z-index: 9999; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- 1. INCOMING MESSAGE TOAST NOTIFICATION BANNER (Slides in when new message arrives) -->
    <div id="chatIncomingToast" style="display: none; position: fixed; bottom: 85px; right: 25px; max-width: 330px; background: linear-gradient(135deg, #002b80 0%, #0033a0 100%); color: white; padding: 12px 16px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.35); border: 1.5px solid #60a5fa; z-index: 10000; animation: slideUpToast 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="width: 10px; height: 10px; border-radius: 50%; background: #4ade80; display: inline-block; box-shadow: 0 0 8px #4ade80;"></span>
                <strong id="toastSenderName" style="font-size: 13px; color: #fde047;">New Message</strong>
            </div>
            <button type="button" onclick="dismissChatToast()" style="background: transparent; border: none; color: rgba(255,255,255,0.7); cursor: pointer; font-size: 14px; padding: 0 2px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p id="toastMessageSnippet" style="font-size: 12px; color: #e2e8f0; margin: 6px 0 10px 0; line-height: 1.35; max-height: 48px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            May bagong mensahe ka.
        </p>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span id="toastMessageTime" style="font-size: 10.5px; color: #93c5fd;">Just now (PH Time)</span>
            <button type="button" id="toastOpenChatBtn" onclick="openToastChat()" style="background: #2563eb; color: white; border: 1px solid #93c5fd; padding: 4px 12px; border-radius: 14px; font-size: 11.5px; font-weight: bold; cursor: pointer;">
                Open Chat &rarr;
            </button>
        </div>
    </div>

    <!-- 2. QUICK CONTACT PICKER FLYOUT (Visible when clicking launcher with no active contact) -->
    <div id="chatContactsFlyout" style="display: none; position: fixed; bottom: 80px; right: 25px; width: 310px; background: #ffffff; border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,0.3); border: 1px solid rgba(0,0,0,0.15); overflow: hidden; z-index: 9999; flex-direction: column;">
        <div style="background: linear-gradient(135deg, #002b80 0%, #0033a0 100%); color: white; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
            <strong style="font-size: 13.5px;"><i class="fa-solid fa-comments" style="color: #fde047;"></i> Recent Messages</strong>
            <button type="button" onclick="toggleContactsFlyout()" style="background: transparent; border: none; color: white; cursor: pointer; font-size: 13px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div id="chatContactsFlyoutList" style="max-height: 280px; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 4px;">
            <div style="text-align: center; padding: 20px; font-size: 12px; color: #64748b;">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading conversations...
            </div>
        </div>
    </div>

    <!-- 3. PERSISTENT FLOATING MESSENGER LAUNCHER BUTTON (Bottom-Right Circle) -->
    <div id="chatFloatingLauncherBtn" onclick="toggleFloatingChatLauncher()" title="SKILLINK Messenger"
         style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #0033a0 0%, #1d4ed8 100%); color: white; cursor: pointer; box-shadow: 0 6px 20px rgba(0,51,160,0.45); border: 2px solid #ffffff; position: fixed; bottom: 20px; right: 25px; transition: transform 0.2s, box-shadow 0.2s; user-select: none;">
        <i class="fa-solid fa-comments" style="font-size: 24px; color: #ffffff;"></i>
        <!-- Unread Badge Counter -->
        <span id="chatFloatingBadge" style="display: none; position: absolute; top: -3px; right: -3px; background: #ef4444; color: white; font-size: 11px; font-weight: bold; min-width: 20px; height: 20px; border-radius: 10px; border: 2px solid white; display: flex; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            0
        </span>
    </div>

    <!-- 4. MINIMIZED BAR (Visible when active chat is minimized) -->
    <div id="chatMinimizedBar" onclick="restoreChatModal()" style="display: none; align-items: center; gap: 10px; background: #0033a0; color: white; padding: 10px 18px; border-radius: 24px 24px 0 0; cursor: pointer; box-shadow: 0 -4px 15px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.3); border-bottom: none; user-select: none; position: fixed; bottom: 0; right: 25px;">
        <div style="position: relative;">
            <i class="fa-solid fa-comments" style="font-size: 16px; color: #fde047;"></i>
            <span id="chatUnreadDot" style="display: none; position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 1px solid white;"></span>
        </div>
        <span id="chatMinimizedTitle" style="font-size: 13px; font-weight: 600; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Chat</span>
        <button type="button" onclick="event.stopPropagation(); closeChatModal();" title="Close chat" style="background: transparent; border: none; color: rgba(255,255,255,0.8); cursor: pointer; font-size: 13px; margin-left: 4px; padding: 0 4px;">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- 5. ACTIVE MESSENGER-STYLE CHAT WINDOW -->
    <div id="chatWindow" style="display: none; width: 340px; max-width: calc(100vw - 30px); background: #ffffff; border-radius: 12px 12px 0 0; box-shadow: 0 10px 30px rgba(0,0,0,0.35); border: 1px solid rgba(0,0,0,0.15); border-bottom: none; overflow: hidden; flex-direction: column; position: fixed; bottom: 0; right: 25px;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #002b80 0%, #0033a0 100%); color: white; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #fde047;">
            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                <div style="position: relative; flex-shrink: 0;">
                    <div id="chatAvatarBadge" style="width: 36px; height: 36px; border-radius: 50%; background: #ffffff; color: #0033a0; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 2px solid #60a5fa;">
                        U
                    </div>
                    <span id="chatPartnerStatusDot" style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #94a3b8; border-radius: 50%; border: 1.5px solid white;"></span>
                </div>
                <div style="overflow: hidden; line-height: 1.25;">
                    <div id="chatWorkerFullName" style="font-size: 13.5px; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff;">Contact Name</div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                        <span id="chatWorkerTradeBadge" style="font-size: 10.5px; color: #fde047; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">User</span>
                        <span id="chatPartnerStatusText" style="font-size: 10px; color: #cbd5e1; opacity: 0.9;">&bull; Checking status...</span>
                    </div>
                </div>
            </div>

            <!-- Controls (Call shortcut, Minimize, Close) -->
            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                <a id="chatCallBtn" href="javascript:void(0)" title="Call Contact" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.18); color: #86efac; text-decoration: none; font-size: 12px; transition: background 0.2s;">
                    <i class="fa-solid fa-phone"></i>
                </a>
                <button type="button" onclick="minimizeChatModal()" title="Minimize" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.18); border: none; color: white; cursor: pointer; font-size: 11px;">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <button type="button" onclick="closeChatModal()" title="Close" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.18); border: none; color: white; cursor: pointer; font-size: 12px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Body / Messages -->
        <div id="chatMessagesBox" style="height: 310px; overflow-y: auto; padding: 12px; background: #f8fafc; display: flex; flex-direction: column; gap: 8px;">
            <div style="text-align: center; margin: 4px 0 10px 0; padding: 8px 10px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; font-size: 11px; color: #0369a1; line-height: 1.35;">
                <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i> <strong>Magalang PESO Official In-App Messaging</strong><br>
                Lahat ng mensahe ay nakatala sa Philippine Standard Time (PHT). Makipag-usap nang pormal.
            </div>
            <div id="chatLoadingState" style="text-align: center; padding: 20px; font-size: 12px; color: #64748b;">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading conversation...
            </div>
            <div id="chatMessagesList" style="display: flex; flex-direction: column; gap: 8px;"></div>
        </div>

        <!-- Pre-booking Quick Replies (Pills) -->
        <div id="chatQuickReplies" style="padding: 6px 10px; background: #f1f5f9; border-top: 1px solid #e2e8f0; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
            <button type="button" onclick="sendQuickReply('Available po ba kayo ngayong linggo?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Available po ba?
            </button>
            <button type="button" onclick="sendQuickReply('Kailangan po ba ako ang bibili ng materyales o gamit?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Materyales / Tools
            </button>
            <button type="button" onclick="sendQuickReply('Pwede po ba magpa-estimate muna o ocular inspection?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Estimate / Inspection
            </button>
            <button type="button" onclick="sendQuickReply('Sige po, kumpirmado na po ang ating napag-usapan.')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Kumpirmado
            </button>
        </div>

        <!-- Footer / Input Form -->
        <div style="padding: 8px 10px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px;">
            <input type="text" id="chatMessageInput" placeholder="Type a message or inquiry..." maxlength="1000"
                   style="flex: 1; padding: 8px 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 20px; outline: none; transition: border-color 0.2s;"
                   onfocus="this.style.borderColor='#0033a0'" onblur="this.style.borderColor='#cbd5e1'"
                   onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); sendChatMessage(); }">
            <button type="button" id="chatSendBtn" onclick="sendChatMessage()" style="width: 36px; height: 36px; border-radius: 50%; background: #0033a0; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,51,160,0.3); transition: background 0.2s;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>

    </div>
</div>

<style>
@keyframes slideUpToast {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
#chatFloatingLauncherBtn:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 24px rgba(0,51,160,0.55);
}
</style>

<script>
(function() {
    let currentPartnerUsername = '';
    let currentPartnerFullName = '';
    let currentPartnerTrade = '';
    let currentPartnerPhone = '';
    let chatPollInterval = null;
    let globalUnreadPollInterval = null;
    let isSending = false;
    let lastMessageCount = 0;
    let lastKnownUnreadCount = 0;

    // Current logged-in user
    const currentUsername = "{{ session('user_name') ?? (Auth::check() ? Auth::user()->name : '') }}";
    const currentUserRole = "{{ session('user_role') ?? (Auth::check() ? Auth::user()->role : '') }}";

    // Play synthesized notification chime using Web Audio API
    function playAudioChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.1); // A5

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

    // Toggle Floating Launcher (Open active chat OR open contacts flyout)
    window.toggleFloatingChatLauncher = function() {
        const chatWin = document.getElementById('chatWindow');
        const minBar = document.getElementById('chatMinimizedBar');

        if (chatWin && chatWin.style.display === 'flex') {
            minimizeChatModal();
            return;
        }

        if (minBar && minBar.style.display === 'flex') {
            restoreChatModal();
            return;
        }

        if (currentPartnerUsername) {
            restoreChatModal();
        } else {
            toggleContactsFlyout();
        }
    };

    window.toggleContactsFlyout = function() {
        const flyout = document.getElementById('chatContactsFlyout');
        if (!flyout) return;

        if (flyout.style.display === 'flex') {
            flyout.style.display = 'none';
        } else {
            flyout.style.display = 'flex';
            loadContactsFlyoutList();
        }
    };

    function loadContactsFlyoutList() {
        const listContainer = document.getElementById('chatContactsFlyoutList');
        if (!listContainer) return;

        listContainer.innerHTML = '<div style="text-align: center; padding: 20px; font-size: 12px; color: #64748b;"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';

        fetch("{{ route('chat.conversations') }}?username=" + encodeURIComponent(currentUsername), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success && Array.isArray(data.conversations) && data.conversations.length > 0) {
                let html = '';
                data.conversations.forEach(c => {
                    const avatar = c.partner_avatar || '{{ asset("image/MP_Profile.png") }}';
                    const initials = (c.partner_name.split(' ').map(w => w[0]).join('').substring(0, 2) || 'U').toUpperCase();
                    const isOnline = !!c.is_online;
                    const dotColor = isOnline ? '#10b981' : '#94a3b8';
                    const dotShadow = isOnline ? 'box-shadow: 0 0 5px #10b981;' : '';
                    const statusText = isOnline ? '<span style="color:#10b981; font-weight:600;">Active</span>' : `<span style="color:#94a3b8;">${c.last_seen_display || 'Offline'}</span>`;
                    html += `
                        <div onclick="openChatModal('${c.partner_username}', '${escapeJs(c.partner_name)}', '${escapeJs(c.partner_role || c.partner_skills || 'User')}', '${escapeJs(c.partner_phone || '')}'); toggleContactsFlyout();"
                             style="display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; cursor: pointer; transition: background 0.2s;"
                             onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            <div style="position: relative; flex-shrink: 0;">
                                <img src="${avatar}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1.5px solid #3b82f6;"
                                     onerror="this.src='{{ asset('image/MP_Profile.png') }}'">
                                <span style="position: absolute; bottom: 0; right: 0; width: 9px; height: 9px; border-radius: 50%; background: ${dotColor}; border: 1.5px solid white; ${dotShadow}" title="${isOnline ? 'Online / Active' : (c.last_seen_display || 'Offline')}"></span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <strong style="font-size: 13px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${c.partner_name}</strong>
                                    <span style="font-size: 10px; color: #64748b; margin-left: 4px;">${c.last_time || ''}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 4px;">
                                    <span style="font-size: 11.5px; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                                        ${c.last_message || 'Start conversation...'}
                                    </span>
                                    <span style="font-size: 9.5px; flex-shrink: 0;">${statusText}</span>
                                </div>
                            </div>
                            ${c.unread_count > 0 ? `<span style="background: #ef4444; color: white; font-size: 10px; font-weight: bold; padding: 1px 6px; border-radius: 10px; flex-shrink: 0;">${c.unread_count}</span>` : ''}
                        </div>
                    `;
                });
                listContainer.innerHTML = html;
            } else {
                listContainer.innerHTML = `
                    <div style="text-align: center; padding: 24px 10px; color: #64748b; font-size: 12px;">
                        <i class="fa-regular fa-comments" style="font-size: 26px; color: #94a3b8; margin-bottom: 6px; display: inline-block;"></i>
                        <p style="font-weight: 600; color: #1e293b;">Walang aktibong chat.</p>
                        <p style="font-size: 11px; margin-top: 2px;">I-click ang Message button sa profile o booking upang makipag-usap.</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            listContainer.innerHTML = '<div style="text-align:center; padding:15px; color:#ef4444; font-size:11px;">Error loading contacts.</div>';
        });
    }

    // Dismiss incoming toast
    window.dismissChatToast = function() {
        const toast = document.getElementById('chatIncomingToast');
        if (toast) toast.style.display = 'none';
    };

    window.openToastChat = function() {
        dismissChatToast();
        if (window._latestSenderUsername) {
            openChatModal(
                window._latestSenderUsername,
                window._latestSenderFullName || window._latestSenderUsername,
                window._latestSenderRole || 'User',
                window._latestSenderPhone || ''
            );
        } else {
            toggleFloatingChatLauncher();
        }
    };

    // Main Open Modal function
    window.openChatModal = function(partnerUsername, partnerFullName, partnerTrade, partnerPhone) {
        if (!partnerUsername) return;

        currentPartnerUsername = partnerUsername;
        currentPartnerFullName = partnerFullName || partnerUsername;
        currentPartnerTrade = partnerTrade || (currentUserRole === 'skilled worker' ? 'Household Client' : 'Skilled Worker');
        currentPartnerPhone = partnerPhone || '';

        // Close flyout & toast if open
        const flyout = document.getElementById('chatContactsFlyout');
        if (flyout) flyout.style.display = 'none';
        dismissChatToast();

        // Update Header UI
        document.getElementById('chatWorkerFullName').innerText = currentPartnerFullName;
        document.getElementById('chatWorkerTradeBadge').innerText = currentPartnerTrade;
        document.getElementById('chatMinimizedTitle').innerText = 'Chat with ' + currentPartnerFullName;

        // Avatar Initial
        const initials = (currentPartnerFullName.split(' ').map(w => w[0]).join('').substring(0, 2) || 'U').toUpperCase();
        document.getElementById('chatAvatarBadge').innerText = initials;

        // Phone call shortcut button
        const callBtn = document.getElementById('chatCallBtn');
        if (currentPartnerPhone && currentPartnerPhone.trim() !== '') {
            callBtn.href = 'tel:' + currentPartnerPhone.replace(/[^0-9+]/g, '');
            callBtn.style.display = 'inline-flex';
            callBtn.title = 'Call ' + currentPartnerFullName + ' (' + currentPartnerPhone + ')';
        } else {
            callBtn.style.display = 'none';
        }

        // Show window and hide minimized pill
        document.getElementById('chatMinimizedBar').style.display = 'none';
        const chatWin = document.getElementById('chatWindow');
        chatWin.style.display = 'flex';

        // Fetch messages
        loadChatMessages(true);

        // Start active conversation polling every 3 seconds
        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(function() {
            loadChatMessages(false);
        }, 3000);

        // Focus input
        setTimeout(function() {
            const input = document.getElementById('chatMessageInput');
            if (input) input.focus();
        }, 200);
    };

    window.minimizeChatModal = function() {
        document.getElementById('chatWindow').style.display = 'none';
        if (currentPartnerUsername) {
            document.getElementById('chatMinimizedBar').style.display = 'flex';
        }
    };

    window.restoreChatModal = function() {
        document.getElementById('chatMinimizedBar').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';
        document.getElementById('chatUnreadDot').style.display = 'none';
        scrollToLatestMessage();
    };

    window.closeChatModal = function() {
        document.getElementById('chatWindow').style.display = 'none';
        document.getElementById('chatMinimizedBar').style.display = 'none';
        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }
        currentPartnerUsername = '';
    };

    window.sendQuickReply = function(text) {
        const input = document.getElementById('chatMessageInput');
        if (input) {
            input.value = text;
            sendChatMessage();
        }
    };

    window.sendChatMessage = function() {
        const input = document.getElementById('chatMessageInput');
        const text = input ? input.value.trim() : '';
        if (!text || !currentPartnerUsername || isSending) return;

        isSending = true;
        const sendBtn = document.getElementById('chatSendBtn');
        if (sendBtn) sendBtn.disabled = true;

        // Optimistically render message with PH Time
        const phTimeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        appendMessageBubble({
            sender_username: currentUsername,
            message_text: text,
            is_me: true,
            timestamp: phTimeStr
        });
        scrollToLatestMessage();
        input.value = '';

        // CSRF Token
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '{{ csrf_token() }}';

        fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                sender_username: currentUsername,
                receiver_username: currentPartnerUsername,
                message_text: text
            })
        })
        .then(res => res.json())
        .then(data => {
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
            if (data && data.success) {
                loadChatMessages(false);
            }
        })
        .catch(err => {
            console.error('Chat send error:', err);
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
        });
    };

    function loadChatMessages(showLoadingSpinner) {
        if (!currentPartnerUsername) return;

        const loading = document.getElementById('chatLoadingState');
        const list = document.getElementById('chatMessagesList');

        if (showLoadingSpinner && loading && (!list.children || list.children.length === 0)) {
            loading.style.display = 'block';
        }

        const url = "{{ route('chat.messages') }}?user1=" + encodeURIComponent(currentUsername) + "&user2=" + encodeURIComponent(currentPartnerUsername);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.style.display = 'none';

            if (data && data.success) {
                if (data.receiver) {
                    if (data.receiver.phone_number && !currentPartnerPhone) {
                        currentPartnerPhone = data.receiver.phone_number;
                        const callBtn = document.getElementById('chatCallBtn');
                        if (callBtn) {
                            callBtn.href = 'tel:' + currentPartnerPhone.replace(/[^0-9+]/g, '');
                            callBtn.style.display = 'inline-flex';
                        }
                    }

                    const isOnline = !!data.receiver.is_online;
                    const dot = document.getElementById('chatPartnerStatusDot');
                    const txt = document.getElementById('chatPartnerStatusText');
                    if (dot) {
                        dot.style.background = isOnline ? '#10b981' : '#94a3b8';
                        dot.style.boxShadow = isOnline ? '0 0 6px #10b981' : 'none';
                        dot.title = isOnline ? 'Online / Active' : (data.receiver.last_seen_display || 'Offline');
                    }
                    if (txt) {
                        txt.style.color = isOnline ? '#86efac' : '#cbd5e1';
                        txt.innerHTML = isOnline 
                            ? '&bull; Active' 
                            : `&bull; ${data.receiver.last_seen_display || 'Offline'}`;
                    }
                }

                if (Array.isArray(data.messages)) {
                    if (data.messages.length !== lastMessageCount) {
                        const hasNewIncoming = data.messages.length > lastMessageCount && data.messages.slice(lastMessageCount).some(m => !m.is_me);
                        if (hasNewIncoming) {
                            playAudioChime();
                        }

                        renderAllMessages(data.messages);
                        lastMessageCount = data.messages.length;
                        scrollToLatestMessage();

                        // If minimized, show unread dot
                        const win = document.getElementById('chatWindow');
                        if (win && win.style.display === 'none') {
                            document.getElementById('chatUnreadDot').style.display = 'block';
                        }
                    }
                }
            }
        })
        .catch(err => {
            if (loading) loading.style.display = 'none';
            console.error('Error fetching chat messages:', err);
        });
    }

    function renderAllMessages(messages) {
        const list = document.getElementById('chatMessagesList');
        if (!list) return;

        list.innerHTML = '';
        if (messages.length === 0) {
            list.innerHTML = `
                <div style="text-align: center; padding: 30px 10px; color: #94a3b8; font-size: 12px;">
                    <i class="fa-regular fa-comments" style="font-size: 28px; margin-bottom: 8px; display: inline-block;"></i>
                    <p style="margin: 0; font-weight: 500;">No messages yet.</p>
                    <p style="margin: 4px 0 0 0; font-size: 11px;">Simulan ang pagtatanong at paglilinaw ukol sa serbisyo!</p>
                </div>
            `;
            return;
        }

        let lastDate = '';
        messages.forEach(m => {
            if (m.date && m.date !== lastDate) {
                const sep = document.createElement('div');
                sep.style.textAlign = 'center';
                sep.style.margin = '6px 0';
                sep.innerHTML = `<span style="background: #e2e8f0; color: #475569; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px;">${m.date}</span>`;
                list.appendChild(sep);
                lastDate = m.date;
            }
            appendMessageBubble(m);
        });
    }

    function appendMessageBubble(m) {
        const list = document.getElementById('chatMessagesList');
        if (!list) return;

        const isMe = m.is_me || (m.sender_username && m.sender_username.toLowerCase() === currentUsername.toLowerCase());

        const bubbleContainer = document.createElement('div');
        bubbleContainer.style.display = 'flex';
        bubbleContainer.style.flexDirection = 'column';
        bubbleContainer.style.alignItems = isMe ? 'flex-end' : 'flex-start';
        bubbleContainer.style.marginBottom = '6px';

        const bubble = document.createElement('div');
        bubble.style.maxWidth = '80%';
        bubble.style.padding = '8px 12px';
        bubble.style.borderRadius = isMe ? '14px 14px 2px 14px' : '14px 14px 14px 2px';
        bubble.style.background = isMe ? '#0033a0' : '#e2e8f0';
        bubble.style.color = isMe ? '#ffffff' : '#0f172a';
        bubble.style.fontSize = '12.5px';
        bubble.style.lineHeight = '1.4';
        bubble.style.wordBreak = 'break-word';
        bubble.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';
        bubble.innerText = m.message_text;

        const meta = document.createElement('div');
        meta.style.fontSize = '10px';
        meta.style.color = '#94a3b8';
        meta.style.marginTop = '2px';
        meta.style.padding = isMe ? '0 4px 0 0' : '0 0 0 4px';
        meta.innerText = (m.timestamp || '') + (m.timestamp ? ' (PH Time)' : '');

        bubbleContainer.appendChild(bubble);
        bubbleContainer.appendChild(meta);
        list.appendChild(bubbleContainer);
    }

    function scrollToLatestMessage() {
        const box = document.getElementById('chatMessagesBox');
        if (box) {
            setTimeout(() => {
                box.scrollTop = box.scrollHeight;
            }, 50);
        }
    }

    // 6. GLOBAL UNREAD BACKGROUND POLLER (Runs on every page every 3 seconds)
    function pollGlobalUnreadMessages() {
        if (!currentUsername) return;

        fetch("{{ route('chat.unread_count') }}?username=" + encodeURIComponent(currentUsername), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) return;

            const badge = document.getElementById('chatFloatingBadge');
            const sideBadges = document.querySelectorAll('.sidebar-chat-unread-badge');

            const count = parseInt(data.unread_count, 10) || 0;

            // Update badge UI
            if (badge) {
                if (count > 0) {
                    badge.style.display = 'flex';
                    badge.innerText = count > 99 ? '99+' : count;
                } else {
                    badge.style.display = 'none';
                }
            }

            sideBadges.forEach(sb => {
                if (count > 0) {
                    sb.style.display = 'inline-block';
                    sb.innerText = count;
                } else {
                    sb.style.display = 'none';
                }
            });

            // If a new message arrived that we haven't notified yet
            if (count > lastKnownUnreadCount && data.latest_sender_username) {
                playAudioChime();

                // Save sender info for toast
                window._latestSenderUsername = data.latest_sender_username;
                window._latestSenderFullName = data.latest_sender_name || data.latest_sender_username;
                window._latestSenderRole = data.latest_sender_role || 'User';
                window._latestSenderPhone = data.latest_sender_phone || '';

                // Show toast if chat is currently closed or chatting with someone else
                const chatWin = document.getElementById('chatWindow');
                const isCurrentChatOpen = chatWin && chatWin.style.display === 'flex' && currentPartnerUsername.toLowerCase() === data.latest_sender_username.toLowerCase();

                if (!isCurrentChatOpen) {
                    const toast = document.getElementById('chatIncomingToast');
                    const toastSender = document.getElementById('toastSenderName');
                    const toastSnippet = document.getElementById('toastMessageSnippet');
                    const toastTime = document.getElementById('toastMessageTime');

                    if (toast && toastSender && toastSnippet) {
                        toastSender.innerText = '💬 Bagong mensahe mula kay ' + (data.latest_sender_name || data.latest_sender_username);
                        toastSnippet.innerText = data.latest_message || 'May bagong mensahe ka.';
                        if (toastTime) toastTime.innerText = (data.latest_time || 'Just now') + ' (PH Time)';
                        toast.style.display = 'block';

                        // Auto-hide toast after 8 seconds
                        setTimeout(() => {
                            if (toast.style.display === 'block') {
                                toast.style.display = 'none';
                            }
                        }, 8000);
                    }
                }
            }

            lastKnownUnreadCount = count;
        })
        .catch(err => console.debug('Unread poll error:', err));
    }

    function escapeJs(str) {
        if (!str) return '';
        return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // Initialize global background listener
    document.addEventListener('DOMContentLoaded', function() {
        if (currentUsername) {
            pollGlobalUnreadMessages();
            globalUnreadPollInterval = setInterval(pollGlobalUnreadMessages, 3500);
        }
    });

})();
</script>
