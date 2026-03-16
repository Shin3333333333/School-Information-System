{{-- resources/views/grades.blade.php --}}
{{--
    Shared view for Admin, Teacher, and Student roles.
    $role: 0 = Admin | 1 = Teacher | 2 = Student
    Students get a read-only table scoped to their own grades.
    Admin / Teacher get the full CRUD interface.
--}}
@extends('layouts.app')

@section('title', 'Grades')

@push('styles')
<style>
    /* ── Page header ─────────────────────────────────────────────── */
    .grades-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .grades-header h2 {
        font-family: var(--font-display);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dk-t1);
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .grades-header p {
        margin: 0;
        font-size: 0.82rem;
        color: var(--dk-t3);
    }
    .role-badge {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: .05em;
        padding: 3px 10px;
        border-radius: 20px;
        vertical-align: middle;
    }
    .role-badge.admin   { background: rgba(37,99,235,0.15);  color: #60a5fa; }
    .role-badge.teacher { background: rgba(22,163,74,0.15);  color: #4ade80; }
    .role-badge.student { background: rgba(217,119,6,0.15);  color: #fbbf24; }

    /* ── Stats strip ─────────────────────────────────────────────── */
    .stats-strip {
        display: flex;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .stats-strip .stat-card {
        flex: 1;
        min-width: 130px;
        background: var(--dk-surface);
        border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg);
        padding: 14px 18px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        transition: border-color .2s, transform .2s;
    }
    .stats-strip .stat-card:hover { border-color: rgba(255,255,255,0.12); transform: translateY(-2px); }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: rgba(37,99,235,0.15);  color: #60a5fa; }
    .stat-icon.green  { background: rgba(22,163,74,0.15);  color: #4ade80; }
    .stat-icon.red    { background: rgba(220,38,38,0.15);   color: #f87171; }
    .stat-icon.yellow { background: rgba(217,119,6,0.15);  color: #fbbf24; }
    .stat-info span   { display: block; font-size: 0.72rem; color: var(--dk-t3); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .stat-info strong { font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: var(--dk-t1); }

    /* ── Filter bar ──────────────────────────────────────────────── */
    .filter-bar {
        background: var(--dk-surface);
        border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        margin-bottom: 20px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 150px;
    }
    .filter-group label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--dk-t4);
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .filter-bar select {
        padding: 8px 12px;
        border: 1.5px solid var(--dk-b1);
        border-radius: var(--radius-md);
        font-size: 0.84rem;
        color: var(--dk-t2);
        background: var(--dk-surface2);
        font-family: var(--font-body);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 28px;
    }
    .filter-bar select:focus {
        border-color: rgba(96,165,250,0.4);
        box-shadow: 0 0 0 3px rgba(96,165,250,0.08);
    }
    .filter-bar select option { background: #111827; color: #cbd5e1; }
    .btn-filter {
        padding: 8px 20px;
        background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 600;
        cursor: pointer; height: 36px; align-self: flex-end;
        transition: background .2s, transform .15s;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }
    .btn-filter:hover { background: var(--blue-700); transform: translateY(-1px); }
    .btn-reset-filter {
        padding: 8px 14px;
        background: rgba(255,255,255,0.05); color: var(--dk-t3);
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 600;
        cursor: pointer; height: 36px; align-self: flex-end;
        transition: all .2s;
    }
    .btn-reset-filter:hover { background: rgba(255,255,255,0.08); color: var(--dk-t1); }

    /* ── Table card ──────────────────────────────────────────────── */
    .table-card {
        background: var(--dk-surface);
        border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid var(--dk-b2);
        flex-wrap: wrap;
        gap: 10px;
        background: var(--dk-surface);
    }
    .table-toolbar .left { display: flex; align-items: center; gap: 10px; }
    .search-input {
        padding: 7px 12px 7px 34px;
        border: 1.5px solid var(--dk-b1);
        border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--dk-t2);
        background: var(--dk-surface2) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center;
        outline: none;
        width: 220px;
        transition: border-color .2s, box-shadow .2s;
    }
    .search-input::placeholder { color: var(--dk-t4); }
    .search-input:focus {
        border-color: rgba(96,165,250,0.4);
        box-shadow: 0 0 0 3px rgba(96,165,250,0.08);
    }
    .record-count { font-size: 0.78rem; color: var(--dk-t4); }
    .btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px;
        background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }
    .btn-add:hover { background: var(--blue-700); transform: translateY(-1px); }

    /* ── Table ───────────────────────────────────────────────────── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    .data-table thead tr { background: var(--dk-surface2); }
    .data-table thead th {
        padding: 11px 16px; text-align: left;
        font-size: 0.72rem; font-weight: 700;
        color: var(--dk-t4); text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 1.5px solid var(--dk-b2); white-space: nowrap;
    }
    .data-table tbody tr {
        border-bottom: 1px solid var(--dk-b2);
        background: var(--dk-surface);
        transition: background .12s;
    }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255,255,255,0.03); }
    .data-table td { padding: 12px 16px; color: var(--dk-t2); vertical-align: middle; }

    /* Grade pills */
    .grade-pill {
        display: inline-block; padding: 3px 10px;
        border-radius: 20px; font-size: 0.78rem; font-weight: 700;
    }
    .grade-pill.outstanding { background: rgba(22,163,74,0.18);  color: #4ade80; }
    .grade-pill.passed      { background: rgba(22,163,74,0.12);  color: #86efac; }
    .grade-pill.average     { background: rgba(217,119,6,0.18);  color: #fbbf24; }
    .grade-pill.failed      { background: rgba(220,38,38,0.18);  color: #f87171; }

    /* Quarter badge */
    .quarter-badge {
        display: inline-block; padding: 2px 9px;
        border-radius: 6px; font-size: 0.75rem; font-weight: 700;
        background: rgba(37,99,235,0.15); color: #60a5fa;
    }

    /* Action buttons */
    .action-btns { display: flex; gap: 6px; }
    .btn-icon {
        width: 30px; height: 30px; border: none; border-radius: 7px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all .18s;
    }
    .btn-icon.edit         { background: rgba(37,99,235,0.15);  color: #60a5fa; }
    .btn-icon.edit:hover   { background: var(--blue-600);        color: #fff; }
    .btn-icon.delete       { background: rgba(220,38,38,0.15);   color: #f87171; }
    .btn-icon.delete:hover { background: var(--red-600);         color: #fff; }

    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; color: var(--dk-t4); }
    .empty-state svg { margin-bottom: 12px; opacity: .3; color: var(--dk-t4); }
    .empty-state p { font-size: 0.88rem; margin: 0; }

    /* Skeleton */
    .skeleton-cell {
        height: 14px; border-radius: 6px;
        background: linear-gradient(90deg,
            rgba(255,255,255,0.04) 25%,
            rgba(255,255,255,0.08) 50%,
            rgba(255,255,255,0.04) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
    }
    @keyframes shimmer { to { background-position: -200% 0; } }

    /* ── Modals ──────────────────────────────────────────────────── */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.65);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; backdrop-filter: blur(3px);
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: var(--dk-surface);
        border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl);
        width: 100%; max-width: 560px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: var(--shadow-xl);
        animation: modalIn .22s ease-out;
    }
    @keyframes modalIn {
        from { opacity:0; transform:translateY(20px) scale(.97); }
        to   { opacity:1; transform:translateY(0)   scale(1);   }
    }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--dk-b2);
    }
    .modal-header h3 {
        font-family: var(--font-display);
        font-size: 1rem; font-weight: 700;
        color: var(--dk-t1); margin: 0;
    }
    .modal-close {
        width: 30px; height: 30px; border: none;
        background: rgba(255,255,255,0.06);
        border-radius: 8px; cursor: pointer;
        font-size: 1rem; color: var(--dk-t3);
        display: flex; align-items: center; justify-content: center;
        transition: all .18s;
    }
    .modal-close:hover { background: rgba(255,255,255,0.1); color: var(--dk-t1); }
    .modal-body { padding: 20px 24px; }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--dk-b2);
        display: flex; justify-content: flex-end; gap: 10px;
    }

    /* Form */
    .form-row { display: flex; gap: 14px; margin-bottom: 14px; }
    .form-row .form-group { flex: 1; }
    .form-group { margin-bottom: 14px; }
    .form-group label {
        display: block; font-size: 0.76rem; font-weight: 700;
        color: var(--dk-t3); margin-bottom: 5px;
        text-transform: uppercase; letter-spacing: .04em;
    }
    .form-group label .req { color: var(--red-400); margin-left: 2px; }
    .form-control {
        width: 100%; padding: 8px 12px;
        border: 1.5px solid var(--dk-b1);
        border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; color: var(--dk-t2);
        background: var(--dk-surface2);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        box-sizing: border-box;
        appearance: none;
    }
    .form-control:focus {
        border-color: rgba(96,165,250,0.4);
        box-shadow: 0 0 0 3px rgba(96,165,250,0.08);
    }
    .form-control::placeholder { color: var(--dk-t4); }
    .form-control option { background: #111827; color: #cbd5e1; }
    .form-control.is-invalid { border-color: var(--red-500); }
    .invalid-feedback { font-size: 0.74rem; color: var(--red-400); margin-top: 3px; display: none; }
    .form-control.is-invalid + .invalid-feedback { display: block; }

    /* Grade preview bar */
    .grade-preview {
        margin-top: 6px; height: 6px;
        border-radius: 10px;
        background: rgba(255,255,255,0.08);
        overflow: hidden;
    }
    .grade-preview-fill { height: 100%; border-radius: 10px; transition: width .4s, background .4s; }
    .grade-hint { font-size: 0.74rem; font-weight: 700; margin-top: 4px; min-height: 16px; }

    /* Modal buttons */
    .btn-cancel {
        padding: 9px 20px;
        background: rgba(255,255,255,0.05); color: var(--dk-t3);
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 600; cursor: pointer;
        transition: all .18s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,0.09); color: var(--dk-t1); }
    .btn-save {
        padding: 9px 24px;
        background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 700; cursor: pointer;
        transition: background .18s, transform .15s;
        display: inline-flex; align-items: center; gap: 6px;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }
    .btn-save:hover    { background: var(--blue-700); transform: translateY(-1px); }
    .btn-save:disabled { background: rgba(37,99,235,0.3); color: rgba(255,255,255,0.4); cursor: not-allowed; transform: none; box-shadow: none; }

    /* Delete confirm modal */
    .confirm-box {
        background: var(--dk-surface);
        border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl);
        width: 100%; max-width: 400px;
        padding: 28px 28px 22px; text-align: center;
        box-shadow: var(--shadow-xl);
        animation: modalIn .22s ease-out;
    }
    .confirm-icon {
        width: 52px; height: 52px;
        background: rgba(220,38,38,0.15);
        border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 1.4rem; margin: 0 auto 14px;
    }
    .confirm-box h3 { font-size: 1rem; font-weight: 700; color: var(--dk-t1); margin: 0 0 6px; }
    .confirm-box p  { font-size: 0.84rem; color: var(--dk-t3); margin: 0 0 22px; }
    .confirm-actions { display: flex; justify-content: center; gap: 10px; }
    .btn-danger {
        padding: 9px 24px;
        background: var(--red-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 700; cursor: pointer;
        transition: background .18s, transform .15s;
        box-shadow: 0 2px 8px rgba(220,38,38,0.3);
    }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-1px); }

    @media (max-width: 768px) {
        .form-row { flex-direction: column; gap: 0; }
        .stats-strip { gap: 10px; }
        .search-input { width: 160px; }
    }
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────────────────── --}}
<div class="grades-header">
    <div>
        <h2>
            @if($role == 2) My Grades @else Grades Management @endif
            @if($role == 0)
                <span class="role-badge admin">Admin</span>
            @elseif($role == 1)
                <span class="role-badge teacher">Teacher</span>
            @else
                <span class="role-badge student">Student</span>
            @endif
        </h2>
        <p>
            @if($role == 2)
                Your academic grade records across all subjects and quarters.
            @elseif($role == 1)
                Grade records for the sections you handle.
            @else
                View, add, edit, and remove student grade records.
            @endif
        </p>
    </div>
    {{-- Add Grade — hidden from students --}}
    @if($role != 2)
    <button class="btn-add" id="btnAddGrade">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
        </svg>
        Add Grade
    </button>
    @endif
</div>

{{-- ── Stats strip ──────────────────────────────────────────────────────────── --}}
<div class="stats-strip">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
        </div>
        <div class="stat-info"><span>Total Records</span><strong id="statTotal">—</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/>
            </svg>
        </div>
        <div class="stat-info"><span>Passed</span><strong id="statPassed">—</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
        </div>
        <div class="stat-info"><span>Failed</span><strong id="statFailed">—</strong></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </div>
        <div class="stat-info"><span>Average</span><strong id="statAvg">—</strong></div>
    </div>
</div>

{{-- ── Filters ───────────────────────────────────────────────────────────────── --}}
<div class="filter-bar">
    @if($role != 2)
    <div class="filter-group">
        <label>Grade Level</label>
        <select id="filterGradeLevel">
            <option value="">All Levels</option>
            @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>Section</label>
        <select id="filterSection">
            <option value="">All Sections</option>
            @foreach($sections as $sec)
                <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">
                    {{ $sec->section_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>Subject</label>
        <select id="filterSubject">
            <option value="">All Subjects</option>
            @foreach($subjects as $sub)
                <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="filter-group">
        <label>Quarter</label>
        <select id="filterQuarter">
            <option value="">All Quarters</option>
            <option value="1">Quarter 1</option>
            <option value="2">Quarter 2</option>
            <option value="3">Quarter 3</option>
            <option value="4">Quarter 4</option>
        </select>
    </div>
    <button class="btn-filter" id="btnApplyFilter">
        <span id="filterBtnText">Filter</span>
        <span id="filterBtnLoader" class="loading loading-spinner" style="width:13px;height:13px;display:none;color:#fff;"></span>
    </button>
    <button class="btn-reset-filter" id="btnResetFilter">Reset</button>
</div>

{{-- ── Table ────────────────────────────────────────────────────────────────── --}}
<div class="table-card">
    <div class="table-toolbar">
        <div class="left">
            <input type="text" class="search-input" id="gradeSearch"
                   placeholder="{{ $role == 2 ? 'Search subject…' : 'Search student, subject…' }}">
            <span class="record-count" id="recordCount"></span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    @if($role != 2)<th>Student</th><th>Grade Level</th><th>Section</th>@endif
                    <th>Subject</th>
                    <th>Quarter</th>
                    <th>Grade</th>
                    <th>Descriptor</th>
                    <th>Remarks</th>
                    @if($role != 2)<th>Actions</th>@endif
                </tr>
            </thead>
            <tbody id="gradesBody">
                <tr id="initialLoadingRow">
                    <td colspan="{{ $role == 2 ? 6 : 9 }}" style="text-align:center; padding:48px;">
                        <span style="display:inline-flex; align-items:center; gap:8px; color:var(--dk-t4); font-size:13px;">
                            Loading grades
                            <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ── Add / Edit Modal (admin + teacher only) ─────────────────────────────── --}}
@if($role != 2)
<div class="modal-overlay" id="gradeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add Grade</h3>
            <button class="modal-close" id="btnModalClose">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="gradeId">

            <div class="form-row">
                <div class="form-group">
                    <label>Grade Level <span class="req">*</span></label>
                    <select class="form-control" id="modalGradeLevel">
                        <option value="">Select level…</option>
                        @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="form-group">
                    <label>Section <span class="req">*</span></label>
                    <div style="position:relative;">
                        <select class="form-control" id="modalSection">
                            <option value="">Select section…</option>
                        </select>
                        <span id="sectionLoadingDots" style="display:none; position:absolute; right:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                            <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </div>
                    <div class="invalid-feedback">Required.</div>
                </div>
            </div>

            <div class="form-group">
                <label>Student <span class="req">*</span></label>
                <div style="position:relative;">
                    <select class="form-control" id="modalStudent">
                        <option value="">Select student…</option>
                    </select>
                    <span id="studentLoadingDots" style="display:none; position:absolute; right:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                        <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                    </span>
                </div>
                <div class="invalid-feedback">Required.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Subject <span class="req">*</span></label>
                    <select class="form-control" id="modalSubject">
                        <option value="">Select subject…</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="form-group">
                    <label>Quarter <span class="req">*</span></label>
                    <select class="form-control" id="modalQuarter">
                        <option value="">Select quarter…</option>
                        <option value="1">Quarter 1</option>
                        <option value="2">Quarter 2</option>
                        <option value="3">Quarter 3</option>
                        <option value="4">Quarter 4</option>
                    </select>
                    <div class="invalid-feedback">Required.</div>
                </div>
            </div>

            <div class="form-group">
                <label>Grade (60–100) <span class="req">*</span></label>
                <input type="number" class="form-control" id="modalGrade"
                       min="60" max="100" step="0.01" placeholder="e.g. 87.50">
                <div class="grade-preview"><div class="grade-preview-fill" id="gradeFill"></div></div>
                <div class="grade-hint" id="gradeHint"></div>
                <div class="invalid-feedback">Enter a value between 60 and 100.</div>
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <input type="text" class="form-control" id="modalRemarks"
                       placeholder="Optional note…" maxlength="255">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancelModal">Cancel</button>
            <button class="btn-save" id="btnSaveGrade">
                <svg id="btnSaveIcon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <path d="M17 21v-8H7v8M7 3v5h8"/>
                </svg>
                <span class="loading loading-spinner" id="btnSaveLoader" style="width:13px;height:13px;display:none;color:#fff;"></span>
                <span id="btnSaveLabel">Save Grade</span>
            </button>
        </div>
    </div>
</div>

{{-- Delete confirm --}}
<div class="modal-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">🗑️</div>
        <h3>Delete Grade Record?</h3>
        <p>This action <strong>cannot be undone</strong>. The record will be permanently removed.</p>
        <div class="confirm-actions">
            <button class="btn-cancel" id="btnCancelDelete">Cancel</button>
            <button class="btn-danger" id="btnConfirmDelete">
                <span class="loading loading-spinner" id="btnDeleteLoader" style="width:13px;height:13px;display:none;color:#fff;"></span>
                <span id="btnDeleteLabel">Yes, Delete</span>
            </button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(function () {
    'use strict';

    // ── Role flags injected from Blade ────────────────────────────────────────
    const ROLE       = {{ (int)($role ?? 2) }};  // 0=admin 1=teacher 2=student
    const CAN_WRITE  = ROLE !== 2;               // admin + teacher
    const CAN_DELETE = ROLE === 0;               // admin only

    // ── jQuery AJAX setup — send CSRF token with every request ────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── Named routes ──────────────────────────────────────────────────────────
    const ROUTES = {
        list              : '{{ route("grades.list") }}',
        store             : '{{ route("grades.store") }}',
        update            : '{{ route("grades.update") }}',
        destroy           : '{{ route("grades.destroy") }}',
        studentsBySection : '{{ route("grades.studentsBySection") }}',
    };

    // All sections from controller — used for cascading section dropdown in modal
    const ALL_SECTIONS = @json($sections);

    // ── State ─────────────────────────────────────────────────────────────────
    let allRows   = [];
    let deleteId  = null;
    let isEditing = false;

    // ── Cached selectors ──────────────────────────────────────────────────────
    const $tbody       = $('#gradesBody');
    const $search      = $('#gradeSearch');
    const $recordCount = $('#recordCount');
    const $filterGL    = $('#filterGradeLevel');
    const $filterSec   = $('#filterSection');
    const $filterSubj  = $('#filterSubject');
    const $filterQtr   = $('#filterQuarter');

    // ── Grade helpers ─────────────────────────────────────────────────────────
    function gradeClass(g) {
        if (g >= 90) return 'outstanding';
        if (g >= 75) return 'passed';
        if (g >= 60) return 'average';
        return 'failed';
    }
    function gradeDescriptor(g) {
        if (g >= 90) return 'Outstanding';
        if (g >= 85) return 'Very Satisfactory';
        if (g >= 80) return 'Satisfactory';
        if (g >= 75) return 'Fairly Satisfactory';
        return 'Did Not Meet Expectations';
    }
    function gradeColor(g) {
        if (g >= 90) return '#22c55e';
        if (g >= 75) return '#f59e0b';
        return '#ef4444';
    }

    // ── Render table ──────────────────────────────────────────────────────────
    function renderTable(rows) {
        const colspan = ROLE === 2 ? 6 : 9;

        if (!rows.length) {
            $tbody.html(`
                <tr><td colspan="${colspan}">
                    <div class="empty-state">
                        <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                        <p>No grade records found.</p>
                    </div>
                </td></tr>`);
            $recordCount.text('0 records');
            updateStats([]);
            return;
        }

        const html = rows.map((r, i) => {
            const g    = parseFloat(r.grade ?? 0);
            const cls  = gradeClass(g);
            const desc = gradeDescriptor(g);
            const name = r.student_name ?? '';
            const qtr  = r.quarter ? `Q${r.quarter}` : '—';

            // Safely encode the row object for the inline onclick attribute
            const rowJson = JSON.stringify(r).replace(/"/g, '&quot;');

            const actions = CAN_WRITE ? `
                <td>
                    <div class="action-btns">
                        <button class="btn-icon edit" title="Edit"
                            onclick="GradesView.openEdit(${rowJson})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        ${CAN_DELETE ? `
                        <button class="btn-icon delete" title="Delete"
                            onclick="GradesView.confirmDelete(${r.id})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                            </svg>
                        </button>` : ''}
                    </div>
                </td>` : '';

            return `
            <tr data-id="${r.id}">
                <td>${i + 1}</td>
                ${ROLE !== 2 ? `<td><strong>${name || '—'}</strong></td>
                                <td>${r.grade_level_name ?? '—'}</td>
                                <td>${r.section_name ?? '—'}</td>` : ''}
                <td>${r.subject_name ?? '—'}</td>
                <td><span class="quarter-badge">${qtr}</span></td>
                <td><span class="grade-pill ${cls}">${g.toFixed(2)}</span></td>
                <td style="font-size:.8rem;color:var(--dk-t3);">${desc}</td>
                <td style="font-size:.8rem;color:var(--dk-t4);">${r.remarks ?? '—'}</td>
                ${actions}
            </tr>`;
        }).join('');

        $tbody.html(html);
        $recordCount.text(`${rows.length} record${rows.length !== 1 ? 's' : ''}`);
        updateStats(rows);
    }

    // ── Stats ─────────────────────────────────────────────────────────────────
    function updateStats(rows) {
        const total  = rows.length;
        const passed = rows.filter(r => parseFloat(r.grade) >= 75).length;
        const failed = total - passed;
        const avg    = total
            ? (rows.reduce((s, r) => s + parseFloat(r.grade ?? 0), 0) / total).toFixed(2)
            : '—';
        $('#statTotal').text(total);
        $('#statPassed').text(passed);
        $('#statFailed').text(failed);
        $('#statAvg').text(avg);
    }

    // ── Load grades ───────────────────────────────────────────────────────────
    // GET: /grades/list?grade_level_id=&section_id=&subject_id=
    function loadGrades() {
        const params = {};
        if ($filterGL.length  && $filterGL.val())   params.grade_level_id = $filterGL.val();
        if ($filterSec.length && $filterSec.val())  params.section_id     = $filterSec.val();
        if ($filterSubj.length && $filterSubj.val()) params.subject_id    = $filterSubj.val();

        // Show filter button spinner
        $('#filterBtnText').text('Loading…');
        $('#filterBtnLoader').css('display', 'inline-block');
        $('#btnApplyFilter').prop('disabled', true);

        $.ajax({
            url     : ROUTES.list,
            type    : 'GET',
            data    : params,
            dataType: 'json',
            success : function (res) {
                // Reset filter button
                $('#filterBtnText').text('Filter');
                $('#filterBtnLoader').hide();
                $('#btnApplyFilter').prop('disabled', false);

                if (res.status !== 'success') {
                    showPopup('Error', res.message ?? 'Failed to load grades.', 'error');
                    return;
                }
                allRows = res.data ?? [];
                applyClientFilters();
            },
            error: function (xhr) {
                $('#filterBtnText').text('Filter');
                $('#filterBtnLoader').hide();
                $('#btnApplyFilter').prop('disabled', false);
                const msg = xhr.responseJSON?.message ?? 'Network error while loading grades.';
                showPopup('Error', msg, 'error');
            }
        });
    }

    // Quarter + search are applied client-side after the server returns all rows
    function applyClientFilters() {
        let rows = [...allRows];

        const qtr = $filterQtr.val();
        if (qtr) rows = rows.filter(r => String(r.quarter) === qtr);

        const term = ($search.val() ?? '').toLowerCase().trim();
        if (term) rows = rows.filter(r => JSON.stringify(r).toLowerCase().includes(term));

        renderTable(rows);

        // Hide the full-screen loading modal once data is rendered
        if (window.loadingModal) window.loadingModal.hide();
    }

    // ── Filter / search events ────────────────────────────────────────────────
    $('#btnApplyFilter').on('click', loadGrades);

    $('#btnResetFilter').on('click', function () {
        $filterGL.val('');
        $filterSec.val('');
        $filterSubj.val('');
        $filterQtr.val('');
        $search.val('');
        loadGrades();
    });

    $search.on('input', applyClientFilters);
    $filterQtr.on('change', applyClientFilters);

    // Cascade section options in the filter bar when grade level changes
    $filterGL.on('change', function () {
        const gl = $(this).val();
        $filterSec.find('option[value!=""]').each(function () {
            $(this).prop('hidden', gl ? $(this).data('grade') != gl : false);
        });
        // Deselect if the current selection is now hidden
        if ($filterSec.find('option:selected').prop('hidden')) {
            $filterSec.val('');
        }
    });

    // ── CRUD helpers ──────────────────────────────────────────────────────────
    if (CAN_WRITE) {

        const $gradeModal  = $('#gradeModal');
        const $deleteModal = $('#deleteModal');

        function openModal()  { $gradeModal.addClass('open'); }
        function closeModal() {
            $gradeModal.removeClass('open');
            $('#gradeModal .is-invalid').removeClass('is-invalid');
        }

        // Cascading section dropdown in the modal.
        // keepStudent=true → do not reset the student dropdown (used by openEdit
        //   so the student value set later by loadStudents is not overwritten).
        function setSections(glId, selectedSecId, keepStudent) {
            $('#sectionLoadingDots').show();
            $('#modalSection').prop('disabled', true);

            const list = glId
                ? ALL_SECTIONS.filter(s => String(s.grade_level_id) === String(glId))
                : ALL_SECTIONS;

            setTimeout(function () {
                let opts = '<option value="">Select section…</option>';
                list.forEach(s => {
                    opts += `<option value="${s.id}"${String(s.id) === String(selectedSecId) ? ' selected' : ''}>${s.section_name}</option>`;
                });
                $('#modalSection').html(opts).prop('disabled', false);
                $('#sectionLoadingDots').hide();

                // Only reset student when NOT restoring an existing record
                if (!keepStudent) {
                    $('#modalStudent').html('<option value="">Select a section first…</option>');
                    $('#studentLoadingDots').hide();
                }

                if (selectedSecId) loadStudents(selectedSecId, null);
            }, 80);
        }

        // Load students for the chosen section — GET: /grades/students-by-section?section_id=
        function loadStudents(sectionId, selectedStudentId) {
            // Show loading dots next to the student label
            $('#studentLoadingDots').css('display', 'inline-block');
            $('#modalStudent').html('<option value="">Loading…</option>').prop('disabled', true);

            $.ajax({
                url     : ROUTES.studentsBySection,
                type    : 'GET',
                data    : { section_id: sectionId },
                dataType: 'json',
                success : function (res) {
                    $('#studentLoadingDots').hide();
                    $('#modalStudent').prop('disabled', false);
                    if (res.status !== 'success') {
                        $('#modalStudent').html('<option value="">No students found</option>');
                        return;
                    }
                    let opts = '<option value="">Select student…</option>';
                    (res.data ?? []).forEach(s => {
                        opts += `<option value="${s.id}"${String(s.id) === String(selectedStudentId) ? ' selected' : ''}>${s.student_name}</option>`;
                    });
                    $('#modalStudent').html(opts);
                },
                error: function () {
                    $('#studentLoadingDots').hide();
                    $('#modalStudent').prop('disabled', false).html('<option value="">Failed to load</option>');
                }
            });
        }

        // Grade level cascade inside modal
        $('#modalGradeLevel').on('change', function () {
            setSections($(this).val(), null);
        });

        // Section change → reload students
        $('#modalSection').on('change', function () {
            const secId = $(this).val();
            if (secId) loadStudents(secId, null);
            else $('#modalStudent').html('<option value="">Select student…</option>');
        });

        // Live grade preview bar + DepEd descriptor
        $('#modalGrade').on('input', function () {
            const g = parseFloat($(this).val());
            if (isNaN(g) || g < 60 || g > 100) {
                $('#gradeFill').css('width', '0%');
                $('#gradeHint').text('');
                return;
            }
            const pct = ((g - 60) / 40 * 100).toFixed(1);
            $('#gradeFill').css({ width: pct + '%', background: gradeColor(g) });
            $('#gradeHint').text('✦ ' + gradeDescriptor(g)).css('color', gradeColor(g));
        });

        // ── Open Add modal ────────────────────────────────────────────────────
        $('#btnAddGrade').on('click', function () {
            isEditing = false;
            $('#modalTitle').text('Add Grade');
            $('#btnSaveLabel').text('Save Grade');
            $('#gradeId').val('');
            $('#modalGradeLevel').val('');
            setSections(null, null);
            $('#modalSubject, #modalQuarter').val('');
            $('#modalGrade, #modalRemarks').val('');
            $('#gradeFill').css('width', '0%');
            $('#gradeHint').text('');
            openModal();
        });

        // ── Close modal ───────────────────────────────────────────────────────
        $('#btnModalClose, #btnCancelModal').on('click', closeModal);
        $gradeModal.on('click', function (e) {
            if ($(e.target).is($gradeModal)) closeModal();
        });

        // ── Save (store / update) — POST with JSON body ───────────────────────
        $('#btnSaveGrade').on('click', function () {
            $('#gradeModal .is-invalid').removeClass('is-invalid');

            let valid = true;
            ['#modalGradeLevel','#modalSection','#modalStudent',
             '#modalSubject','#modalQuarter','#modalGrade'].forEach(function (sel) {
                if (!$(sel).val()) { $(sel).addClass('is-invalid'); valid = false; }
            });

            const g = parseFloat($('#modalGrade').val());
            if ($('#modalGrade').val() && (isNaN(g) || g < 60 || g > 100)) {
                $('#modalGrade').addClass('is-invalid'); valid = false;
            }
            if (!valid) return;

            const payload = {
                student_id    : parseInt($('#modalStudent').val()),
                subject_id    : parseInt($('#modalSubject').val()),
                section_id    : parseInt($('#modalSection').val()),
                grade_level_id: parseInt($('#modalGradeLevel').val()),
                quarter       : parseInt($('#modalQuarter').val()),
                grade         : parseFloat($('#modalGrade').val()),
                remarks       : $('#modalRemarks').val().trim() || null,
            };

            const $btn      = $(this).prop('disabled', true);
            const editing   = isEditing;          // snapshot before async
            const url       = editing ? ROUTES.update : ROUTES.store;

            // For updates, id must be in the payload
            if (editing) payload.id = parseInt($('#gradeId').val());

            // Show loading state on button
            $('#btnSaveIcon').hide();
            $('#btnSaveLoader').css('display', 'inline-block');
            $('#btnSaveLabel').text(editing ? 'Updating…' : 'Saving…');

            $.ajax({
                url        : url,
                type       : 'POST',
                contentType: 'application/json',
                data       : JSON.stringify(payload),
                dataType   : 'json',
                success    : function (res) {
                    $('#btnSaveIcon').show();
                    $('#btnSaveLoader').hide();
                    $('#btnSaveLabel').text(editing ? 'Update Grade' : 'Save Grade');
                    $btn.prop('disabled', false);
                    if (res.status === 'success') {
                        closeModal();
                        if (window.loadingModal) window.loadingModal.show();
                        loadGrades();
                        showPopup('Success', res.message ?? (editing ? 'Grade updated.' : 'Grade saved.'), 'success');
                    } else {
                        showPopup('Error', res.message ?? 'Failed.', 'error');
                    }
                },
                error: function (xhr) {
                    $('#btnSaveIcon').show();
                    $('#btnSaveLoader').hide();
                    $('#btnSaveLabel').text(editing ? 'Update Grade' : 'Save Grade');
                    $btn.prop('disabled', false);
                    const msg = xhr.responseJSON?.message ?? 'Server error.';
                    showPopup('Error', msg, 'error');
                }
            });
        });

        // ── Delete (admin only) ───────────────────────────────────────────────
        if (CAN_DELETE) {
            $('#btnCancelDelete').on('click', function () {
                $deleteModal.removeClass('open');
                deleteId = null;
            });
            $deleteModal.on('click', function (e) {
                if ($(e.target).is($deleteModal)) {
                    $deleteModal.removeClass('open');
                    deleteId = null;
                }
            });

            $('#btnConfirmDelete').on('click', function () {
                if (!deleteId) return;
                const $btn = $(this).prop('disabled', true);

                // Show spinner on delete button
                $('#btnDeleteLoader').css('display', 'inline-block');
                $('#btnDeleteLabel').text('Deleting…');

                $.ajax({
                    url        : ROUTES.destroy,
                    type       : 'POST',
                    contentType: 'application/json',
                    data       : JSON.stringify({ id: deleteId }),
                    dataType   : 'json',
                    success    : function (res) {
                        // Reset delete button
                        $('#btnDeleteLoader').hide();
                        $('#btnDeleteLabel').text('Yes, Delete');
                        $btn.prop('disabled', false);
                        $deleteModal.removeClass('open');
                        deleteId = null;
                        if (res.status === 'success') {
                            // Show loading modal while table reloads
                            if (window.loadingModal) window.loadingModal.show();
                            loadGrades();
                            showPopup('Deleted', res.message ?? 'Record removed.', 'success');
                        } else {
                            showPopup('Error', res.message ?? 'Delete failed.', 'error');
                        }
                    },
                    error: function (xhr) {
                        $('#btnDeleteLoader').hide();
                        $('#btnDeleteLabel').text('Yes, Delete');
                        $btn.prop('disabled', false);
                        const msg = xhr.responseJSON?.message ?? 'Server error.';
                        showPopup('Error', msg, 'error');
                    }
                });
            });
        }

        // ── Exposed globally for inline onclick handlers in rendered rows ─────
        window.GradesView = {
            openEdit: function (r) {
                isEditing = true;
                $('#modalTitle').text('Edit Grade');
                $('#btnSaveLabel').text('Update Grade');
                $('#gradeId').val(r.id);

                // Populate non-cascading fields immediately
                $('#modalSubject').val(r.subject_id ?? '');
                $('#modalQuarter').val(r.quarter  ?? '');
                $('#modalGrade').val(r.grade     ?? '').trigger('input');
                $('#modalRemarks').val(r.remarks  ?? '');

                // Set grade level, then build sections with the pre-selected
                // section_id. Pass keepStudent=true so setSections does NOT
                // wipe the student dropdown before loadStudents can fill it.
                $('#modalGradeLevel').val(r.grade_level_id ?? '');
                setSections(r.grade_level_id, r.section_id, true);

                // loadStudents fires inside setSections' setTimeout (80ms).
                // We call it again at 160ms (after setSections has finished)
                // to set the correct pre-selected student.
                if (r.section_id) {
                    setTimeout(function () {
                        loadStudents(r.section_id, r.student_id);
                    }, 160);
                }

                openModal();
            },
            confirmDelete: function (id) {
                deleteId = id;
                $deleteModal.addClass('open');
            }
        };
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    loadGrades();

}); // end $(function)
</script>
@endpush