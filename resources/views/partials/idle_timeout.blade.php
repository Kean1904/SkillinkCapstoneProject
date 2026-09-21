<!-- ═════════════════════════════════════════════════════════════════════════
     SKILLINK (PESO Magalang) — Automatic Inactivity Logout Security Modal
     Anti-Data-Leakage Safeguard for Admin & PESO Staff Portals
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="idleTimeoutOverlay" style="display: none; position: fixed; inset: 0; background: rgba(5, 15, 38, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 999999; align-items: center; justify-content: center; animation: fadeIn 0.3s ease;">
    <div id="idleTimeoutCard" style="background: linear-gradient(145deg, #0b1e3f, #071329); border: 1.5px solid rgba(239, 68, 68, 0.5); border-radius: 20px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 35px rgba(239, 68, 68, 0.2); width: 92%; max-width: 480px; padding: 32px 28px; text-align: center; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; position: relative;">
        
        <!-- Pulsing Warning Icon -->
        <div style="width: 76px; height: 76px; margin: 0 auto 20px; background: rgba(239, 68, 68, 0.15); border: 2px solid #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(239, 68, 68, 0.35);">
            <i class="fa-solid fa-shield-halved" style="font-size: 34px; color: #ef4444;"></i>
        </div>

        <h3 style="margin: 0 0 8px; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; color: #ffffff;">
            SECURITY INACTIVITY WARNING
        </h3>
        
        <p style="margin: 0 0 18px; font-size: 14px; line-height: 1.5; color: rgba(255, 255, 255, 0.85);">
            No activity has been recorded. To prevent the <strong>leakage of sensitive information (Data Privacy Safeguard)</strong>, you will be automatically logged out and redirected to the Main Dashboard.
        </p>

        <!-- Countdown Timer Display -->
        <div style="background: rgba(255, 255, 255, 0.07); border: 1px dashed rgba(239, 68, 68, 0.6); border-radius: 12px; padding: 14px; margin-bottom: 22px;">
            <span style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255, 255, 255, 0.7); display: block; margin-bottom: 4px;">Automatic Logout out:</span>
            <span id="idleCountdownTimer" style="font-size: 36px; font-weight: 900; color: #f87171; letter-spacing: 1px; font-variant-numeric: tabular-nums;">20s</span>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <button id="btnKeepActive" onclick="resetIdleTimer()" style="flex: 1; min-width: 170px; background: #0047ab; color: #ffffff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0, 71, 171, 0.4);">
                <i class="fa-solid fa-hand"></i> Maintain to Active
            </button>
            <button id="btnImmediateLogout" onclick="triggerIdleLogout()" style="background: rgba(255, 255, 255, 0.12); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); padding: 12px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                <i class="fa-solid fa-right-from-bracket"></i> Logout Now
            </button>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
#btnKeepActive:hover {
    background: #0033a0 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 71, 171, 0.6) !important;
}
#btnImmediateLogout:hover {
    background: rgba(239, 68, 68, 0.25) !important;
    color: #ffffff !important;
}
</style>

<script>
(function() {
    // ═════════════════════════════════════════════════════════════════════════
    // IDLE TIMEOUT CONFIGURATION
    // ═════════════════════════════════════════════════════════════════════════
    // Total inactive time bago tuluyang mag-logout (Default: 2 minuto / 120 segundo)
    const IDLE_LIMIT_MS = 1800 * 1000; 
    
    // Warning duration bago ang mismong logout (20 segundo bago mag 2 minuto)
    const WARNING_TIME_MS = 20 * 1000; 
    
    const LOGOUT_URL = "{{ route('logout') }}?reason=idle&redirect=dashboard";

    let lastActivityTime = Date.now();
    let isWarningShown = false;
    let tickerInterval = null;

    const overlay = document.getElementById('idleTimeoutOverlay');
    const countdownEl = document.getElementById('idleCountdownTimer');

    function updateActivity() {
        lastActivityTime = Date.now();
        if (isWarningShown) {
            hideWarningModal();
        }
    }

    // Pakikinig sa user interactions (mouse, keyboard, scroll, touch)
    const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
    activityEvents.forEach(function(evt) {
        window.addEventListener(evt, updateActivity, { passive: true });
    });

    function showWarningModal() {
        isWarningShown = true;
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }

    function hideWarningModal() {
        isWarningShown = false;
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    window.resetIdleTimer = function() {
        updateActivity();
    };

    window.triggerIdleLogout = function() {
        window.location.href = LOGOUT_URL;
    };

    // Heartbeat check bawat segundo
    tickerInterval = setInterval(function() {
        const timePassed = Date.now() - lastActivityTime;
        const timeLeftMs = IDLE_LIMIT_MS - timePassed;

        if (timeLeftMs <= 0) {
            clearInterval(tickerInterval);
            triggerIdleLogout();
            return;
        }

        if (timeLeftMs <= WARNING_TIME_MS) {
            if (!isWarningShown) {
                showWarningModal();
            }
            const secondsLeft = Math.ceil(timeLeftMs / 1000);
            if (countdownEl) {
                countdownEl.textContent = secondsLeft + 's';
            }
        } else {
            if (isWarningShown) {
                hideWarningModal();
            }
        }
    }, 1000);

    // Kapag binalikan ang browser tab pagkatapos ma-idle
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            const timePassed = Date.now() - lastActivityTime;
            if (timePassed >= IDLE_LIMIT_MS) {
                triggerIdleLogout();
            }
        }
    });
})();
</script>
