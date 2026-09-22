@php
    $modalUser = auth()->user() ?? (\App\Models\User::where('user_id', session('user_id'))->orWhere('name', session('user_name'))->first());
    $needsConsent = $modalUser && !$modalUser->privacy_consent_accepted && !session('privacy_consent_accepted');
@endphp

@if($needsConsent)
<div id="dashboardConsentModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 999999; display: flex; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;">
    <div style="background: #ffffff; color: #1e293b; width: 100%; max-width: 380px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1); padding: 24px; box-sizing: border-box; position: relative; animation: popIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        
        <!-- Top Row: Round Icon & Top-Right Close / Logout Button -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 50%; background: #eff6ff; border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <a href="{{ route('logout') }}" title="Mag-logout" style="width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e5e7eb; background: #ffffff; color: #9ca3af; font-size: 13px; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.color='#111827'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.color='#9ca3af'; this.style.borderColor='#e5e7eb';">
                <i class="fa-solid fa-xmark"></i>
            </a>
        </div>

        <!-- Title & Message -->
        <h3 style="margin: 0 0 8px 0; font-size: 16.5px; font-weight: 700; color: #111827; text-align: left; line-height: 1.3;">
            Pahintulot sa Data Privacy
        </h3>
        <p style="margin: 0 0 22px 0; font-size: 13px; color: #6b7280; line-height: 1.5; text-align: left;">
            Kumusta, <strong>{{ $modalUser->first_name ?? $modalUser->name }}</strong>! Alinsunod sa <strong>Data Privacy Act (RA 10173)</strong>, pinapahintulutan mo ba ang SKILLINK (Web at Mobile) at PESO Magalang na gamitin ang iyong impormasyon para sa mga opisyal na serbisyo?
        </p>

        <!-- Buttons: Side by Side (Cancel/Logout & Confirm) -->
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('logout') }}" style="flex: 1; padding: 10px 16px; border: 1px solid #e5e7eb; background: #ffffff; color: #374151; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='#ffffff';">
                <i class="fa-solid fa-xmark"></i> Cancel
            </a>
            <button type="button" id="dashboardConsentSubmitBtn" onclick="submitDashboardConsent()" style="flex: 1; padding: 10px 16px; border: none; background: #4f46e5; color: #ffffff; border-radius: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.35); transition: background 0.2s;" onmouseover="this.style.background='#4338ca';" onmouseout="this.style.background='#4f46e5';">
                <i class="fa-solid fa-check"></i> Confirm
            </button>
        </div>

    </div>
</div>

<script>
function submitDashboardConsent() {
    const btn = document.getElementById('dashboardConsentSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ...';
    }

    fetch("{{ route('user.consent.accept') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ consent: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = document.getElementById('dashboardConsentModal');
            if (modal) {
                modal.style.transition = 'opacity 0.25s ease';
                modal.style.opacity = '0';
                setTimeout(() => modal.remove(), 250);
            }
        } else {
            alert(data.message || 'May naganap na error.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm';
            }
        }
    })
    .catch(err => {
        console.error('Consent error:', err);
        const modal = document.getElementById('dashboardConsentModal');
        if (modal) modal.remove();
    });
}
</script>
@endif

