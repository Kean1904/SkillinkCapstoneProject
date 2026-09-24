<!-- REUSABLE DIRECT SERVICE BOOKING MODAL WITH DYNAMIC AVAILABILITY CALENDAR & FIXED RATE -->
<div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 3000; align-items: center; justify-content: center; padding: 15px; overflow-y: auto;">
    <div style="background: #1e3a8a; border: 4px solid #0033a0; border-radius: 16px; padding: 22px; width: 100%; max-width: 520px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.5); max-height: 92vh; overflow-y: auto;">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px;">
            <div>
                <h3 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-check" style="color: #60a5fa;"></i> BOOK SKILLED WORKER
                </h3>
                <p style="font-size: 11.5px; opacity: 0.85; margin: 2px 0 0 0;">Direct Service Booking via PESO Magalang</p>
            </div>
            <button type="button" onclick="closeBookModal()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 15px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Worker Summary -->
        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #0033a0; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; border: 2px solid #60a5fa;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div style="flex: 1;">
                <strong id="bookWorkerName" style="font-size: 15px; color: #ffffff; display: block;">Worker Name</strong>
                <span id="bookWorkerSkills" style="font-size: 12px; color: #93c5fd;">Skills: General Handyman</span>
            </div>
        </div>

        <form method="POST" action="{{ route('residential.booking.create') }}" id="serviceBookingForm" onsubmit="return validateBookingSchedule()">
            @csrf
            <input type="hidden" name="workerUsername" id="bookWorkerUsername">
            <input type="hidden" name="estimatedBudget" id="bookEstimatedBudget" value="₱500.00">
            <input type="hidden" name="scheduledDate" id="bookScheduledDate" required>

            <!-- 1. FIXED ESTIMATED SERVICE RATE CARD (DICTATED BY WORKER) -->
            <div style="margin-bottom: 14px; background: rgba(16, 185, 129, 0.18); border: 2px solid #10b981; border-radius: 10px; padding: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 10.5px; font-weight: bold; letter-spacing: 0.5px; color: #86efac; text-transform: uppercase; display: block;">
                            <i class="fa-solid fa-lock"></i> Fixed Service Rate (Naka-set ng Worker)
                        </span>
                        <div id="bookFixedRateDisplay" style="font-size: 22px; font-weight: 900; color: #ffffff; margin-top: 2px;">
                            ₱500.00
                        </div>
                    </div>
                    <span style="background: #10b981; color: white; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-shield-halved"></i> Fixed Rate
                    </span>
                </div>
                <p style="font-size: 11px; opacity: 0.85; margin: 4px 0 0 0; color: #d1fae5;">
                    * Fixed ang halagang ito ayon sa rate ni skilled worker at hindi maaaring baguhin o bawasan ng household client.
                </p>
            </div>

            <!-- 2. SERVICE DETAILS -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 11.5px; font-weight: bold; margin-bottom: 4px; color: #e2e8f0;">Service Category</label>
                <input type="text" name="serviceCategory" id="bookServiceCategory" style="width: 100%; padding: 8px 12px; border-radius: 6px; background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.35); font-size: 13px;" required>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 11.5px; font-weight: bold; margin-bottom: 4px; color: #e2e8f0;">Task Details / Sira na Aayusin</label>
                <textarea name="taskDescription" id="bookTaskDescription" rows="2" style="width: 100%; padding: 8px 12px; border-radius: 6px; background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.35); font-size: 13px;" placeholder="Halimbawa: Tumutulong tubo sa lababo at baradong drain..." required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 11.5px; font-weight: bold; margin-bottom: 4px; color: #e2e8f0;">Service Address</label>
                    <input type="text" name="serviceAddress" id="bookServiceAddress" placeholder="Lot / House No. / Street" style="width: 100%; padding: 8px 12px; border-radius: 6px; background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.35); font-size: 13px;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 11.5px; font-weight: bold; margin-bottom: 4px; color: #e2e8f0;">Barangay (Magalang)</label>
                    <input type="text" name="barangay" id="bookBarangay" value="San Nicolas 1st" style="width: 100%; padding: 8px 12px; border-radius: 6px; background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.35); font-size: 13px;" required>
                </div>
            </div>

            <!-- 3. DYNAMIC INTERACTIVE AVAILABILITY CALENDAR -->
            <div style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; padding: 14px; margin-bottom: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: bold; color: #93c5fd; text-transform: uppercase;">
                        <i class="fa-regular fa-calendar-days"></i> Availability Calendar ni Worker
                    </span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <button type="button" onclick="calPrevMonth()" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 4px; width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                        </button>
                        <span id="calMonthYearLabel" style="font-size: 12px; font-weight: bold; min-width: 115px; text-align: center;">Month 2026</span>
                        <button type="button" onclick="calNextMonth()" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 4px; width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Weekday Headers -->
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 10.5px; font-weight: bold; color: #94a3b8; margin-bottom: 6px;">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>

                <!-- Days Grid -->
                <div id="calendarDaysGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 10px;">
                    <!-- Days dynamically rendered by JS -->
                </div>

                <!-- Preferred Time Slots -->
                <div style="border-top: 1px solid rgba(255,255,255,0.15); padding-top: 8px;">
                    <span style="font-size: 11px; font-weight: bold; color: #e2e8f0; display: block; margin-bottom: 6px;">
                        Preferred Time Slot:
                    </span>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;" id="timeSlotContainer">
                        <button type="button" class="time-slot-btn active" onclick="selectTimeSlot('08:00 AM', this)" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; border: 1px solid #60a5fa; background: #0033a0; color: white; cursor: pointer; font-weight: bold;">08:00 AM</button>
                        <button type="button" class="time-slot-btn" onclick="selectTimeSlot('10:00 AM', this)" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white; cursor: pointer;">10:00 AM</button>
                        <button type="button" class="time-slot-btn" onclick="selectTimeSlot('01:00 PM', this)" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white; cursor: pointer;">01:00 PM</button>
                        <button type="button" class="time-slot-btn" onclick="selectTimeSlot('03:00 PM', this)" style="padding: 5px 9px; font-size: 11px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white; cursor: pointer;">03:00 PM</button>
                    </div>
                </div>

                <!-- Selected Schedule Display Indicator -->
                <div id="calSelectedDisplay" style="margin-top: 8px; font-size: 11.5px; font-weight: bold; color: #fde047; background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.4); border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-clock"></i> Paki-pili ang petsa mula sa available (clickable) numbers sa calendar sa itaas.
                </div>

                <!-- Calendar Legend -->
                <div style="display: flex; gap: 12px; margin-top: 8px; font-size: 10.5px; opacity: 0.85; flex-wrap: wrap;">
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Available (Clickable)
                    </span>
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <span style="width: 10px; height: 10px; border-radius: 3px; background: #0033a0; border: 1px solid #60a5fa; display: inline-block;"></span> Napili
                    </span>
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <span style="width: 10px; height: 10px; border-radius: 3px; background: #334155; border: 1px dashed #64748b; display: inline-block;"></span> May Book Na (Gray / Hindi Clickable)
                    </span>
                </div>
            </div>

            <!-- Error message container -->
            <div id="bookingModalError" style="display: none; background: rgba(239, 68, 68, 0.25); border: 1px solid #f87171; color: #fecaca; padding: 8px 12px; border-radius: 6px; font-size: 12px; margin-bottom: 12px;"></div>

            <!-- Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white; padding: 9px 18px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px;" onclick="closeBookModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitBooking" style="background: #0033a0; border: 1px solid #60a5fa; color: white; padding: 9px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-paper-plane"></i> Confirm & Book
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Dynamic Calendar & Booking State Manager
let calCurrentYear = new Date().getFullYear();
let calCurrentMonth = new Date().getMonth(); // 0 = Jan, 11 = Dec
let calSelectedDate = null; // YYYY-MM-DD
let calSelectedTime = '08:00 AM';
let calBookedDates = []; // List of YYYY-MM-DD where worker has existing bookings

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

