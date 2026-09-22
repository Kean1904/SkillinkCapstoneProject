@php
    $modalUser = auth()->user() ?? (\App\Models\User::where('user_id', session('user_id'))->orWhere('name', session('user_name'))->first());
    $needsConsent = $modalUser && !$modalUser->privacy_consent_accepted && !session('privacy_consent_accepted');
@endphp

@if($needsConsent)
<div id="dashboardConsentModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 15, 45, 0.82); backdrop-filter: blur(6px); z-index: 999999; display: flex; justify-content: center; align-items: center; padding: 16px; box-sizing: border-box;">
    <div style="background: #ffffff; color: #1e293b; width: 100%; max-width: 580px; max-height: 92vh; overflow-y: auto; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); border-top: 6px solid #0033a0; display: flex; flex-direction: column;">
        
        <!-- Header -->
        <div style="padding: 22px 26px 16px 26px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 14px;">
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" style="width: 44px; height: 44px; object-fit: contain;">
            <div>
                <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0033a0; letter-spacing: -0.2px;">Pahintulot sa Data Privacy at Paggamit ng Impormasyon</h3>
                <p style="margin: 3px 0 0 0; font-size: 12px; color: #64748b; font-weight: 500;">SKILLINK (Web at Mobile) &bull; PESO Magalang</p>
            </div>
        </div>

        <!-- Body -->
        <div style="padding: 22px 26px; font-size: 13.5px; line-height: 1.55; color: #334155;">
            <p style="margin-top: 0; margin-bottom: 14px;">
                Kumusta, <strong>{{ $modalUser->first_name ?? $modalUser->name }}</strong>! Upang patuloy na magamit ang mga serbisyo ng SKILLINK sa Web at Mobile, kinakailangan ang iyong kumpirmasyon at pahintulot sa paggamit ng iyong personal na data alinsunod sa batas.
            </p>

            <!-- User Info Summary Box -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                    <span style="font-weight: 600; color: #64748b;">Buong Pangalan:</span>
                    <span style="font-weight: 700; color: #0f172a;">{{ $modalUser->first_name }} {{ $modalUser->last_name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                    <span style="font-weight: 600; color: #64748b;">Username:</span>
                    <span style="color: #0f172a; font-weight: 600;">{{ $modalUser->name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                    <span style="font-weight: 600; color: #64748b;">Email Address:</span>
                    <span style="color: #0f172a;">{{ $modalUser->email ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                    <span style="font-weight: 600; color: #64748b;">Cellphone:</span>
                    <span style="color: #0f172a;">{{ $modalUser->cellphone ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #cbd5e1;">
                    <span style="font-weight: 600; color: #64748b;">Tirahan (Address):</span>
                    <span style="color: #0f172a; text-align: right; max-width: 65%;">{{ $modalUser->address ?? 'N/A' }} {{ !empty($modalUser->barangay) ? ', Brgy. ' . $modalUser->barangay : '' }}, Magalang</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: 600; color: #64748b;">Uri ng Account (Role):</span>
                    <span style="font-weight: 700; color: #0033a0; text-transform: uppercase;">{{ $modalUser->role }}</span>
                </div>
            </div>

            <!-- Legal Privacy Act Statement -->
            <div style="background: #eff6ff; border-left: 4px solid #2563eb; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-bottom: 18px;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #1e40af; margin-bottom: 6px; font-size: 13px;">
                    <i class="fa-solid fa-shield-halved"></i> Data Privacy Act of 2012 (RA 10173)
                </div>
                <p style="margin: 0; font-size: 12px; color: #1e3a8a; line-height: 1.5;">
                    Alinsunod sa <strong>Republic Act No. 10173</strong>, ako ay nagbibigay ng kusang-loob at malinaw na pahintulot sa <strong>SKILLINK</strong> (sa <strong>Web System</strong> at <strong>Mobile App</strong>) at sa <strong>PESO Magalang</strong> upang gamitin, iproseso, at itala ang aking mga personal na impormasyon para sa layunin ng pagpapatunay ng aking profile, pag-ugnay sa trabaho at serbisyo, at mga opisyal na operasyon ng munisipalidad.
                </p>
            </div>

            <!-- Checkbox -->
            <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; user-select: none; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px;">
                <input type="checkbox" id="dashboardConsentCheckbox" onchange="toggleDashboardConsentBtn()" style="width: 19px; height: 19px; margin-top: 2px; accent-color: #0033a0; cursor: pointer;">
                <span style="font-size: 13px; color: #92400e; font-weight: 600; line-height: 1.45;">
                    Pinatutunayan ko na akin ang impormasyong nasa itaas at pinapahintulutan ko ang paggamit nito sa Web at Mobile application ng SKILLINK.
                </span>
            </label>
        </div>

        <!-- Footer -->
        <div style="padding: 16px 26px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <a href="{{ route('logout') }}" style="text-decoration: none; padding: 10px 16px; border: 1px solid #cbd5e1; background: #ffffff; color: #64748b; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-right-from-bracket"></i> Mag-logout
            </a>
            <button type="button" id="dashboardConsentSubmitBtn" onclick="submitDashboardConsent()" disabled style="padding: 11px 22px; border: none; background: #0033a0; color: #ffffff; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: not-allowed; opacity: 0.5; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fa-solid fa-check-circle"></i> Tanggapin at Magpatuloy
            </button>
        </div>

    </div>
</div>

<script>
function toggleDashboardConsentBtn() {
    const chk = document.getElementById('dashboardConsentCheckbox');
    const btn = document.getElementById('dashboardConsentSubmitBtn');
    if (chk && chk.checked) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    }
}

function submitDashboardConsent() {
    const btn = document.getElementById('dashboardConsentSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sine-save...';

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
                modal.style.transition = 'opacity 0.3s ease';
                modal.style.opacity = '0';
                setTimeout(() => modal.remove(), 300);
            }
        } else {
            alert(data.message || 'May naganap na error. Pakisubukang muli.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Tanggapin at Magpatuloy';
        }
    })
    .catch(err => {
        console.error('Consent error:', err);
        alert('Hindi ma-save ang pahintulot. Pakisubukan muli o mag-refresh.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Tanggapin at Magpatuloy';
    });
}
</script>
@endif
