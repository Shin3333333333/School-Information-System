@extends('layouts.app')

@section('title', 'Calendar — School Information System')

@section('page-title')
<h2>School Calendar</h2>
@endsection

@section('content')

<style>
    .calendar-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--gray-100);
    }

    .calendar-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cal-nav-btn {
        width: 32px;
        height: 32px;
        border: 1px solid var(--gray-200);
        background: var(--white);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--gray-600);
        transition: all .15s;
        font-size: 14px;
    }
    .cal-nav-btn:hover { background: var(--gray-100); border-color: var(--gray-300); }

    .cal-month-label {
        font-family: var(--font-display);
        font-size: 15px;
        font-weight: 700;
        color: var(--gray-800);
        min-width: 160px;
        text-align: center;
    }

    .calendar-grid {
        padding: 16px 20px 20px;
    }

    .cal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        margin-bottom: 8px;
    }

    .cal-weekday {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--gray-400);
        letter-spacing: .5px;
        text-transform: uppercase;
        padding: 6px 0;
    }

    .cal-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .cal-day {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 6px 4px 4px;
        border-radius: 8px;
        cursor: pointer;
        position: relative;
        transition: background .12s;
        min-height: 52px;
    }
    .cal-day:hover { background: var(--gray-100); }
    .cal-day.other-month .cal-day-num { color: var(--gray-300); }
    .cal-day.today .cal-day-num {
        background: var(--blue-600);
        color: var(--white);
        border-radius: 50%;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cal-day.selected { background: var(--blue-50); }

    .cal-day-num {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        line-height: 1;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cal-day-dots {
        display: flex;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 3px;
    }

    .cal-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .cal-dot.academic { background: #1d4ed8; }
    .cal-dot.admin    { background: #d97706; }
    .cal-dot.holiday  { background: #16a34a; }
    .cal-dot.activity { background: #dc2626; }

    /* Event List Panel */
    .events-panel { display: flex; flex-direction: column; gap: 12px; }

    .events-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--gray-100);
    }

    .events-panel-title {
        font-family: var(--font-display);
        font-size: 14px;
        font-weight: 700;
        color: var(--gray-800);
    }

    .events-panel-date {
        font-size: 12px;
        color: var(--gray-400);
    }

    .events-list {
        padding: 12px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 420px;
        overflow-y: auto;
    }

    .event-card {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--gray-100);
        cursor: pointer;
        transition: all .15s;
        background: var(--white);
    }
    .event-card:hover { border-color: var(--blue-200); background: var(--blue-50); }

    .event-card-bar {
        width: 3px;
        border-radius: 2px;
        align-self: stretch;
        flex-shrink: 0;
    }
    .event-card-bar.academic { background: #1d4ed8; }
    .event-card-bar.admin    { background: #d97706; }
    .event-card-bar.holiday  { background: #16a34a; }
    .event-card-bar.activity { background: #dc2626; }

    .event-card-body { flex: 1; min-width: 0; }
    .event-card-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-800);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .event-card-desc {
        font-size: 11.5px;
        color: var(--gray-400);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-type-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 600;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .event-type-badge.academic { background: #dbeafe; color: #1d4ed8; }
    .event-type-badge.admin    { background: #fef3c7; color: #d97706; }
    .event-type-badge.holiday  { background: #dcfce7; color: #16a34a; }
    .event-type-badge.activity { background: #fee2e2; color: #dc2626; }

    .no-events {
        text-align: center;
        color: var(--gray-400);
        font-size: 13px;
        padding: 30px 0;
    }

    @media (max-width: 860px) {
        .calendar-layout { grid-template-columns: 1fr; }
    }
</style>

{{-- Top Bar --}}
<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <button class="btn btn-primary" id="openPostModal">+ Add Event</button>
</div>

<div class="calendar-layout">

    {{-- Calendar Card --}}
    <div class="card">
        <div class="calendar-header">
            <div class="calendar-nav">
                <button class="cal-nav-btn" id="prevMonth">&#8249;</button>
                <span class="cal-month-label" id="monthLabel"></span>
                <button class="cal-nav-btn" id="nextMonth">&#8250;</button>
            </div>
            <button class="btn btn-outline" id="todayBtn" style="font-size:12px; padding:6px 12px;">Today</button>
        </div>
        <div class="calendar-grid">
            <div class="cal-weekdays">
                <div class="cal-weekday">Sun</div>
                <div class="cal-weekday">Mon</div>
                <div class="cal-weekday">Tue</div>
                <div class="cal-weekday">Wed</div>
                <div class="cal-weekday">Thu</div>
                <div class="cal-weekday">Fri</div>
                <div class="cal-weekday">Sat</div>
            </div>
            <div class="cal-days" id="calDays"></div>
        </div>

        {{-- Legend --}}
        <div style="display:flex; gap:16px; padding:12px 20px; border-top:1px solid var(--gray-100); flex-wrap:wrap;">
            <span style="display:flex; align-items:center; gap:5px; font-size:12px; color:var(--gray-500);">
                <span class="cal-dot academic"></span> Academic
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:12px; color:var(--gray-500);">
                <span class="cal-dot admin"></span> Admin
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:12px; color:var(--gray-500);">
                <span class="cal-dot holiday"></span> Holiday
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:12px; color:var(--gray-500);">
                <span class="cal-dot activity"></span> Activity
            </span>
        </div>
    </div>

    {{-- Events Panel --}}
    <div class="card events-panel">
        <div class="events-panel-header">
            <span class="events-panel-title">Events</span>
            <span class="events-panel-date" id="selectedDateLabel">All upcoming</span>
        </div>
        <div class="events-list" id="eventsList">
            <div class="no-events">Loading...</div>
        </div>
    </div>

</div>

{{-- VIEW MODAL --}}
<div id="viewModal" style="display:none; position:fixed; inset:0; background-color:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:10px; width:420px; padding:24px; box-shadow:0 4px 16px rgba(0,0,0,0.3);">
        <h3 style="margin-bottom:16px; font-weight:600;">Event Details</h3>
        <div style="margin-bottom:16px; display:flex; flex-direction:column; gap:10px;">
            <p><strong>Title:</strong> <span id="modalTitle"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Date:</strong> <span id="modalDate"></span></p>
            <p><strong>Type:</strong> <span id="modalType"></span></p>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary" onclick="openEditModal()">Edit</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

{{-- POST / EDIT MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background-color:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:var(--radius-lg); width:420px; padding:24px; box-shadow:var(--shadow-md);">
        <h3 class="section-title" id="postModalTitle" style="margin-bottom:16px;">Add New Event</h3>
        <form id="eventForm">
            <input type="hidden" id="editEventId" value="">

            <label class="filter-label mb-1">Title</label>
            <input type="text" id="eventTitle" class="form-input mb-3" placeholder="e.g. Final Exams Begin">

            <label class="filter-label mb-1">Description</label>
            <textarea id="eventDescription" class="form-input mb-3" rows="3" placeholder="Brief description of the event..."></textarea>

            <label class="filter-label mb-1">Event Date</label>
            <input type="date" id="eventDate" class="form-input mb-3">

            <label class="filter-label mb-1">Event Type</label>
            <select id="eventType" class="form-select mb-3">
                <option value="">— Select Type —</option>
                <option value="academic">Academic</option>
                <option value="admin">Admin</option>
                <option value="holiday">Holiday</option>
                <option value="activity">Activity</option>
            </select>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
                <button type="button" class="btn btn-outline" onclick="closePostModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="postSubmitBtn">Save Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const viewModal = document.getElementById('viewModal');
    const postModal = document.getElementById('postModal');
    let currentEvent  = {};
    let allEvents     = [];
    let currentYear   = new Date().getFullYear();
    let currentMonth  = new Date().getMonth();
    let selectedDate  = null;

    const monthNames = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];

    // ── Calendar Render ───────────────────────────────────────────────────────
    function renderCalendar() {
        const label   = document.getElementById('monthLabel');
        const grid    = document.getElementById('calDays');
        label.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();
        const today = new Date();

        // Previous month padding
        for (let i = firstDay - 1; i >= 0; i--) {
            grid.appendChild(makeDay(daysInPrev - i, currentMonth - 1, currentYear, true));
        }

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            grid.appendChild(makeDay(d, currentMonth, currentYear, false));
        }

        // Next month padding
        const totalCells = firstDay + daysInMonth;
        const remaining  = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let d = 1; d <= remaining; d++) {
            grid.appendChild(makeDay(d, currentMonth + 1, currentYear, true));
        }
    }

    function makeDay(day, month, year, isOther) {
        const realMonth = (month + 12) % 12;
        const realYear  = month < 0 ? year - 1 : (month > 11 ? year + 1 : year);
        const dateStr   = `${realYear}-${String(realMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        const today     = new Date();
        const isToday   = day === today.getDate() && realMonth === today.getMonth() && realYear === today.getFullYear();
        const isSelected = dateStr === selectedDate;

        const cell = document.createElement('div');
        cell.className = `cal-day${isOther ? ' other-month' : ''}${isToday ? ' today' : ''}${isSelected ? ' selected' : ''}`;
        cell.dataset.date = dateStr;

        const numDiv = document.createElement('div');
        numDiv.className = 'cal-day-num';
        numDiv.textContent = day;
        cell.appendChild(numDiv);

        // Dots for events on this day
        const dayEvents = allEvents.filter(e => e.event_date === dateStr);
        if (dayEvents.length > 0) {
            const dotsDiv = document.createElement('div');
            dotsDiv.className = 'cal-day-dots';
            dayEvents.slice(0, 3).forEach(ev => {
                const dot = document.createElement('span');
                dot.className = `cal-dot ${ev.event_type}`;
                dotsDiv.appendChild(dot);
            });
            cell.appendChild(dotsDiv);
        }

        cell.addEventListener('click', function () {
            selectedDate = dateStr;
            renderCalendar();
            renderEventsList(dateStr);
            document.getElementById('selectedDateLabel').textContent =
                new Date(realYear, realMonth, day).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' });
        });

        return cell;
    }

    // ── Events List Panel ─────────────────────────────────────────────────────
    function renderEventsList(dateStr) {
        const list   = document.getElementById('eventsList');
        const events = dateStr
            ? allEvents.filter(e => e.event_date === dateStr)
            : allEvents.filter(e => e.event_date >= new Date().toISOString().slice(0,10));

        list.innerHTML = '';

        if (events.length === 0) {
            list.innerHTML = `<div class="no-events">${dateStr ? 'No events on this day.' : 'No upcoming events.'}</div>`;
            return;
        }

        events.forEach(ev => {
            const card = document.createElement('div');
            card.className = 'event-card';
            card.innerHTML = `
                <div class="event-card-bar ${ev.event_type}"></div>
                <div class="event-card-body">
                    <div class="event-card-title">${ev.title}</div>
                    <div class="event-card-desc">${ev.description || '—'}</div>
                    <span class="event-type-badge ${ev.event_type}">${ev.event_type.charAt(0).toUpperCase() + ev.event_type.slice(1)}</span>
                </div>
            `;
            card.addEventListener('click', function () {
                openViewModal(ev.id, ev.title, ev.description || '', ev.event_date, ev.event_type);
            });
            list.appendChild(card);
        });
    }

    // ── Navigation ────────────────────────────────────────────────────────────
    document.getElementById('prevMonth').addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        selectedDate = null;
        document.getElementById('selectedDateLabel').textContent = 'All upcoming';
        renderCalendar();
        renderEventsList(null);
    });

    document.getElementById('nextMonth').addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        selectedDate = null;
        document.getElementById('selectedDateLabel').textContent = 'All upcoming';
        renderCalendar();
        renderEventsList(null);
    });

    document.getElementById('todayBtn').addEventListener('click', function () {
        currentYear  = new Date().getFullYear();
        currentMonth = new Date().getMonth();
        selectedDate = null;
        document.getElementById('selectedDateLabel').textContent = 'All upcoming';
        renderCalendar();
        renderEventsList(null);
    });

    // ── View Modal ────────────────────────────────────────────────────────────
    function typeBadge(type) {
        const colors = {
            academic: { bg: '#dbeafe', color: '#1d4ed8' },
            admin:    { bg: '#fef3c7', color: '#d97706' },
            holiday:  { bg: '#dcfce7', color: '#16a34a' },
            activity: { bg: '#fee2e2', color: '#dc2626' },
        };
        const c = colors[type] || { bg: '#f1f5f9', color: '#64748b' };
        return `<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:${c.bg};color:${c.color};">${type.charAt(0).toUpperCase()+type.slice(1)}</span>`;
    }

    function openViewModal(id, title, description, event_date, event_type) {
        currentEvent = { id, title, description, event_date, event_type };
        document.getElementById('modalTitle').textContent       = title;
        document.getElementById('modalDescription').textContent = description || '—';
        document.getElementById('modalDate').textContent        = event_date;
        document.getElementById('modalType').innerHTML          = typeBadge(event_type);
        viewModal.style.display = 'flex';
    }

    function closeViewModal() { viewModal.style.display = 'none'; }

    // ── Post/Edit Modal ───────────────────────────────────────────────────────
    function closePostModal() {
        postModal.style.display = 'none';
        document.getElementById('eventForm').reset();
        $('#postModalTitle').text('Add New Event');
        $('#postSubmitBtn').text('Save Event');
        $('#editEventId').val('');
    }

    function openEditModal() {
        closeViewModal();
        $('#postModalTitle').text('Edit Event');
        $('#postSubmitBtn').text('Update Event');
        $('#editEventId').val(currentEvent.id);
        $('#eventTitle').val(currentEvent.title);
        $('#eventDescription').val(currentEvent.description);
        $('#eventDate').val(currentEvent.event_date);
        $('#eventType').val(currentEvent.event_type);
        postModal.style.display = 'flex';
    }

    document.getElementById('openPostModal').addEventListener('click', function () {
        closePostModal();
        postModal.style.display = 'flex';
    });

    // ── Delete ────────────────────────────────────────────────────────────────
    function confirmDelete() {
        showConfirmationModal(
            'Delete Event',
            'Are you sure you want to delete this event?',
            function () {
                closeViewModal();
                loadingModal.show();
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        $.ajax({
                            url:    '{{ route("admin.calendar.destroy") }}',
                            method: 'POST',
                            data:   { id: currentEvent.id },
                            success: function (response) {
                                loadingModal.hide();
                                setTimeout(function() {
                                    if (response.status === 'success') {
                                        showPopup('Success', response.message, 'success');
                                        loadEvents();
                                    } else {
                                        showPopup('Error', response.message, 'error');
                                    }
                                }, 100);
                            },
                            error: function () {
                                loadingModal.hide();
                                setTimeout(function() {
                                    showPopup('Error', 'An error occurred while deleting.', 'error');
                                }, 100);
                            }
                        });
                    });
                });
            }
        );
    }

    // ── Form Submit ───────────────────────────────────────────────────────────
    $('#eventForm').on('submit', function (e) {
        e.preventDefault();
        const editId = $('#editEventId').val();

        if (!$('#eventTitle').val()) { showPopup('Validation', 'Please enter a title.', 'warning'); return; }
        if (!$('#eventDate').val())  { showPopup('Validation', 'Please select a date.', 'warning'); return; }
        if (!$('#eventType').val())  { showPopup('Validation', 'Please select an event type.', 'warning'); return; }

        const data = {
            title:       $('#eventTitle').val(),
            description: $('#eventDescription').val(),
            event_date:  $('#eventDate').val(),
            event_type:  $('#eventType').val(),
        };
        if (editId) data.id = editId;

        closePostModal();
        loadingModal.show();

        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                $.ajax({
                    url:    editId ? '{{ route("admin.calendar.update") }}' : '{{ route("admin.calendar.store") }}',
                    method: 'POST',
                    data:   data,
                    success: function (response) {
                        loadingModal.hide();
                        setTimeout(function() {
                            if (response.status === 'success') {
                                showPopup('Success', response.message, 'success');
                                loadEvents();
                            } else {
                                showPopup('Error', response.message, 'error');
                            }
                        }, 100);
                    },
                    error: function () {
                        loadingModal.hide();
                        setTimeout(function() {
                            showPopup('Error', 'An error occurred. Please try again.', 'error');
                        }, 100);
                    }
                });
            });
        });
    });

    // ── Load Events ───────────────────────────────────────────────────────────
    function loadEvents() {
        $.ajax({
            url:    '{{ route("admin.calendar.list") }}',
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    allEvents = response.data;
                } else {
                    allEvents = [];
                }
                renderCalendar();
                renderEventsList(selectedDate);
            },
            error: function () {
                allEvents = [];
                renderCalendar();
                document.getElementById('eventsList').innerHTML = '<div class="no-events">Failed to load events.</div>';
            }
        });
    }

    $(document).ready(function () {
        loadEvents();
    });
</script>

@endsection