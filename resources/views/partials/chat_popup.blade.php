<!-- SKILLINK In-App Messaging Pop-up (Facebook Messenger Style) -->
<div id="skillinkChatFloatingContainer" style="position: fixed; bottom: 0; right: 25px; z-index: 9999; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Minimized Floating Bar / Pill (Visible when chat is minimized or closed with active contact) -->
    <div id="chatMinimizedBar" onclick="restoreChatModal()" style="display: none; align-items: center; gap: 10px; background: #0033a0; color: white; padding: 10px 18px; border-radius: 24px 24px 0 0; cursor: pointer; box-shadow: 0 -4px 15px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.3); border-bottom: none; user-select: none;">
        <div style="position: relative;">
            <i class="fa-solid fa-comments" style="font-size: 16px; color: #fde047;"></i>
            <span id="chatUnreadDot" style="display: none; position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 1px solid white;"></span>
        </div>
        <span id="chatMinimizedTitle" style="font-size: 13px; font-weight: 600; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Chat with Worker</span>
        <button type="button" onclick="event.stopPropagation(); closeChatModal();" title="Close chat" style="background: transparent; border: none; color: rgba(255,255,255,0.8); cursor: pointer; font-size: 13px; margin-left: 4px; padding: 0 4px;">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Active Messenger-style Window -->
    <div id="chatWindow" style="display: none; width: 340px; max-width: calc(100vw - 30px); background: #ffffff; border-radius: 12px 12px 0 0; box-shadow: 0 10px 30px rgba(0,0,0,0.35); border: 1px solid rgba(0,0,0,0.15); border-bottom: none; overflow: hidden; flex-direction: column;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #002b80 0%, #0033a0 100%); color: white; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #fde047;">
            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                <div style="position: relative; flex-shrink: 0;">
                    <div id="chatAvatarBadge" style="width: 36px; height: 36px; border-radius: 50%; background: #ffffff; color: #0033a0; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 2px solid #60a5fa;">
                        W
                    </div>
                    <span style="position: absolute; bottom: 0; right: 0; width: 9px; height: 9px; background: #10b981; border-radius: 50%; border: 1.5px solid white;"></span>
                </div>
                <div style="overflow: hidden; line-height: 1.25;">
                    <div id="chatWorkerFullName" style="font-size: 13.5px; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff;">Skilled Worker</div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                        <span id="chatWorkerTradeBadge" style="font-size: 10.5px; color: #fde047; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Handyman</span>
                        <span style="font-size: 10px; color: #86efac; opacity: 0.9;">&bull; Active</span>
                    </div>
                </div>
            </div>

            <!-- Controls (Call shortcut, Minimize, Close) -->
            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                <a id="chatCallBtn" href="javascript:void(0)" title="Call Worker" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.18); color: #86efac; text-decoration: none; font-size: 12px; transition: background 0.2s;">
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
                <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i> <strong>Magalang PESO In-App Messaging</strong><br>
                Makipag-ugnayan muna upang linawin ang kailangan, materyales, o iskedyul bago mag-book.
            </div>
            <div id="chatLoadingState" style="text-align: center; padding: 20px; font-size: 12px; color: #64748b;">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading conversation...
            </div>
            <div id="chatMessagesList" style="display: flex; flex-direction: column; gap: 8px;"></div>
        </div>

        <!-- Pre-booking Quick Replies (Pills) -->
        <div id="chatQuickReplies" style="padding: 6px 10px; background: #f1f5f9; border-top: 1px solid #e2e8f0; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
            <button type="button" onclick="sendQuickReply('Available po ba kayo ngayong linggo?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Available po ba kayo?
            </button>
            <button type="button" onclick="sendQuickReply('Kailangan po ba ako ang bibili ng materyales?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Sino po magpo-provide ng gamit?
            </button>
            <button type="button" onclick="sendQuickReply('Pwede po ba magpa-estimate muna bago mag-book?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #334155; cursor: pointer; flex-shrink: 0;">
                Estimate clarification
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

<script>
(function() {
    let currentPartnerUsername = '';
    let currentPartnerFullName = '';
    let currentPartnerTrade = '';
    let currentPartnerPhone = '';
    let chatPollInterval = null;
    let isSending = false;
    let lastMessageCount = 0;

    // Current logged-in user
    const currentUsername = "{{ session('user_name') ?? '' }}";

    window.openChatModal = function(partnerUsername, partnerFullName, partnerTrade, partnerPhone) {
        if (!partnerUsername) return;

        currentPartnerUsername = partnerUsername;
        currentPartnerFullName = partnerFullName || partnerUsername;
        currentPartnerTrade = partnerTrade || 'Skilled Worker';
        currentPartnerPhone = partnerPhone || '';

        // Update Header UI
        document.getElementById('chatWorkerFullName').innerText = currentPartnerFullName;
        document.getElementById('chatWorkerTradeBadge').innerText = currentPartnerTrade;
        document.getElementById('chatMinimizedTitle').innerText = 'Chat with ' + currentPartnerFullName;

        // Avatar Initial
        const initials = (currentPartnerFullName.split(' ').map(w => w[0]).join('').substring(0, 2) || 'W').toUpperCase();
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

        // Start polling every 3 seconds while open
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

        // Optimistically render message
        appendMessageBubble({
            sender_username: currentUsername,
            message_text: text,
            is_me: true,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
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
                // Optionally refresh message list
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

            if (data && data.success && Array.isArray(data.messages)) {
                // If receiver details were returned, update phone if previously empty
                if (data.receiver && data.receiver.phone_number && !currentPartnerPhone) {
                    currentPartnerPhone = data.receiver.phone_number;
                    const callBtn = document.getElementById('chatCallBtn');
                    if (callBtn) {
                        callBtn.href = 'tel:' + currentPartnerPhone.replace(/[^0-9+]/g, '');
                        callBtn.style.display = 'inline-flex';
                    }
                }

                if (data.messages.length !== lastMessageCount) {
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
                    <p style="margin: 4px 0 0 0; font-size: 11px;">Simulan ang pagtatanong bago mag-book ng serbisyo!</p>
                </div>
            `;
            return;
        }

        messages.forEach(m => {
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
        meta.innerText = m.timestamp || '';

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
})();
</script>
