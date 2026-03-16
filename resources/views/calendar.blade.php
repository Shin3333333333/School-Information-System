{{-- resources/views/calendar.blade.php --}}
{{--
    Shared calendar view — Admin, Teacher, Student.
    $role: 0 = Admin  → full CRUD for standalone events, sees everything
           1 = Teacher → read-only, sees standalone + their own announcement-linked events
           2 = Student → read-only, sees standalone + announcement-linked events targeting their section

    Controller: CalendarController@index
    Data feed:  GET /calendar/list  (usp_get_data MODE 16, role-aware)
--}}
@extends('layouts.app')

@section('title', 'Calendar')

@section('page-title')
<h2>{{ $role == 0 ? 'School Calendar' : 'Calendar' }}</h2>
@endsection

@push('styles')
<style>
    .calendar-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    /* ── Calendar card ─────────────────────────────────────────── */
    .calendar-header {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--dk-b2);
    }
    .calendar-nav { display: flex; align-items: center; gap: 12px; }
    .cal-nav-btn {
        width: 32px; height: 32px;
        border: 1.5px solid var(--dk-b1);
        background: transparent;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--dk-t3);
        transition: all .15s; font-size: 16px; line-height: 1;
    }
    .cal-nav-btn:hover { background: rgba(255,255,255,0.06); color: var(--dk-t1); }
    .cal-month-label {
        font-family: var(--font-display); font-size: 15px;
        font-weight: 700; color: var(--dk-t1);
        min-width: 180px; text-align: center;
    }
    .calendar-grid { padding: 14px 18px 16px; }
    .cal-weekdays {
        display: grid; grid-template-columns: repeat(7, 1fr);
        margin-bottom: 6px;
    }
    .cal-weekday {
        text-align: center; font-size: 10.5px; font-weight: 700;
        color: var(--dk-t4); letter-spacing: .5px;
        text-transform: uppercase; padding: 6px 0;
    }
    .cal-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
    .cal-day {
        aspect-ratio: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: flex-start;
        padding: 5px 3px 3px; border-radius: 8px;
        cursor: pointer; position: relative;
        transition: background .12s; min-height: 48px;
    }
    .cal-day:hover { background: rgba(255,255,255,0.06); }
    .cal-day.other-month .cal-day-num { color: var(--dk-t4); opacity: .35; }
    .cal-day.today .cal-day-num {
        background: var(--blue-600); color: #fff;
        border-radius: 50%; width: 26px; height: 26px;
        display: flex; align-items: center; justify-content: center;
    }
    .cal-day.selected { background: rgba(37,99,235,0.13); }
    .cal-day-num {
        font-size: 12.5px; font-weight: 600; color: var(--dk-t2);
        line-height: 1; width: 26px; height: 26px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .cal-day-dots {
        display: flex; gap: 2px; flex-wrap: wrap;
        justify-content: center; margin-top: 3px;
    }
    .cal-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
    .cal-dot.academic    { background: #3b82f6; }
    .cal-dot.admin       { background: #f59e0b; }
    .cal-dot.holiday     { background: #22c55e; }
    .cal-dot.activity    { background: #ef4444; }
    .cal-dot.announcement{ background: #a78bfa; }

    /* Legend */
    .cal-legend {
        display: flex; gap: 14px; padding: 10px 20px 14px;
        border-top: 1px solid var(--dk-b2); flex-wrap: wrap;
    }
    .cal-legend span {
        display: flex; align-items: center; gap: 5px;
        font-size: 11.5px; color: var(--dk-t4);
    }

    /* ── Events panel ──────────────────────────────────────────── */
    .events-panel { display: flex; flex-direction: column; }
    .events-panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--dk-b2);
    }
    .events-panel-title {
        font-family: var(--font-display); font-size: 14px;
        font-weight: 700; color: var(--dk-t1);
    }
    .events-panel-date { font-size: 11.5px; color: var(--dk-t4); }

    .events-list {
        padding: 10px 12px; display: flex; flex-direction: column;
        gap: 7px; max-height: 460px; overflow-y: auto;
    }
    .event-card {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 10px 12px; border-radius: 8px;
        border: 1px solid var(--dk-b1); cursor: pointer;
        transition: all .15s; background: var(--dk-surface2);
    }
    .event-card:hover { border-color: rgba(37,99,235,0.3); background: rgba(37,99,235,0.06); }
    .event-card.read-only { cursor: default; }
    .event-card.read-only:hover { border-color: var(--dk-b1); background: var(--dk-surface2); }

    .event-card-bar {
        width: 3px; min-height: 100%; border-radius: 2px;
        align-self: stretch; flex-shrink: 0;
    }
    .event-card-bar.academic    { background: #3b82f6; }
    .event-card-bar.admin       { background: #f59e0b; }
    .event-card-bar.holiday     { background: #22c55e; }
    .event-card-bar.activity    { background: #ef4444; }
    .event-card-bar.announcement{ background: #a78bfa; }

    .event-card-body { flex: 1; min-width: 0; }
    .event-card-title {
        font-size: 13px; font-weight: 600; color: var(--dk-t1);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .event-card-desc {
        font-size: 11.5px; color: var(--dk-t4); margin-top: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .event-card-meta {
        display: flex; align-items: center; gap: 6px; margin-top: 5px;
        flex-wrap: wrap;
    }
    .event-type-badge {
        display: inline-block; padding: 1px 8px; border-radius: 20px;
        font-size: 10.5px; font-weight: 700;
    }
    .event-type-badge.academic    { background: rgba(59,130,246,0.15); color: #60a5fa; }
    .event-type-badge.admin       { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .event-type-badge.holiday     { background: rgba(34,197,94,0.15);  color: #4ade80; }
    .event-type-badge.activity    { background: rgba(239,68,68,0.15);  color: #f87171; }
    .event-type-badge.announcement{ background: rgba(167,139,250,0.15); color: #c4b5fd; }

    .event-source-badge {
        font-size: 10px; color: var(--dk-t4); font-style: italic;
    }

    .no-events {
        text-align: center; color: var(--dk-t4);
        font-size: 13px; padding: 36px 0;
    }

    /* ── Modals ─────────────────────────────────────────────────── */
    .cal-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.65);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; backdrop-filter: blur(3px);
    }
    .cal-modal-overlay.open { display: flex; }
    .cal-modal-box {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl); width: 100%; max-width: 460px;
        padding: 26px 28px; box-shadow: var(--shadow-xl);
        animation: calModalIn .22s ease-out; margin: 16px;
        max-height: 90vh; overflow-y: auto;
    }
    @keyframes calModalIn {
        from { opacity:0; transform:translateY(18px) scale(.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .cal-modal-box h3 {
        font-family: var(--font-display); font-size: 1rem;
        font-weight: 700; color: var(--dk-t1); margin: 0 0 20px;
    }
    .cal-form-group { margin-bottom: 14px; }
    .cal-form-group label {
        display: block; font-size: 0.73rem; font-weight: 700;
        color: var(--dk-t4); text-transform: uppercase;
        letter-spacing: .04em; margin-bottom: 5px;
    }
    .cal-form-control {
        width: 100%; padding: 8px 12px;
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem;
        color: var(--dk-t2); background: var(--dk-surface2);
        outline: none; box-sizing: border-box;
        transition: border-color .2s; appearance: none;
    }
    .cal-form-control:focus { border-color: rgba(96,165,250,0.4); box-shadow: 0 0 0 3px rgba(96,165,250,0.08); }
    .cal-form-control::placeholder { color: var(--dk-t4); }
    .cal-form-control option { background: #111827; color: #cbd5e1; }
    textarea.cal-form-control { resize: vertical; min-height: 80px; }
    .cal-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        margin-top: 22px; padding-top: 18px;
        border-top: 1px solid var(--dk-b2);
    }
    .cal-btn-cancel {
        padding: 8px 18px; background: rgba(255,255,255,0.05);
        color: var(--dk-t3); border: 1.5px solid var(--dk-b1);
        border-radius: var(--radius-md); font-family: var(--font-body);
        font-size: 0.83rem; font-weight: 600; cursor: pointer;
        transition: all .18s;
    }
    .cal-btn-cancel:hover { background: rgba(255,255,255,0.09); color: var(--dk-t1); }
    .cal-btn-save {
        padding: 8px 22px; background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.83rem; font-weight: 700;
        cursor: pointer; transition: background .18s;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        display: inline-flex; align-items: center; gap: 6px;
    }
    .cal-btn-save:hover { background: var(--blue-700); }
    .cal-btn-save:disabled { opacity: .5; cursor: not-allowed; }
    .cal-btn-danger {
        padding: 8px 18px; background: var(--red-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.83rem; font-weight: 700;
        cursor: pointer; transition: background .18s;
        box-shadow: 0 2px 8px rgba(220,38,38,0.3);
    }
    .cal-btn-danger:hover { background: #b91c1c; }

    /* View modal detail rows */
    .cal-detail-row { display: flex; flex-direction: column; gap: 3px; margin-bottom: 12px; }
    .cal-detail-label { font-size: 0.71rem; font-weight: 700; color: var(--dk-t4); text-transform: uppercase; letter-spacing: .04em; }
    .cal-detail-value { font-size: 0.87rem; color: var(--dk-t2); }

    @media (max-width: 860px) {
        .calendar-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ── Add Event button — admin only ───────────────────────────────────────── --}}
@if($role == 0)
<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <button class="cal-btn-save" id="btnOpenAddEvent">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
        </svg>
        Add Event
    </button>
</div>
@else
<div style="margin-bottom:16px;"></div>
@endif

<div class="calendar-layout">

    {{-- ── Calendar ─────────────────────────────────────────────────────────── --}}
    <div class="table-card" style="border-radius:var(--radius-lg); overflow:hidden;">
        <div class="calendar-header">
            <div class="calendar-nav">
                <button class="cal-nav-btn" id="prevMonth">&#8249;</button>
                <span class="cal-month-label" id="monthLabel"></span>
                <button class="cal-nav-btn" id="nextMonth">&#8250;</button>
            </div>
            <button class="cal-btn-cancel" id="todayBtn" style="height:32px; padding:0 14px; font-size:12px;">
                Today
            </button>
        </div>
        <div class="calendar-grid">
            <div class="cal-weekdays">
                <div class="cal-weekday">Sun</div><div class="cal-weekday">Mon</div>
                <div class="cal-weekday">Tue</div><div class="cal-weekday">Wed</div>
                <div class="cal-weekday">Thu</div><div class="cal-weekday">Fri</div>
                <div class="cal-weekday">Sat</div>
            </div>
            <div class="cal-days" id="calDays"></div>
        </div>
        <div class="cal-legend">
            <span><span class="cal-dot academic"></span> Academic</span>
            <span><span class="cal-dot admin"></span> Admin</span>
            <span><span class="cal-dot holiday"></span> Holiday</span>
            <span><span class="cal-dot activity"></span> Activity</span>
            <span><span class="cal-dot announcement"></span> Announcement</span>
        </div>
    </div>

    {{-- ── Events panel ─────────────────────────────────────────────────────── --}}
    <div class="table-card events-panel" style="border-radius:var(--radius-lg); overflow:hidden;">
        <div class="events-panel-header">
            <span class="events-panel-title">Events</span>
            <span class="events-panel-date" id="selectedDateLabel">All upcoming</span>
        </div>
        <div class="events-list" id="eventsList">
            <div class="no-events">
                <span style="display:inline-flex; align-items:center; gap:6px;">
                    Loading
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                </span>
            </div>
        </div>
    </div>

</div>

{{-- ── Add / Edit modal (admin only) ───────────────────────────────────────── --}}
@if($role == 0)
<div class="cal-modal-overlay" id="eventFormModal">
    <div class="cal-modal-box">
        <h3 id="eventFormTitle">Add Event</h3>
        <input type="hidden" id="editEventId">

        <div class="cal-form-group">
            <label>Title <span style="color:var(--red-400);">*</span></label>
            <input type="text" class="cal-form-control" id="eventTitle" placeholder="e.g. Final Exams Begin" maxlength="200">
        </div>
        <div class="cal-form-group">
            <label>Description</label>
            <textarea class="cal-form-control" id="eventDescription" placeholder="Brief description…"></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="cal-form-group">
                <label>Date <span style="color:var(--red-400);">*</span></label>
                <input type="date" class="cal-form-control" id="eventDate">
            </div>
            <div class="cal-form-group">
                <label>Type <span style="color:var(--red-400);">*</span></label>
                <select class="cal-form-control" id="eventType"
                    style="background-image:url(\"data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E\"); background-repeat:no-repeat; background-position:right 10px center; padding-right:28px;">
                    <option value="">Select type…</option>
                    <option value="academic">Academic</option>
                    <option value="admin">Admin</option>
                    <option value="holiday">Holiday</option>
                    <option value="activity">Activity</option>
                </select>
            </div>
        </div>

        <div class="cal-modal-footer">
            <button class="cal-btn-cancel" id="btnCancelEventForm">Cancel</button>
            <button class="cal-btn-save" id="btnSaveEvent">
                <svg id="btnSaveEventIcon" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <path d="M17 21v-8H7v8M7 3v5h8"/>
                </svg>
                <span class="loading loading-spinner" id="btnSaveEventLoader" style="width:13px;height:13px;display:none;color:#fff;"></span>
                <span id="btnSaveEventLabel">Save Event</span>
            </button>
        </div>
    </div>
</div>

{{-- ── View / Edit / Delete modal (admin only — for standalone events) ─────── --}}
<div class="cal-modal-overlay" id="eventViewModal">
    <div class="cal-modal-box" style="max-width:420px;">
        <h3>Event Details</h3>
        <div class="cal-detail-row">
            <span class="cal-detail-label">Title</span>
            <span class="cal-detail-value" id="viewEventTitle"></span>
        </div>
        <div class="cal-detail-row">
            <span class="cal-detail-label">Description</span>
            <span class="cal-detail-value" id="viewEventDesc" style="color:var(--dk-t3);"></span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="cal-detail-row">
                <span class="cal-detail-label">Date</span>
                <span class="cal-detail-value" id="viewEventDate"></span>
            </div>
            <div class="cal-detail-row">
                <span class="cal-detail-label">Type</span>
                <span id="viewEventType"></span>
            </div>
        </div>
        <div class="cal-detail-row" id="viewEventSourceRow">
            <span class="cal-detail-label">Source</span>
            <span class="cal-detail-value" id="viewEventSource" style="color:var(--dk-t4); font-style:italic;"></span>
        </div>
        <div class="cal-modal-footer" id="viewEventAdminActions">
            <button class="cal-btn-cancel" id="btnCloseViewModal">Close</button>
            <button class="cal-btn-cancel" id="btnEditEvent" style="color:#60a5fa; border-color:rgba(37,99,235,0.3);">Edit</button>
            <button class="cal-btn-danger" id="btnDeleteEvent">Delete</button>
        </div>
        <div class="cal-modal-footer" id="viewEventReadOnlyActions" style="display:none;">
            <button class="cal-btn-cancel" id="btnCloseViewModalRO">Close</button>
        </div>
    </div>
</div>
@endif

{{-- ── View modal (teacher + student — read-only) ───────────────────────────── --}}
@if($role != 0)
<div class="cal-modal-overlay" id="eventViewModal">
    <div class="cal-modal-box" style="max-width:400px;">
        <h3>Event Details</h3>
        <div class="cal-detail-row">
            <span class="cal-detail-label">Title</span>
            <span class="cal-detail-value" id="viewEventTitle"></span>
        </div>
        <div class="cal-detail-row">
            <span class="cal-detail-label">Description</span>
            <span class="cal-detail-value" id="viewEventDesc" style="color:var(--dk-t3);"></span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="cal-detail-row">
                <span class="cal-detail-label">Date</span>
                <span class="cal-detail-value" id="viewEventDate"></span>
            </div>
            <div class="cal-detail-row">
                <span class="cal-detail-label">Type</span>
                <span id="viewEventType"></span>
            </div>
        </div>
        <div class="cal-detail-row">
            <span class="cal-detail-label">Posted By</span>
            <span class="cal-detail-value" id="viewEventSource" style="color:#60a5fa; font-weight:600;"></span>
        </div>
        <div class="cal-modal-footer">
            <button class="cal-btn-cancel" id="btnCloseViewModal">Close</button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const ROLE = {{ (int)($role ?? 2) }};  // 0=admin 1=teacher 2=student

    const ROUTES = {
        list   : '{{ route("calendar.list") }}',
        store  : '{{ route("calendar.store") }}',
        update : '{{ route("calendar.update") }}',
        destroy: '{{ route("calendar.destroy") }}',
    };

    const MONTH_NAMES = ['January','February','March','April','May','June',
                         'July','August','September','October','November','December'];

    let allEvents    = [];
    let currentYear  = new Date().getFullYear();
    let currentMonth = new Date().getMonth();
    let selectedDate = null;
    let currentViewEvent = null;  // event currently open in view modal

    // ── Load events ───────────────────────────────────────────────
    function loadEvents() {
        $.ajax({
            url: ROUTES.list, type: 'GET', dataType: 'json',
            success: function (res) {
                allEvents = res.status === 'success' ? (res.data ?? []) : [];
                renderCalendar();
                renderEventsList(selectedDate);
            },
            error: function () {
                allEvents = [];
                renderCalendar();
                $('#eventsList').html('<div class="no-events" style="color:var(--red-400);">Failed to load events.</div>');
            }
        });
    }

    // ── Calendar render ───────────────────────────────────────────
    function renderCalendar() {
        $('#monthLabel').text(`${MONTH_NAMES[currentMonth]} ${currentYear}`);
        const grid = document.getElementById('calDays');
        grid.innerHTML = '';

        const firstDay    = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();
        const today       = new Date();

        for (let i = firstDay - 1; i >= 0; i--)
            grid.appendChild(makeDay(daysInPrev - i, currentMonth - 1, currentYear, true));

        for (let d = 1; d <= daysInMonth; d++)
            grid.appendChild(makeDay(d, currentMonth, currentYear, false));

        const total     = firstDay + daysInMonth;
        const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
        for (let d = 1; d <= remaining; d++)
            grid.appendChild(makeDay(d, currentMonth + 1, currentYear, true));
    }

    function makeDay(day, month, year, isOther) {
        const realMonth = ((month % 12) + 12) % 12;
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

        const dayEvents = allEvents.filter(e => e.event_date === dateStr);
        if (dayEvents.length) {
            const dotsDiv = document.createElement('div');
            dotsDiv.className = 'cal-day-dots';
            dayEvents.slice(0, 3).forEach(ev => {
                const dot = document.createElement('span');
                dot.className = `cal-dot ${ev.source === 'announcement' ? 'announcement' : (ev.event_type ?? 'academic')}`;
                dotsDiv.appendChild(dot);
            });
            cell.appendChild(dotsDiv);
        }

        cell.addEventListener('click', function () {
            selectedDate = dateStr;
            renderCalendar();
            renderEventsList(dateStr);
            $('#selectedDateLabel').text(
                new Date(realYear, realMonth, day)
                    .toLocaleDateString('en-PH', { month:'long', day:'numeric', year:'numeric' })
            );
        });

        return cell;
    }

    // ── Events list panel ─────────────────────────────────────────
    function renderEventsList(dateStr) {
        const todayStr = new Date().toISOString().slice(0, 10);
        const events   = dateStr
            ? allEvents.filter(e => e.event_date === dateStr)
            : allEvents.filter(e => e.event_date >= todayStr);

        if (!events.length) {
            $('#eventsList').html(`<div class="no-events">${dateStr ? 'No events on this day.' : 'No upcoming events.'}</div>`);
            return;
        }

        const html = events.map(ev => {
            const type      = ev.source === 'announcement' ? 'announcement' : (ev.event_type ?? 'academic');
            const typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
            const sourceNote = ev.source === 'announcement'
                ? `<span class="event-source-badge">📢 Announcement${ev.posted_by_name ? ' · ' + ev.posted_by_name : ''}</span>`
                : '';

            return `
            <div class="event-card${ROLE !== 0 ? ' read-only' : ''}"
                 data-event='${JSON.stringify(ev).replace(/'/g,"&#39;")}'>
                <div class="event-card-bar ${type}"></div>
                <div class="event-card-body">
                    <div class="event-card-title">${esc(ev.title)}</div>
                    <div class="event-card-desc">${esc(ev.description ?? '—')}</div>
                    <div class="event-card-meta">
                        <span class="event-type-badge ${type}">${typeLabel}</span>
                        ${sourceNote}
                    </div>
                </div>
            </div>`;
        }).join('');

        $('#eventsList').html(html);

        // Wire card clicks → view modal
        $('#eventsList .event-card').on('click', function () {
            const ev = JSON.parse($(this).attr('data-event').replace(/&#39;/g, "'"));
            openViewModal(ev);
        });
    }

    // ── View modal ────────────────────────────────────────────────
    function typeBadgeHtml(type) {
        const labels = { academic:'Academic', admin:'Admin', holiday:'Holiday', activity:'Activity', announcement:'Announcement' };
        return `<span class="event-type-badge ${type}">${labels[type] ?? type}</span>`;
    }

    function openViewModal(ev) {
        currentViewEvent = ev;
        const type = ev.source === 'announcement' ? 'announcement' : (ev.event_type ?? 'academic');

        $('#viewEventTitle').text(ev.title ?? '');
        $('#viewEventDesc').text(ev.description || '—');
        $('#viewEventDate').text(ev.event_date ?? '');
        $('#viewEventType').html(typeBadgeHtml(type));

        if (ROLE === 0) {
            // Admin sees source info + edit/delete only for standalone events
            const isStandalone = ev.source === 'event';
            $('#viewEventSource').text(isStandalone ? 'Standalone event' : `Announcement (by ${ev.posted_by_name ?? 'unknown'})`);
            $('#viewEventAdminActions').toggle(isStandalone);
            $('#viewEventReadOnlyActions').toggle(!isStandalone);
        } else {
            // Teacher/Student: show who posted it
            $('#viewEventSource').text(
                ev.source === 'announcement' && ev.posted_by_name
                    ? ev.posted_by_name
                    : 'School Administration'
            );
        }

        $('#eventViewModal').addClass('open');
    }

    function closeViewModal() { $('#eventViewModal').removeClass('open'); currentViewEvent = null; }

    $('#btnCloseViewModal, #btnCloseViewModalRO').on('click', closeViewModal);
    $('#eventViewModal').on('click', function (e) {
        if ($(e.target).is($('#eventViewModal'))) closeViewModal();
    });

    // ── Admin: add / edit / delete standalone events ──────────────
    if (ROLE === 0) {

        // Open add form
        $('#btnOpenAddEvent').on('click', function () {
            $('#eventFormTitle').text('Add Event');
            $('#btnSaveEventLabel').text('Save Event');
            $('#editEventId, #eventTitle, #eventDescription, #eventDate').val('');
            $('#eventType').val('');
            $('#eventFormModal').addClass('open');
        });

        // Open edit form from view modal
        $('#btnEditEvent').on('click', function () {
            if (!currentViewEvent) return;
            closeViewModal();
            $('#eventFormTitle').text('Edit Event');
            $('#btnSaveEventLabel').text('Update Event');
            $('#editEventId').val(currentViewEvent.id);
            $('#eventTitle').val(currentViewEvent.title ?? '');
            $('#eventDescription').val(currentViewEvent.description ?? '');
            $('#eventDate').val(currentViewEvent.event_date ?? '');
            $('#eventType').val(currentViewEvent.event_type ?? '');
            $('#eventFormModal').addClass('open');
        });

        // Close form modal
        $('#btnCancelEventForm').on('click', function () { $('#eventFormModal').removeClass('open'); });
        $('#eventFormModal').on('click', function (e) {
            if ($(e.target).is($('#eventFormModal'))) $('#eventFormModal').removeClass('open');
        });

        // Save (store or update)
        $('#btnSaveEvent').on('click', function () {
            const title = $('#eventTitle').val().trim();
            const date  = $('#eventDate').val();
            const type  = $('#eventType').val();

            if (!title) { showPopup('Validation', 'Title is required.', 'warning'); return; }
            if (!date)  { showPopup('Validation', 'Date is required.', 'warning'); return; }
            if (!type)  { showPopup('Validation', 'Please select an event type.', 'warning'); return; }

            const editId  = $('#editEventId').val();
            const editing = !!editId;
            const payload = {
                title      : title,
                description: $('#eventDescription').val().trim() || null,
                event_date : date,
                event_type : type,
            };
            if (editing) payload.id = parseInt(editId);

            const $btn = $(this).prop('disabled', true);
            $('#btnSaveEventIcon').hide();
            $('#btnSaveEventLoader').css('display', 'inline-block');
            $('#btnSaveEventLabel').text(editing ? 'Updating…' : 'Saving…');

            $.ajax({
                url        : editing ? ROUTES.update : ROUTES.store,
                type       : 'POST',
                contentType: 'application/json',
                data       : JSON.stringify(payload),
                dataType   : 'json',
                success    : function (res) {
                    $('#btnSaveEventIcon').show();
                    $('#btnSaveEventLoader').hide();
                    $('#btnSaveEventLabel').text(editing ? 'Update Event' : 'Save Event');
                    $btn.prop('disabled', false);
                    if (res.status === 'success') {
                        $('#eventFormModal').removeClass('open');
                        loadEvents();
                        showPopup('Success', res.message ?? 'Event saved.', 'success');
                    } else {
                        showPopup('Error', res.message ?? 'Failed.', 'error');
                    }
                },
                error: function (xhr) {
                    $('#btnSaveEventIcon').show();
                    $('#btnSaveEventLoader').hide();
                    $('#btnSaveEventLabel').text(editing ? 'Update Event' : 'Save Event');
                    $btn.prop('disabled', false);
                    showPopup('Error', xhr.responseJSON?.message ?? 'Server error.', 'error');
                }
            });
        });

        // Delete standalone event
        $('#btnDeleteEvent').on('click', function () {
            if (!currentViewEvent) return;
            const ev = currentViewEvent;
            showConfirmationModal(
                'Delete Event',
                `Delete "${ev.title}"? This cannot be undone.`,
                function () {
                    closeViewModal();
                    if (window.loadingModal) window.loadingModal.show();
                    $.ajax({
                        url        : ROUTES.destroy,
                        type       : 'POST',
                        contentType: 'application/json',
                        data       : JSON.stringify({ id: ev.id }),
                        dataType   : 'json',
                        success    : function (res) {
                            if (window.loadingModal) window.loadingModal.hide();
                            if (res.status === 'success') {
                                loadEvents();
                                showPopup('Deleted', res.message ?? 'Event removed.', 'success');
                            } else {
                                showPopup('Error', res.message ?? 'Delete failed.', 'error');
                            }
                        },
                        error: function (xhr) {
                            if (window.loadingModal) window.loadingModal.hide();
                            showPopup('Error', xhr.responseJSON?.message ?? 'Server error.', 'error');
                        }
                    });
                }
            );
        });
    }

    // ── Navigation ────────────────────────────────────────────────
    function resetToUpcoming() {
        selectedDate = null;
        $('#selectedDateLabel').text('All upcoming');
    }

    $('#prevMonth').on('click', function () {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        resetToUpcoming(); renderCalendar(); renderEventsList(null);
    });
    $('#nextMonth').on('click', function () {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        resetToUpcoming(); renderCalendar(); renderEventsList(null);
    });
    $('#todayBtn').on('click', function () {
        currentYear  = new Date().getFullYear();
        currentMonth = new Date().getMonth();
        resetToUpcoming(); renderCalendar(); renderEventsList(null);
    });

    // ── Helpers ───────────────────────────────────────────────────
    function esc(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // ── Init ──────────────────────────────────────────────────────
    loadEvents();
});
</script>
@endpush