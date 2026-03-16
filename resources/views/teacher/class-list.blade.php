{{-- resources/views/teacher/class-list.blade.php --}}
@extends('layouts.app')

@section('title', 'My Classes — School Information System')

@section('page-title')
<h2>My Classes</h2>
@endsection

@push('styles')
<style>
    /* ── Page header ──────────────────────────────────────────────── */
    .cl-header {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .cl-header h2 {
        font-family: var(--font-display); font-size: 1.25rem;
        font-weight: 700; color: var(--dk-t1); margin: 0 0 4px;
    }
    .cl-header p { margin: 0; font-size: 0.82rem; color: var(--dk-t3); }

    /* ── Filter bar ───────────────────────────────────────────────── */
    .cl-filter-bar {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg); padding: 14px 20px;
        display: flex; flex-wrap: wrap; gap: 12px;
        align-items: flex-end; margin-bottom: 22px;
    }
    .cl-filter-group { display: flex; flex-direction: column; gap: 4px; min-width: 160px; flex: 1; }
    .cl-filter-group label {
        font-size: 0.72rem; font-weight: 700; color: var(--dk-t4);
        text-transform: uppercase; letter-spacing: .04em;
    }
    .cl-filter-bar select {
        padding: 8px 28px 8px 12px;
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-size: 0.84rem; color: var(--dk-t2);
        background: var(--dk-surface2); font-family: var(--font-body);
        outline: none; transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center;
    }
    .cl-filter-bar select:focus { border-color: rgba(96,165,250,0.4); }
    .cl-filter-bar select option { background: #111827; color: #cbd5e1; }

    /* ── Subject cards grid ───────────────────────────────────────── */
    .subject-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }

    .subject-card {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg); overflow: hidden;
        transition: border-color .2s, transform .2s;
    }
    .subject-card:hover { border-color: rgba(255,255,255,0.12); transform: translateY(-2px); }

    .subject-card-header {
        padding: 16px 18px 12px;
        border-bottom: 1px solid var(--dk-b2);
        display: flex; align-items: center; gap: 12px;
    }
    .subject-icon {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        background: rgba(37,99,235,0.15); color: #60a5fa;
        display: flex; align-items: center; justify-content: center;
    }
    .subject-name {
        font-family: var(--font-display); font-size: 0.95rem;
        font-weight: 700; color: var(--dk-t1); margin: 0 0 2px;
    }
    .subject-meta { font-size: 0.75rem; color: var(--dk-t4); }

    /* Section rows inside card */
    .section-list { padding: 8px 0; }
    .section-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 18px; transition: background .12s; gap: 12px;
    }
    .section-row:hover { background: rgba(255,255,255,0.03); }
    .section-row + .section-row { border-top: 1px solid var(--dk-b2); }

    .section-info { display: flex; flex-direction: column; gap: 2px; }
    .section-name-text {
        font-size: 0.84rem; font-weight: 600; color: var(--dk-t1);
    }
    .section-schedule {
        font-size: 0.74rem; color: var(--dk-t4);
        display: flex; flex-wrap: wrap; gap: 6px;
    }
    .schedule-chip {
        background: rgba(255,255,255,0.05); border: 1px solid var(--dk-b2);
        border-radius: 5px; padding: 1px 7px;
        font-size: 0.7rem; color: var(--dk-t3);
    }

    .section-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .student-count-badge {
        background: rgba(37,99,235,0.12); color: #60a5fa;
        border: 1px solid rgba(37,99,235,0.2);
        border-radius: 20px; padding: 2px 10px;
        font-size: 0.75rem; font-weight: 700; white-space: nowrap;
    }
    .btn-view-students {
        padding: 5px 12px;
        background: rgba(255,255,255,0.06); color: var(--dk-t2);
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.78rem; font-weight: 600;
        cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .btn-view-students:hover {
        background: var(--blue-600); color: #fff;
        border-color: var(--blue-600);
    }

    /* ── Empty / loading states ───────────────────────────────────── */
    .cl-empty {
        text-align: center; padding: 60px 20px;
        color: var(--dk-t4); background: var(--dk-surface);
        border: 1px solid var(--dk-b1); border-radius: var(--radius-lg);
    }
    .cl-empty svg { margin-bottom: 12px; opacity: .3; }
    .cl-empty p { font-size: 0.88rem; margin: 0; }
    .cl-loading {
        display: flex; align-items: center; justify-content: center;
        gap: 10px; padding: 60px 20px;
        color: var(--dk-t4); font-size: 13px;
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-lg);
    }

    /* ── Student modal ────────────────────────────────────────────── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.65);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; backdrop-filter: blur(3px);
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl); width: 100%; max-width: 560px;
        max-height: 85vh; display: flex; flex-direction: column;
        box-shadow: var(--shadow-xl); animation: modalIn .22s ease-out;
    }
    @keyframes modalIn {
        from { opacity:0; transform:translateY(20px) scale(.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px 14px; border-bottom: 1px solid var(--dk-b2);
        flex-shrink: 0;
    }
    .modal-header-left { display: flex; flex-direction: column; gap: 2px; }
    .modal-header h3 {
        font-family: var(--font-display); font-size: 1rem;
        font-weight: 700; color: var(--dk-t1); margin: 0;
    }
    .modal-header-sub { font-size: 0.75rem; color: var(--dk-t4); }
    .modal-close {
        width: 30px; height: 30px; border: none;
        background: rgba(255,255,255,0.06); border-radius: 8px;
        cursor: pointer; font-size: 1rem; color: var(--dk-t3);
        display: flex; align-items: center; justify-content: center;
        transition: all .18s; flex-shrink: 0;
    }
    .modal-close:hover { background: rgba(255,255,255,0.12); color: var(--dk-t1); }

    /* Student search inside modal */
    .modal-search-wrap {
        padding: 12px 22px; border-bottom: 1px solid var(--dk-b2); flex-shrink: 0;
    }
    .modal-search {
        width: 100%; padding: 7px 12px 7px 34px;
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem; color: var(--dk-t2);
        background: var(--dk-surface2) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center;
        outline: none; box-sizing: border-box;
        transition: border-color .2s;
    }
    .modal-search::placeholder { color: var(--dk-t4); }
    .modal-search:focus { border-color: rgba(96,165,250,0.4); }

    /* Student list */
    .modal-body { overflow-y: auto; flex: 1; }
    .student-list-item {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 22px; border-bottom: 1px solid var(--dk-b2);
        transition: background .12s;
    }
    .student-list-item:last-child { border-bottom: none; }
    .student-list-item:hover { background: rgba(255,255,255,0.03); }
    .student-avatar {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--blue-700), var(--blue-500));
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff;
        font-family: var(--font-display);
    }
    .student-details { flex: 1; min-width: 0; }
    .student-full-name {
        font-size: 0.85rem; font-weight: 600; color: var(--dk-t1);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .student-meta { font-size: 0.74rem; color: var(--dk-t4); }
    .student-lrn {
        font-size: 0.72rem; color: var(--dk-t4);
        background: rgba(255,255,255,0.05); border: 1px solid var(--dk-b2);
        border-radius: 5px; padding: 1px 7px; white-space: nowrap; flex-shrink: 0;
    }

    .modal-footer {
        padding: 12px 22px; border-top: 1px solid var(--dk-b2);
        display: flex; justify-content: space-between; align-items: center;
        flex-shrink: 0;
    }
    .modal-footer-count { font-size: 0.78rem; color: var(--dk-t4); }
    .btn-close-modal {
        padding: 7px 18px;
        background: rgba(255,255,255,0.05); color: var(--dk-t3);
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem; font-weight: 600;
        cursor: pointer; transition: all .18s;
    }
    .btn-close-modal:hover { background: rgba(255,255,255,0.09); color: var(--dk-t1); }

    /* Sex badge */
    .sex-badge {
        font-size: 0.68rem; font-weight: 700; padding: 1px 7px;
        border-radius: 20px; flex-shrink: 0;
    }
    .sex-badge.male   { background: rgba(37,99,235,0.14); color: #60a5fa; }
    .sex-badge.female { background: rgba(236,72,153,0.14); color: #f472b6; }

    @media (max-width: 600px) {
        .subject-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────────────────── --}}
<div class="cl-header">
    <div>
        <h2>My Classes</h2>
        <p>Subjects and sections you currently handle, with enrolled students.</p>
    </div>
</div>

{{-- ── Filters ───────────────────────────────────────────────────────────────── --}}
<div class="cl-filter-bar">
    <div class="cl-filter-group">
        <label>Subject</label>
        <select id="filterSubject">
            <option value="">All Subjects</option>
        </select>
    </div>
    <div class="cl-filter-group">
        <label>Day</label>
        <select id="filterDay">
            <option value="">All Days</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
        </select>
    </div>
    <div class="cl-filter-group">
        <label>Section</label>
        <select id="filterSection">
            <option value="">All Sections</option>
        </select>
    </div>
</div>

{{-- ── Subject cards ─────────────────────────────────────────────────────────── --}}
<div id="classListContainer">
    <div class="cl-loading">
        Loading your classes
        <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
    </div>
</div>

{{-- ── Student list modal ────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="studentModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-left">
                <h3 id="modalSectionTitle">Students</h3>
                <span class="modal-header-sub" id="modalSectionSub"></span>
            </div>
            <button class="modal-close" id="btnCloseModal">✕</button>
        </div>
        <div class="modal-search-wrap">
            <input type="text" class="modal-search" id="studentSearch" placeholder="Search student name or LRN…">
        </div>
        <div class="modal-body" id="studentListBody">
            <div style="text-align:center; padding:40px; color:var(--dk-t4); font-size:13px; display:flex; align-items:center; justify-content:center; gap:8px;">
                Loading students
                <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
            </div>
        </div>
        <div class="modal-footer">
            <span class="modal-footer-count" id="modalStudentCount"></span>
            <button class="btn-close-modal" id="btnCloseModalFooter">Close</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const ROUTES = {
        schedule: '{{ route("teacher.class-list.schedule") }}',
        students : '{{ route("teacher.class-list.students") }}',
    };

    // ── State ─────────────────────────────────────────────────────────────────
    let allSchedule   = [];   // raw rows from SP MODE 13
    let allStudents   = [];   // raw rows from SP MODE 14 (current modal)

    // ── Load teacher's schedule ───────────────────────────────────────────────
    function loadSchedule() {
        $.ajax({
            url: ROUTES.schedule, type: 'GET', dataType: 'json',
            success: function (res) {
                if (res.status !== 'success') {
                    showPopup('Error', res.message ?? 'Failed to load classes.', 'error');
                    return;
                }
                allSchedule = res.data ?? [];
                populateFilters(allSchedule);
                renderCards(allSchedule);
            },
            error: function (xhr) {
                $('#classListContainer').html(`
                    <div class="cl-empty">
                        <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                        </svg>
                        <p>Failed to load classes. Please refresh the page.</p>
                    </div>`);
                showPopup('Error', xhr.responseJSON?.message ?? 'Network error.', 'error');
            }
        });
    }

    // ── Populate filter dropdowns from live data ──────────────────────────────
    function populateFilters(rows) {
        const subjects = [...new Map(rows.map(r => [r.subject_id, r.subject_name])).entries()];
        const sections = [...new Map(rows.map(r => [r.section_id, r.section_name])).entries()];

        subjects.forEach(([id, name]) => {
            $('#filterSubject').append(`<option value="${id}">${name}</option>`);
        });
        sections.forEach(([id, name]) => {
            $('#filterSection').append(`<option value="${id}">${name}</option>`);
        });
    }

    // ── Apply filters client-side ─────────────────────────────────────────────
    function applyFilters() {
        const subjectId = $('#filterSubject').val();
        const day       = $('#filterDay').val();
        const sectionId = $('#filterSection').val();

        let filtered = [...allSchedule];
        if (subjectId) filtered = filtered.filter(r => String(r.subject_id) === subjectId);
        if (day)       filtered = filtered.filter(r => r.day === day);
        if (sectionId) filtered = filtered.filter(r => String(r.section_id) === sectionId);

        renderCards(filtered);
    }

    $('#filterSubject, #filterDay, #filterSection').on('change', applyFilters);

    // ── Render subject cards ──────────────────────────────────────────────────
    // Group rows by subject_id, then list each section inside
    function renderCards(rows) {
        if (!rows.length) {
            $('#classListContainer').html(`
                <div class="cl-empty">
                    <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        <path d="M9 7h6M9 11h4"/>
                    </svg>
                    <p>No classes match the selected filters.</p>
                </div>`);
            return;
        }

        // Group by subject
        const bySubject = {};
        rows.forEach(r => {
            if (!bySubject[r.subject_id]) {
                bySubject[r.subject_id] = { name: r.subject_name, sections: {} };
            }
            const key = r.section_id;
            if (!bySubject[r.subject_id].sections[key]) {
                bySubject[r.subject_id].sections[key] = {
                    section_id        : r.section_id,
                    section_name      : r.section_name,
                    grade_level_name  : r.grade_level_name,
                    student_count     : r.student_count,
                    schedules         : [],
                };
            }
            bySubject[r.subject_id].sections[key].schedules.push({
                day       : r.day,
                time_start: r.time_start,
                time_end  : r.time_end,
                room      : r.room,
            });
        });

        const cards = Object.entries(bySubject).map(([subjectId, subj]) => {
            const sections = Object.values(subj.sections);
            const totalStudents = sections.reduce((s, sec) => s + parseInt(sec.student_count ?? 0), 0);

            const sectionRows = sections.map(sec => {
                const chips = sec.schedules.map(sch =>
                    `<span class="schedule-chip">${sch.day} ${sch.time_start}–${sch.time_end}${sch.room ? ' · ' + sch.room : ''}</span>`
                ).join('');

                return `
                <div class="section-row">
                    <div class="section-info">
                        <span class="section-name-text">${sec.grade_level_name} — ${sec.section_name}</span>
                        <div class="section-schedule">${chips}</div>
                    </div>
                    <div class="section-right">
                        <span class="student-count-badge">${sec.student_count} students</span>
                        <button class="btn-view-students"
                            onclick="ClassList.openStudents(${sec.section_id}, '${sec.section_name.replace(/'/g,"\\'")}', '${subj.name.replace(/'/g,"\\'")}', '${sec.grade_level_name.replace(/'/g,"\\'")}')">
                            View Students
                        </button>
                    </div>
                </div>`;
            }).join('');

            return `
            <div class="subject-card">
                <div class="subject-card-header">
                    <div class="subject-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            <path d="M9 7h6M9 11h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="subject-name">${subj.name}</div>
                        <div class="subject-meta">${sections.length} section${sections.length !== 1 ? 's' : ''} · ${totalStudents} total students</div>
                    </div>
                </div>
                <div class="section-list">${sectionRows}</div>
            </div>`;
        }).join('');

        $('#classListContainer').html(`<div class="subject-grid">${cards}</div>`);
    }

    // ── Student modal ─────────────────────────────────────────────────────────
    window.ClassList = {
        openStudents: function (sectionId, sectionName, subjectName, gradeLevelName) {
            // Open modal with loading state
            $('#modalSectionTitle').text(`${gradeLevelName} — ${sectionName}`);
            $('#modalSectionSub').text(subjectName);
            $('#studentListBody').html(`
                <div style="text-align:center; padding:40px; color:var(--dk-t4); font-size:13px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    Loading students
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                </div>`);
            $('#studentSearch').val('');
            $('#modalStudentCount').text('');
            $('#studentModal').addClass('open');

            $.ajax({
                url     : ROUTES.students,
                type    : 'GET',
                data    : { section_id: sectionId },
                dataType: 'json',
                success : function (res) {
                    if (res.status !== 'success') {
                        renderStudentList([]);
                        showPopup('Error', res.message ?? 'Failed to load students.', 'error');
                        return;
                    }
                    allStudents = res.data ?? [];
                    renderStudentList(allStudents);
                },
                error: function () {
                    $('#studentListBody').html(`
                        <div style="text-align:center; padding:40px; color:var(--red-400); font-size:13px;">
                            Failed to load students.
                        </div>`);
                }
            });
        }
    };

    function renderStudentList(students) {
        if (!students.length) {
            $('#studentListBody').html(`
                <div style="text-align:center; padding:40px; color:var(--dk-t4); font-size:13px;">
                    No students enrolled in this section.
                </div>`);
            $('#modalStudentCount').text('0 students');
            return;
        }

        const html = students.map((s, i) => {
            const initials = (s.fname ? s.fname[0] : '') + (s.lname ? s.lname[0] : '');
            const sexClass = (s.sex || '').toLowerCase() === 'female' ? 'female' : 'male';
            const lrn = s.lrn ? `<span class="student-lrn">LRN: ${s.lrn}</span>` : '';
            return `
            <div class="student-list-item">
                <div class="student-avatar">${initials.toUpperCase()}</div>
                <div class="student-details">
                    <div class="student-full-name">${s.student_name ?? `${s.fname} ${s.lname}`}</div>
                    <div class="student-meta">${s.grade_level_name} · ${s.section_name}</div>
                </div>
                <span class="sex-badge ${sexClass}">${s.sex ?? '—'}</span>
                ${lrn}
            </div>`;
        }).join('');

        $('#studentListBody').html(html);
        $('#modalStudentCount').text(`${students.length} student${students.length !== 1 ? 's' : ''}`);
    }

    // Live search inside modal
    $('#studentSearch').on('input', function () {
        const term = $(this).val().toLowerCase().trim();
        if (!term) { renderStudentList(allStudents); return; }
        renderStudentList(allStudents.filter(s =>
            (s.student_name ?? `${s.fname} ${s.lname}`).toLowerCase().includes(term) ||
            (s.lrn ?? '').toLowerCase().includes(term)
        ));
    });

    // Close modal
    $('#btnCloseModal, #btnCloseModalFooter').on('click', function () {
        $('#studentModal').removeClass('open');
    });
    $('#studentModal').on('click', function (e) {
        if ($(e.target).is($('#studentModal'))) $('#studentModal').removeClass('open');
    });

    // ── Init ──────────────────────────────────────────────────────────────────
    loadSchedule();
});
</script>
@endpush