{{-- resources/views/schedule.blade.php --}}
{{--
    Unified schedule view for all user types.
    $role: 1 = Teacher | 2 = Student | 3 = Admin
    Passed from the controller.
--}}
@extends('layouts.app')

@section('title', $role == 1 ? 'My Schedule' : ($role == 2 ? 'Class Schedule' : 'Schedule Management'))

@section('page-title')
<h2>
    @if($role == 1)
        My Schedule
    @elseif($role == 2)
        My Class Schedule
    @else
        Schedule Management
    @endif
</h2>
@endsection

@push('styles')
<style>
    /* ── Page header ──────────────────────────────────────── */
    .sv-header {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 22px; flex-wrap: wrap; gap: 10px;
    }
    .sv-header h2 {
        font-family: var(--font-display); font-size: 1.2rem;
        font-weight: 700; color: var(--dk-t1); margin: 0 0 3px;
    }
    .sv-header p { margin: 0; font-size: 0.81rem; color: var(--dk-t3); }

    /* ── Filter bar ───────────────────────────────────────── */
    .sv-filter-bar {
        display: flex; gap: 10px; align-items: center;
        flex-wrap: wrap; margin-bottom: 18px;
    }
    .sv-filter-bar select, .sv-filter-bar .form-select {
        padding: 7px 28px 7px 11px;
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-size: 0.83rem; color: var(--dk-t2);
        background: var(--dk-surface2); font-family: var(--font-body);
        outline: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 9px center;
        transition: border-color .2s;
    }
    .sv-filter-bar select:focus, .sv-filter-bar .form-select:focus { border-color: rgba(96,165,250,0.4); }
    .sv-filter-bar select option { background: #111827; color: #cbd5e1; }

    .btn-outline {
        background: transparent;
        border: 1.5px solid var(--dk-b2);
        color: var(--dk-t2);
        padding: 7px 14px;
        border-radius: var(--radius-md);
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-outline:hover {
        background: var(--dk-surface2);
        border-color: var(--dk-t3);
    }
    
    .btn-primary {
        background: #2563eb;
        border: none;
        color: white;
        padding: 7px 14px;
        border-radius: var(--radius-md);
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .2s;
    }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-primary svg { stroke: currentColor; }

    /* ── Timetable card ───────────────────────────────────── */
    .sv-card {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg); overflow: hidden;
    }
    .sv-table-wrap { overflow-x: auto; }

    /* ── Grid table ───────────────────────────────────────── */
    .sv-table {
        width: 100%; border-collapse: collapse;
        font-size: 0.82rem; table-layout: fixed; min-width: 640px;
    }
    .sv-table thead th {
        padding: 11px 14px; text-align: center;
        font-size: 0.72rem; font-weight: 700;
        letter-spacing: .04em; text-transform: uppercase;
        border-bottom: 1.5px solid var(--dk-b2);
        white-space: nowrap;
    }
    .sv-table thead th.time-col {
        text-align: left; color: var(--dk-t4);
        background: var(--dk-surface2); width: 96px;
    }
    .sv-table tbody tr { border-bottom: 1px solid var(--dk-b2); }
    .sv-table tbody tr:last-child { border-bottom: none; }
    .sv-table td {
        padding: 5px 6px; vertical-align: top;
        border-left: 1px solid var(--dk-b2);
    }
    .sv-table td.time-lbl {
        padding: 8px 14px; vertical-align: top;
        background: var(--dk-surface2); border-left: none;
        white-space: nowrap;
    }
    .time-lbl-main { font-size: 0.78rem; font-weight: 600; color: var(--dk-t3); }
    .time-lbl-sub  { font-size: 0.68rem; color: var(--dk-t4); }

    /* ── Schedule block ───────────────────────────────────── */
    .sv-block {
        border-radius: 8px; padding: 8px 10px;
        margin-bottom: 3px; overflow: hidden;
        border: 1.5px solid transparent;
        transition: transform .15s, box-shadow .15s;
    }
    .sv-block:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.25); }
    .sv-block-subject {
        font-weight: 700; font-size: 0.8rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 3px;
    }
    .sv-block-meta { font-size: 0.71rem; line-height: 1.55; opacity: .85; }
    .sv-block-time {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 0.7rem; font-weight: 600; margin-top: 5px;
        background: rgba(0,0,0,0.15); border-radius: 4px; padding: 1px 6px;
    }

    /* Admin action buttons */
    .sv-block-actions {
        display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px;
    }
    .sv-block-actions .btn-sm {
        padding: 2px 8px; font-size: 0.7rem; border-radius: 4px;
        border: none; font-weight: 600; cursor: pointer;
    }
    .btn-edit {
        background: rgba(37,99,235,0.15); color: #60a5fa;
        border: 1px solid rgba(37,99,235,0.3);
    }
    .btn-delete {
        background: rgba(220,38,38,0.15); color: #f87171;
        border: 1px solid rgba(220,38,38,0.3);
    }

    /* Day colour themes (dark-safe) */
    .day-mon { background: rgba(37,99,235,0.13);  border-color: rgba(37,99,235,0.32);  color: #93c5fd; }
    .day-tue { background: rgba(22,163,74,0.13);  border-color: rgba(22,163,74,0.32);  color: #86efac; }
    .day-wed { background: rgba(217,119,6,0.13);  border-color: rgba(217,119,6,0.32);  color: #fcd34d; }
    .day-thu { background: rgba(220,38,38,0.13);  border-color: rgba(220,38,38,0.32);  color: #fca5a5; }
    .day-fri { background: rgba(139,92,246,0.13); border-color: rgba(139,92,246,0.32); color: #c4b5fd; }

    .day-head-mon { color: #60a5fa; background: rgba(37,99,235,0.08); }
    .day-head-tue { color: #4ade80; background: rgba(22,163,74,0.08); }
    .day-head-wed { color: #fbbf24; background: rgba(217,119,6,0.08); }
    .day-head-thu { color: #f87171; background: rgba(220,38,38,0.08); }
    .day-head-fri { color: #a78bfa; background: rgba(139,92,246,0.08); }

    /* ── Empty / loading ──────────────────────────────────── */
    .sv-placeholder {
        text-align: center; padding: 56px 20px; color: var(--dk-t4);
    }
    .sv-placeholder svg { margin-bottom: 12px; opacity: .3; }
    .sv-placeholder p { font-size: 0.88rem; margin: 0; }

    /* ── Summary chips ────────────────────────────────────── */
    .sv-summary {
        padding: 14px 18px; border-top: 1px solid var(--dk-b2);
        display: flex; flex-wrap: wrap; gap: 8px; align-items: center;
    }
    .sv-chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
        background: rgba(37,99,235,0.1); color: #60a5fa;
        border: 1px solid rgba(37,99,235,0.2);
    }
    .sv-chip.green  { background: rgba(22,163,74,0.1);  color: #4ade80; border-color: rgba(22,163,74,0.2); }
    .sv-chip.yellow { background: rgba(217,119,6,0.1);  color: #fbbf24; border-color: rgba(217,119,6,0.2); }

    /* ── Modal styles ─────────────────────────────────────── */
    .sv-modal-backdrop {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.45); z-index: 10000;
        align-items: center; justify-content: center;
    }
    .sv-modal-box {
        background: var(--dk-surface); border-radius: 14px;
        width: 100%; max-width: 520px; box-shadow: 0 10px 40px rgba(0,0,0,.3);
        padding: 28px; margin: 16px; max-height: 90vh; overflow-y: auto;
        position: relative; border: 1px solid var(--dk-b2);
    }
    .sv-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px;
    }
    .sv-modal-header h3 {
        font-size: 15px; font-weight: 600; color: var(--dk-t1);
        margin: 0;
    }
    .sv-modal-close {
        border: none; background: none; cursor: pointer;
        color: var(--dk-t4); padding: 4px; font-size: 18px; line-height: 1;
    }
    .sv-modal-error {
        display: none; background: rgba(220,38,38,0.15);
        color: #f87171; padding: 10px 14px; border-radius: 6px;
        font-size: 13px; font-weight: 600; margin-bottom: 14px;
        border: 1px solid rgba(220,38,38,0.3);
    }
    .sv-form-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
    }
    .sv-form-full { grid-column: 1/-1; }
    .sv-form-label {
        font-size: 11px; font-weight: 600; color: var(--dk-t3);
        letter-spacing: .3px; display: block; margin-bottom: 4px;
    }
    .sv-form-input, .sv-form-select {
        width: 100%; padding: 8px 12px;
        border: 1px solid var(--dk-b2); border-radius: 6px;
        font-size: 13px; color: var(--dk-t2);
        outline: none; background: var(--dk-surface2);
        box-sizing: border-box;
    }
    .sv-form-input:focus, .sv-form-select:focus {
        border-color: #3b82f6;
    }
    .sv-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        margin-top: 24px; padding-top: 16px;
        border-top: 1px solid var(--dk-b2);
    }
    .sv-btn-cancel {
        padding: 8px 16px; border: 1.5px solid var(--dk-b2);
        background: transparent; color: var(--dk-t2);
        border-radius: 6px; font-size: 13px; font-weight: 600;
        cursor: pointer;
    }
    .sv-btn-save {
        padding: 8px 16px; background: #2563eb; color: #fff;
        border: none; border-radius: 6px; font-size: 13px;
        font-weight: 600; cursor: pointer;
    }
    .sv-btn-save:disabled { opacity: 0.5; cursor: not-allowed; }

    @media (max-width: 640px) {
        .sv-block-meta { display: none; }
    }
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────── --}}
<div class="sv-header">
    <div>
        <h2>
            @if($role == 1)
                My Schedule
            @elseif($role == 2)
                My Class Schedule
            @else
                Schedule Management
            @endif
        </h2>
        <p>
            @if($role == 1)
                Your weekly teaching schedule across all sections.
            @elseif($role == 2)
                Your weekly class timetable for this academic period.
            @else
                Manage all class schedules, filter by grade, section, or teacher.
            @endif
        </p>
    </div>
</div>

{{-- ── Filters (All users) ─────────────────────────────────────── --}}
<div class="sv-filter-bar">
    @if($role == 3 || $role == 0) {{-- Admin filters --}}
        <select id="sv-filter-grade" class="form-select" style="width:170px;">
            <option value="">All Grade Levels</option>
            @isset($gradeLevels)
                @foreach($gradeLevels as $gl)
                    <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                @endforeach
            @endisset
        </select>

        <select id="sv-filter-section" class="form-select" style="width:170px;">
            <option value="">All Sections</option>
            @isset($sections)
                @foreach($sections as $sec)
                    <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">
                        {{ $sec->grade_level_name }} — {{ $sec->section_name }}
                    </option>
                @endforeach
            @endisset
        </select>

        <select id="sv-filter-teacher" class="form-select" style="width:200px;">
            <option value="">All Teachers</option>
            @isset($teachers)
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
                @endforeach
            @endisset
        </select>

        <button class="btn-outline" id="btn-sv-reset">Reset</button>

        <button class="btn-primary" id="btn-add-schedule" style="margin-left:auto;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            Add Schedule
        </button>
    @else {{-- Teacher/Student day filter --}}
        <select id="svDayFilter">
            <option value="">All Days</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
        </select>
    @endif
</div>

{{-- ── Timetable card ───────────────────────────────────────────── --}}
<div class="sv-card">
    <div class="sv-table-wrap">
        <div id="svGridWrap">
            <div class="sv-placeholder">
                <span style="display:inline-flex; align-items:center; gap:8px; color:var(--dk-t4); font-size:13px;">
                    Loading schedule
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                </span>
            </div>
        </div>
    </div>
    <div class="sv-summary" id="svSummary" style="display:none;"></div>
</div>

{{-- ── Admin Modal (only visible to admin roles) ─────────────────────── --}}
@if($role == 3 || $role == 0)
<div id="sv-modal-backdrop" class="sv-modal-backdrop">
    <div class="sv-modal-box">
        <div class="sv-modal-header">
            <h3 id="sv-modal-title">Add Schedule</h3>
            <button id="btn-sv-modal-close" class="sv-modal-close">✕</button>
        </div>

        <div id="sv-modal-error" class="sv-modal-error"></div>

        <input type="hidden" id="sv-edit-id">

        <div class="sv-form-grid">
            <div class="sv-form-full">
                <label class="sv-form-label">SUBJECT *</label>
                <select id="sv-subject" class="sv-form-select">
                    <option value="">Select subject</option>
                    @isset($subjects)
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="sv-form-full">
                <label class="sv-form-label">TEACHER *</label>
                <select id="sv-teacher" class="sv-form-select">
                    <option value="">Select teacher</option>
                    @isset($teachers)
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div>
                <label class="sv-form-label">GRADE LEVEL *</label>
                <select id="sv-grade" class="sv-form-select">
                    <option value="">Select grade</option>
                    @isset($gradeLevels)
                        @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div>
                <label class="sv-form-label">SECTION *</label>
                <select id="sv-section" class="sv-form-select">
                    <option value="">Select section</option>
                    @isset($sections)
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">{{ $sec->section_name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div>
                <label class="sv-form-label">DAY *</label>
                <select id="sv-day" class="sv-form-select">
                    <option value="">Select day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>

            <div>
                <label class="sv-form-label">ROOM *</label>
                <input type="text" id="sv-room" placeholder="e.g. Room 101" maxlength="100" class="sv-form-input">
            </div>

            <div>
                <label class="sv-form-label">START TIME *</label>
                <input type="time" id="sv-time-start" class="sv-form-input">
            </div>

            <div>
                <label class="sv-form-label">END TIME *</label>
                <input type="time" id="sv-time-end" class="sv-form-input">
            </div>
        </div>

        <div class="sv-modal-footer">
            <button id="btn-sv-cancel" class="sv-btn-cancel">Cancel</button>
            <button id="btn-sv-save" class="sv-btn-save">Save Schedule</button>
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

    // Role injected from controller
    const ROLE = {{ (int)($role ?? 2) }}; // 1 = teacher, 2 = student, 3 = admin

    // API Routes based on role
    const LIST_ROUTE = ROLE === 1 ? '{{ route("teacher.schedule.list") }}' :
                      (ROLE === 2 ? '{{ route("student.schedule.list") }}' :
                      '{{ route("admin.schedule.list") }}');

    // Admin CRUD routes
    const URL_STORE   = '{{ route("admin.schedule.store") }}';
    const URL_UPDATE  = '{{ route("admin.schedule.update") }}';
    const URL_DESTROY = '{{ route("admin.schedule.destroy") }}';
    const CSRF = '{{ csrf_token() }}';

    const DAYS    = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
    const DAY_KEY = { Monday:'mon', Tuesday:'tue', Wednesday:'wed', Thursday:'thu', Friday:'fri' };

    let allRows = [];
    
    // Store original select HTML for admin modal
    let selectOriginals = {};

    // ── Admin Modal Elements ──────────────────────────────────────
    let backdrop, modalErr, editId, fSubject, fTeacher, fGrade, fSection, fDay, fRoom, fStart, fEnd, saveBtn;
    
    // Define openModal in a scope accessible to both the initialization and event handlers
    let openModalFn = null;
    
    if (ROLE === 3) {
        backdrop  = document.getElementById('sv-modal-backdrop');
        modalErr  = document.getElementById('sv-modal-error');
        editId    = document.getElementById('sv-edit-id');
        fSubject  = document.getElementById('sv-subject');
        fTeacher  = document.getElementById('sv-teacher');
        fGrade    = document.getElementById('sv-grade');
        fSection  = document.getElementById('sv-section');
        fDay      = document.getElementById('sv-day');
        fRoom     = document.getElementById('sv-room');
        fStart    = document.getElementById('sv-time-start');
        fEnd      = document.getElementById('sv-time-end');
        saveBtn   = document.getElementById('btn-sv-save');

        // Store originals
        ['sv-subject','sv-teacher','sv-grade','sv-section','sv-day'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) selectOriginals[id] = el.innerHTML;
        });

        // Grade → Section filter
        if (fGrade) {
            fGrade.addEventListener('change', function () {
                var gid = this.value;
                if (!fSection) return;
                
                fSection.innerHTML = '<option value="">Loading...</option>';
                fSection.disabled = true;
                fSection.style.opacity = '.5';

                setTimeout(function () {
                    if (selectOriginals['sv-section']) {
                        fSection.innerHTML = selectOriginals['sv-section'];
                    }
                    Array.from(fSection.options).forEach(function (opt) {
                        if (!opt.value) return;
                        opt.hidden = gid ? opt.dataset.grade !== gid : false;
                    });
                    fSection.value = '';
                    fSection.disabled = false;
                    fSection.style.opacity = '1';
                }, 250);
            });
        }

        // Define the openModal function
        openModalFn = function(title, saveTxt, row) {
            console.log('Opening modal with row data:', row); // Debug log
            
            const titleEl = document.getElementById('sv-modal-title');
            if (titleEl) titleEl.textContent = title;
            if (saveBtn) saveBtn.textContent = saveTxt;
            if (modalErr) modalErr.style.display = 'none';
            
            // First, restore all selects to their original state
            setModalLoading(false);
            
            if (editId) editId.value = row ? row.id : '';
            
            // Set values from row data
            if (row) {
                console.log('Setting form values:', {
                    subject_id: row.subject_id,
                    user_id: row.user_id,
                    grade_level_id: row.grade_level_id,
                    section_id: row.section_id,
                    day: row.day,
                    room: row.room,
                    time_start: row.time_start,
                    time_end: row.time_end
                });
                
                // Set simple fields first
                if (fSubject) {
                    fSubject.value = row.subject_id || '';
                    console.log('Subject set to:', fSubject.value);
                }
                if (fTeacher) {
                    fTeacher.value = row.user_id || '';
                    console.log('Teacher set to:', fTeacher.value);
                }
                if (fGrade) {
                    fGrade.value = row.grade_level_id || '';
                    console.log('Grade set to:', fGrade.value);
                }
                if (fDay) {
                    fDay.value = row.day || '';
                    console.log('Day set to:', fDay.value);
                }
                if (fRoom) {
                    fRoom.value = row.room || '';
                    console.log('Room set to:', fRoom.value);
                }
                if (fStart) {
                    fStart.value = row.time_start || '';
                    console.log('Start time set to:', fStart.value);
                }
                if (fEnd) {
                    fEnd.value = row.time_end || '';
                    console.log('End time set to:', fEnd.value);
                }

                // Handle section filtering based on grade
                if (row.grade_level_id && fSection) {
                    console.log('Filtering sections for grade:', row.grade_level_id);
                    
                    // First, show all sections
                    Array.from(fSection.options).forEach(function (opt) {
                        if (!opt.value) return;
                        opt.hidden = false;
                    });
                    
                    // Then hide sections not matching the grade
                    Array.from(fSection.options).forEach(function (opt) {
                        if (!opt.value) return;
                        const shouldHide = opt.dataset.grade !== String(row.grade_level_id);
                        opt.hidden = shouldHide;
                        console.log(`Option ${opt.value} (grade: ${opt.dataset.grade}) hidden: ${shouldHide}`);
                    });
                    
                    // Set the section value
                    setTimeout(function() {
                        if (fSection) {
                            fSection.value = row.section_id || '';
                            console.log('Section set to:', fSection.value);
                        }
                    }, 50);
                }
            } else {
                // Reset form for new entry
                if (fSubject) fSubject.value = '';
                if (fTeacher) fTeacher.value = '';
                if (fGrade) fGrade.value = '';
                if (fSection) {
                    // Show all sections
                    Array.from(fSection.options).forEach(function (opt) {
                        if (!opt.value) return;
                        opt.hidden = false;
                    });
                    fSection.value = '';
                }
                if (fDay) fDay.value = '';
                if (fRoom) fRoom.value = '';
                if (fStart) fStart.value = '';
                if (fEnd) fEnd.value = '';
            }

            if (backdrop) backdrop.style.display = 'flex';
        };

        function closeModal() { 
            if (backdrop) backdrop.style.display = 'none'; 
        }

        if (backdrop) {
            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) closeModal();
            });
        }

        const addBtn = document.getElementById('btn-add-schedule');
        if (addBtn) {
            addBtn.addEventListener('click', function () { 
                if (openModalFn) openModalFn('Add Schedule', 'Save Schedule', null); 
            });
        }
        
        const closeBtn = document.getElementById('btn-sv-modal-close');
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        
        const cancelBtn = document.getElementById('btn-sv-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Save schedule
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                if (modalErr) modalErr.style.display = 'none';

                var id = editId ? editId.value : '';
                var subject = fSubject ? fSubject.value : '';
                var teacher = fTeacher ? fTeacher.value : '';
                var grade = fGrade ? fGrade.value : '';
                var section = fSection ? fSection.value : '';
                var day = fDay ? fDay.value : '';
                var room = fRoom ? fRoom.value.trim() : '';
                var start = fStart ? fStart.value : '';
                var end = fEnd ? fEnd.value : '';

                if (!subject) { showErr('Please select a subject.'); return; }
                if (!teacher) { showErr('Please select a teacher.'); return; }
                if (!grade) { showErr('Please select a grade level.'); return; }
                if (!section) { showErr('Please select a section.'); return; }
                if (!day) { showErr('Please select a day.'); return; }
                if (!room) { showErr('Room is required.'); return; }
                if (!start) { showErr('Start time is required.'); return; }
                if (!end) { showErr('End time is required.'); return; }
                if (start >= end) { showErr('End time must be after start time.'); return; }

                var url = id ? URL_UPDATE : URL_STORE;
                var data = {
                    subject_id: subject, 
                    user_id: teacher, 
                    section_id: section,
                    grade_level_id: grade, 
                    room: room, 
                    day: day,
                    time_start: start, 
                    time_end: end, 
                    _token: CSRF
                };
                if (id) data.id = id;

                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving…';

                $.ajax({
                    url: url, 
                    method: 'POST', 
                    data: data,
                    success: function (res) {
                        closeModal();
                        if (typeof showPopup === 'function') {
                            showPopup('Success', res.message, 'success');
                        }
                        loadSchedule();
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.';
                        showErr(msg);
                    },
                    complete: function () {
                        saveBtn.disabled = false;
                        saveBtn.textContent = id ? 'Update Schedule' : 'Save Schedule';
                    }
                });
            });
        }

        function showErr(msg) {
            if (modalErr) {
                modalErr.textContent = msg;
                modalErr.style.display = 'block';
            }
        }

        function setModalLoading(loading) {
            var selectIds = ['sv-subject','sv-teacher','sv-grade','sv-section','sv-day'];
            var inputs = [fRoom, fStart, fEnd];

            if (loading) {
                selectIds.forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.innerHTML = '<option value="">Loading...</option>';
                        el.disabled = true;
                        el.style.opacity = '.5';
                    }
                });
                if (inputs) {
                    inputs.forEach(function (el) { if (el) { el.disabled = true; el.style.opacity = '.4'; } });
                }
                if (saveBtn) saveBtn.disabled = true;
            } else {
                selectIds.forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el && selectOriginals[id]) {
                        el.innerHTML = selectOriginals[id];
                        el.disabled = false;
                        el.style.opacity = '1';
                    }
                });
                if (inputs) {
                    inputs.forEach(function (el) { if (el) { el.disabled = false; el.style.opacity = '1'; } });
                }
                if (saveBtn) saveBtn.disabled = false;
            }
        }
    }

    // ── Common Functions ──────────────────────────────────────────

    // Load schedule data
    function loadSchedule() {
        $('#svGridWrap').html(`
            <div class="sv-placeholder">
                <span style="display:inline-flex;align-items:center;gap:8px;color:var(--dk-t4);font-size:13px;">
                    Loading schedule
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                </span>
            </div>`);
        $('#svSummary').hide();

        let params = {};
        
        // Add admin filters if applicable
        if (ROLE === 3) {
            params = {
                section_id: $('#sv-filter-section').val(),
                grade_level_id: $('#sv-filter-grade').val(),
                teacher_id: $('#sv-filter-teacher').val(),
            };
        }

        $.ajax({
            url: LIST_ROUTE, 
            type: 'GET', 
            data: params,
            dataType: 'json',
            success: function (res) {
                if (res.status !== 'success') {
                    showError(res.message ?? 'Failed to load schedule.');
                    return;
                }
                allRows = res.data ?? [];
                console.log('Loaded schedule data:', allRows); // Debug log
                render(allRows);
            },
            error: function (xhr) {
                showError(xhr.responseJSON?.message ?? 'Network error.');
            }
        });
    }

    function showError(msg) {
        $('#svGridWrap').html(`
            <div class="sv-placeholder">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                </svg>
                <p style="color:var(--red-400);">${msg}</p>
            </div>`);
        if (typeof showPopup === 'function') {
            showPopup('Error', msg, 'error');
        }
    }

    // ── Filter events ─────────────────────────────────────────────
    if (ROLE === 3) {
        // Admin filters
        $('#sv-filter-grade').on('change', function () {
            var gid = $(this).val();
            var $secSel = $('#sv-filter-section');
            $secSel.find('option').each(function () {
                if (!$(this).val()) return;
                $(this).toggle(!gid || $(this).data('grade') == gid);
            });
            $secSel.val('');
            loadSchedule();
        });

        $('#sv-filter-section, #sv-filter-teacher').on('change', loadSchedule);

        $('#btn-sv-reset').on('click', function () {
            $('#sv-filter-grade').val('');
            $('#sv-filter-section').val('');
            $('#sv-filter-teacher').val('');
            $('#sv-filter-section option').each(function () { $(this).show(); });
            loadSchedule();
        });
    } else {
        // Teacher/Student day filter
        $('#svDayFilter').on('change', function () {
            const day = $(this).val();
            render(day ? allRows.filter(r => r.day === day) : allRows);
        });
    }

    // ── Time helpers ──────────────────────────────────────────────
    function toMin(t) {
        if (!t) return 0;
        if (/[AP]M/i.test(t)) {
            const parts = t.trim().split(' ');
            const [h, m] = parts[0].split(':').map(Number);
            const ispm = parts[1].toUpperCase() === 'PM';
            return ((ispm && h !== 12 ? h + 12 : (!ispm && h === 12 ? 0 : h)) * 60) + (m || 0);
        }
        const [h, m] = t.split(':').map(Number);
        return h * 60 + (m || 0);
    }

    function fmtTime(t) {
        const min = toMin(t);
        let h = Math.floor(min / 60);
        const m = min % 60;
        const ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return h + ':' + String(m).padStart(2, '0') + ' ' + ap;
    }

    function esc(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Render timetable ──────────────────────────────────────────
    function render(rows) {
        if (!rows.length) {
            $('#svGridWrap').html(`
                <div class="sv-placeholder">
                    <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    <p>No schedule entries found.</p>
                </div>`);
            $('#svSummary').hide();
            return;
        }

        // Only show days that have data
        const activeDays = DAYS.filter(d => rows.some(r => r.day === d));

        // Dynamic time range
        let minMin = Infinity, maxMin = -Infinity;
        rows.forEach(r => {
            const s = toMin(r.time_start), e = toMin(r.time_end);
            if (s < minMin) minMin = s;
            if (e > maxMin) maxMin = e;
        });

        const startH = Math.max(0, Math.floor(minMin / 60) - 1);
        const endH = Math.min(23, Math.ceil(maxMin / 60) + 1);
        const hours = [];
        for (let h = startH; h <= endH; h++) {
            hours.push(String(h).padStart(2, '0') + ':00');
        }

        // Group by day
        const byDay = {};
        activeDays.forEach(d => { byDay[d] = []; });
        rows.forEach(r => { if (byDay[r.day]) byDay[r.day].push(r); });

        const colPct = `calc((100% - 96px) / ${activeDays.length})`;

        // ── Build table HTML ──────────────────────────────────────
        let html = `<table class="sv-table">
            <colgroup>
                <col style="width:96px;">
                ${activeDays.map(() => `<col style="width:${colPct};">`).join('')}
            </colgroup>
            <thead><tr>
                <th class="time-col">TIME</th>
                ${activeDays.map(d => `<th class="day-head-${DAY_KEY[d]}">${d}</th>`).join('')}
            </tr></thead>
            <tbody>`;

        for (let i = 0; i < hours.length - 1; i++) {
            const sMin = toMin(hours[i]);
            const eMin = toMin(hours[i + 1]);

            html += `<tr>
                <td class="time-lbl">
                    <div class="time-lbl-main">${fmtTime(hours[i])}</div>
                    <div class="time-lbl-sub">– ${fmtTime(hours[i + 1])}</div>
                </td>`;

            activeDays.forEach(d => {
                const items = (byDay[d] || []).filter(r =>
                    toMin(r.time_start) >= sMin && toMin(r.time_start) < eMin
                );

                html += '<td>';
                items.forEach(r => {
                    const cls = 'day-' + DAY_KEY[d];
                    html += `<div class="sv-block ${cls}">
                        <div class="sv-block-subject">${esc(r.subject_name)}</div>
                        <div class="sv-block-meta">`;

                    // Different metadata based on role
                    if (ROLE === 1) { // Teacher
                        html += `📚 ${esc(r.section_name)}`;
                        if (r.grade_level_name) html += ` · ${esc(r.grade_level_name)}`;
                    } else if (ROLE === 2) { // Student
                        html += `👤 ${esc(r.teacher_name)}`;
                    } else { // Admin
                        html += `👤 ${esc(r.teacher_name)} · 📚 ${esc(r.section_name)}`;
                        if (r.grade_level_name) html += ` · ${esc(r.grade_level_name)}`;
                    }

                    html += `<br>📍 ${esc(r.room)}</div>
                        <div class="sv-block-time">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
                            </svg>
                            ${esc(r.time_start)} – ${esc(r.time_end)}
                        </div>`;

                    // Admin actions
                    if (ROLE === 3) {
                        html += `<div class="sv-block-actions">
                            <button class="btn-sm btn-edit" data-action="edit" data-id="${r.id}">Edit</button>
                            <button class="btn-sm btn-delete" data-action="delete" data-id="${r.id}" data-name="${esc(r.subject_name)}">Delete</button>
                        </div>`;
                    }

                    html += `</div>`;
                });

                if (!items.length) html += '<div style="height:36px;"></div>';
                html += '</td>';
            });

            html += '</tr>';
        }

        html += '</tbody></table>';
        $('#svGridWrap').html(html);

        // Wire admin action buttons
        if (ROLE === 3) {
            var rowMap = {};
            rows.forEach(function (r) { 
                rowMap[r.id] = r; 
                console.log('Mapping row:', r.id, r); // Debug log
            });

            $('#svGridWrap [data-action="edit"]').on('click', function (e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var row = rowMap[id];
                console.log('Edit clicked for ID:', id, 'Row data:', row); // Debug log
                
                if (row && openModalFn) {
                    openModalFn('Edit Schedule', 'Update Schedule', row);
                } else {
                    console.error('Cannot open modal:', {row: row, openModalFn: openModalFn});
                    if (!row) console.error('Row not found for ID:', id);
                    if (!openModalFn) console.error('openModalFn is not defined');
                }
            });

            $('#svGridWrap [data-action="delete"]').on('click', function (e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var name = $(this).data('name');
                if (typeof showConfirmationModal === 'function') {
                    showConfirmationModal('Delete Schedule', 'Are you sure you want to delete "' + name + '"?', function () {
                        $.ajax({
                            url: URL_DESTROY, 
                            method: 'POST',
                            data: { id: id, _token: CSRF },
                            success: function (res) {
                                if (typeof showPopup === 'function') {
                                    showPopup('Deleted', res.message, 'success');
                                }
                                loadSchedule();
                            },
                            error: function (xhr) {
                                if (typeof showPopup === 'function') {
                                    showPopup('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed.', 'error');
                                }
                            }
                        });
                    });
                }
            });
        }

        // ── Summary chips ─────────────────────────────────────────
        const subjects = [...new Set(rows.map(r => r.subject_name))];
        const totalSlots = rows.length;
        const teachers = ROLE === 3 ? [...new Set(rows.map(r => r.teacher_name))] : [];
        
        let summaryHtml = `
            <span class="sv-chip">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
                ${activeDays.length} day${activeDays.length !== 1 ? 's' : ''}
            </span>
            <span class="sv-chip green">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                ${subjects.length} subject${subjects.length !== 1 ? 's' : ''}
            </span>
            <span class="sv-chip yellow">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
                </svg>
                ${totalSlots} session${totalSlots !== 1 ? 's' : ''} / week
            </span>`;

        if (ROLE === 3 && teachers.length > 0) {
            summaryHtml += `
            <span class="sv-chip">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
                </svg>
                ${teachers.length} teacher${teachers.length !== 1 ? 's' : ''}
            </span>`;
        }

        $('#svSummary').html(summaryHtml).show();
    }

    // ── Init ──────────────────────────────────────────────────────
    loadSchedule();
});
</script>
@endpush