function openBookModal(username, fullName, skills, fixedRate) {
    document.getElementById('bookWorkerUsername').value = username;
    document.getElementById('bookWorkerName').innerText = fullName + ' (@' + username + ')';
    document.getElementById('bookWorkerSkills').innerText = 'Skills: ' + (skills ? skills : 'General Handyman');
    document.getElementById('bookServiceCategory').value = skills ? skills.split(',')[0].trim() : 'Home Repair';
    
    // Set worker's fixed rate
    const rateText = fixedRate ? fixedRate : '₱500.00';
    document.getElementById('bookFixedRateDisplay').innerText = rateText;
    document.getElementById('bookEstimatedBudget').value = rateText;

    // Reset calendar selection
    calCurrentYear = new Date().getFullYear();
    calCurrentMonth = new Date().getMonth();
    calSelectedDate = null;
    calSelectedTime = '08:00 AM';
    calBookedDates = [];
    document.getElementById('bookScheduledDate').value = '';
    document.getElementById('calSelectedDisplay').innerHTML = '<i class="fa-solid fa-clock"></i> Paki-pili ang petsa mula sa available (clickable) numbers sa calendar sa itaas.';
    document.getElementById('bookingModalError').style.display = 'none';

    // Reset time buttons
    document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.style.background = 'rgba(255,255,255,0.1)';
        btn.style.border = '1px solid rgba(255,255,255,0.3)';
    });
    const firstTimeBtn = document.querySelector('.time-slot-btn');
    if (firstTimeBtn) {
        firstTimeBtn.style.background = '#0033a0';
        firstTimeBtn.style.border = '1px solid #60a5fa';
    }

    document.getElementById('bookingModal').style.display = 'flex';

    // Fetch existing booked dates for this worker
    fetch("{{ url('/residential/worker-booked-dates') }}/" + encodeURIComponent(username), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.booked_dates) {
            calBookedDates = data.booked_dates;
        }
        renderCalendarGrid();
    })
    .catch(err => {
        console.warn('Could not fetch booked dates for worker:', err);
        renderCalendarGrid();
    });

    renderCalendarGrid();
}

function closeBookModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

function calPrevMonth() {
    const today = new Date();
    // Do not navigate back past the current month/year
    if (calCurrentYear === today.getFullYear() && calCurrentMonth <= today.getMonth()) {
        return;
    }
    calCurrentMonth--;
    if (calCurrentMonth < 0) {
        calCurrentMonth = 11;
        calCurrentYear--;
    }
    renderCalendarGrid();
}

function calNextMonth() {
    calCurrentMonth++;
    if (calCurrentMonth > 11) {
        calCurrentMonth = 0;
        calCurrentYear++;
    }
    renderCalendarGrid();
}

function selectTimeSlot(time, element) {
    calSelectedTime = time;
    document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.style.background = 'rgba(255,255,255,0.1)';
        btn.style.border = '1px solid rgba(255,255,255,0.3)';
    });
    element.style.background = '#0033a0';
    element.style.border = '1px solid #60a5fa';

    updateSelectedScheduleDisplay();
}

function renderCalendarGrid() {
    const grid = document.getElementById('calendarDaysGrid');
    if (!grid) return;
    grid.innerHTML = '';

    document.getElementById('calMonthYearLabel').innerText = monthNames[calCurrentMonth] + ' ' + calCurrentYear;

    const firstDayIndex = new Date(calCurrentYear, calCurrentMonth, 1).getDay();
    const daysInMonth = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();

    const today = new Date();
    today.setHours(0,0,0,0);

    // Empty cells before the 1st
    for (let i = 0; i < firstDayIndex; i++) {
        const blank = document.createElement('div');
        blank.style.height = '36px';
        grid.appendChild(blank);
    }

    // Days in current month
    for (let d = 1; d <= daysInMonth; d++) {
        const cellDate = new Date(calCurrentYear, calCurrentMonth, d);
        cellDate.setHours(0,0,0,0);

        const yearStr = calCurrentYear;
        const monthStr = String(calCurrentMonth + 1).padStart(2, '0');
        const dayStr = String(d).padStart(2, '0');
        const isoDate = `${yearStr}-${monthStr}-${dayStr}`;

        // Format for human reading (e.g. Sep 27, 2026)
        const monthShort = monthNames[calCurrentMonth].substring(0, 3);
        const readableDate = `${monthShort} ${dayStr}, ${yearStr}`;

        const isPast = cellDate < today;
        
        // Check if date is booked
        const isBooked = calBookedDates.some(bd => {
            if (!bd) return false;
            // Match ISO YYYY-MM-DD or readable substring
            return bd === isoDate || bd.includes(isoDate) || bd.includes(`${monthShort} ${dayStr}`) || bd.includes(`${monthShort} ${d},`);
        });

        const isSelected = calSelectedDate === isoDate;

        const cell = document.createElement('div');
        cell.style.height = '36px';
        cell.style.display = 'flex';
        cell.style.flexDirection = 'column';
        cell.style.alignItems = 'center';
        cell.style.justifyContent = 'center';
        cell.style.borderRadius = '6px';
        cell.style.fontSize = '12px';
        cell.style.position = 'relative';
        cell.style.transition = 'all 0.15s ease';

        if (isPast) {
            // Past dates: dimmed, disabled
            cell.innerText = d;
            cell.style.color = '#64748b';
            cell.style.background = 'transparent';
            cell.style.cursor = 'not-allowed';
            cell.style.opacity = '0.35';
        } else if (isBooked) {
            // BOOKED DATE: Grayed out, strikethrough, not clickable
            cell.innerText = d;
            cell.style.color = '#94a3b8';
            cell.style.background = '#334155';
            cell.style.border = '1px dashed #64748b';
            cell.style.textDecoration = 'line-through';
            cell.style.cursor = 'not-allowed';
            cell.style.opacity = '0.5';
            cell.title = 'Hindi Available: May naunang booking na sa araw na ito.';

            const dot = document.createElement('span');
            dot.style.position = 'absolute';
            dot.style.bottom = '2px';
            dot.style.width = '4px';
            dot.style.height = '4px';
            dot.style.borderRadius = '50%';
            dot.style.background = '#ef4444';
            cell.appendChild(dot);
        } else {
            // AVAILABLE DATE: Clickable!
            cell.innerText = d;
            cell.style.cursor = 'pointer';

            if (isSelected) {
                cell.style.background = '#0033a0';
                cell.style.border = '2px solid #60a5fa';
                cell.style.color = '#ffffff';
                cell.style.fontWeight = 'bold';
                cell.style.boxShadow = '0 0 8px rgba(96, 165, 250, 0.6)';
            } else {
                cell.style.background = 'rgba(255,255,255,0.08)';
                cell.style.border = '1px solid rgba(255,255,255,0.2)';
                cell.style.color = '#ffffff';

                cell.onmouseover = () => { cell.style.background = '#2563eb'; };
                cell.onmouseout = () => { 
                    if (calSelectedDate !== isoDate) {
                        cell.style.background = 'rgba(255,255,255,0.08)';
                    }
                };

                const dot = document.createElement('span');
                dot.style.position = 'absolute';
                dot.style.bottom = '2px';
                dot.style.width = '4px';
                dot.style.height = '4px';
                dot.style.borderRadius = '50%';
                dot.style.background = '#10b981';
                cell.appendChild(dot);
            }

            cell.onclick = () => {
                calSelectedDate = isoDate;
                renderCalendarGrid();
                updateSelectedScheduleDisplay();
            };
        }

        grid.appendChild(cell);
    }
}

function updateSelectedScheduleDisplay() {
    if (!calSelectedDate) return;

    const parts = calSelectedDate.split('-');
    const year = parts[0];
    const monthIndex = parseInt(parts[1], 10) - 1;
    const day = parts[2];
    const readable = `${monthNames[monthIndex].substring(0, 3)} ${day}, ${year}`;

    const fullSchedule = `${readable} (${calSelectedTime})`;
    document.getElementById('bookScheduledDate').value = fullSchedule;

    const display = document.getElementById('calSelectedDisplay');
    display.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #4ade80;"></i> <span style="color: white;">Napiling Schedule:</span> <strong style="color: #60a5fa;">${fullSchedule}</strong>`;
    display.style.background = 'rgba(37, 99, 235, 0.2)';
    display.style.border = '1px solid #3b82f6';
}

function validateBookingSchedule() {
    const sched = document.getElementById('bookScheduledDate').value;
    const errBox = document.getElementById('bookingModalError');
    if (!sched || sched.trim() === '') {
        errBox.innerText = 'Paki-pili muna ang available na petsa mula sa calendar bago i-submit ang booking.';
        errBox.style.display = 'block';
        return false;
    }
    errBox.style.display = 'none';
    return true;
}
</script>
