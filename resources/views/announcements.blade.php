@extends('layouts.app')

@section('title', 'Announcements — School Information System')

@section('page-title')
<h2>Announcements</h2>
@endsection

@push('styles')
<style>
/* ── Calendar toggle block ─────────────────────────────────────── */
.cal-toggle-block {
    margin-top: 4px;
    padding: 13px 15px;
    background: rgba(37,99,235,0.07);
    border: 1.5px solid rgba(37,99,235,0.2);
    border-radius: 10px;
    transition: border-color .2s;
}
.cal-toggle-block:has(input[type=checkbox]:checked) {
    border-color: rgba(37,99,235,0.4);
}
.cal-toggle-row {
    display: flex; align-items: center;
    justify-content: space-between; cursor: pointer; user-select: none;
}
.cal-toggle-info { display: flex; align-items: center; gap: 10px; }
.cal-toggle-label {
    font-size: 13px; font-weight: 600; color: #e2e8f0;
}
.cal-toggle-hint {
    font-size: 11px; color: #475569; margin-top: 1px;
}
.cal-date-wrap {
    display: none; margin-top: 12px;
    padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06);
}
.cal-date-wrap.visible { display: block; }
.cal-date-label {
    font-size: 11px; font-weight: 700; color: #60a5fa;
    text-transform: uppercase; letter-spacing: .04em;
    display: block; margin-bottom: 5px;
}
.cal-date-input {
    width: 100%; padding: 8px 12px;
    border: 1.5px solid rgba(37,99,235,0.3);
    border-radius: 8px; font-size: 13px;
    color: #e2e8f0; background: #0f172a;
    outline: none; box-sizing: border-box;
    font-family: inherit;
    transition: border-color .2s;
}
.cal-date-input:focus { border-color: rgba(96,165,250,0.5); }
.cal-date-hint {
    font-size: 11px; color: #334155; margin: 4px 0 0;
}
/* Calendar indicator badge in table */
.cal-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 700; padding: 1px 7px;
    border-radius: 20px; background: rgba(37,99,235,0.15); color: #60a5fa;
    border: 1px solid rgba(37,99,235,0.2); white-space: nowrap;
    margin-left: 5px; vertical-align: middle;
}
</style>
@endpush

@section('content')

@if(auth()->user()->role->name === 'Admin')

{{-- ════════════════════════════════════════════════════════════
     ADMIN ANNOUNCEMENTS
════════════════════════════════════════════════════════════ --}}

<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <button class="btn btn-primary" id="openPostModal">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        Post Announcement
    </button>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date Posted</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Subject</th>
                    <th>Grade Level</th>
                    <th>Sections</th>
                    <th>Posted By</th>
                </tr>
            </thead>
            <tbody id="announcementsTable">
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">
                        <span style="display:inline-flex; align-items:center; gap:6px; color:#475569; font-size:13px;">
                            Loading <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ADMIN: POST / EDIT MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:540px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 id="adminPostModalTitle" style="font-size:15px; font-weight:700; color:#e2e8f0;">Post Announcement</h3>
            <button onclick="adminClosePostModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>

        <input type="hidden" id="adminEditAnnouncementId">

        <div style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Title *</label>
                <input type="text" id="postTitle" class="form-input" placeholder="Announcement title">
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Description</label>
                <textarea id="postDescription" class="form-input" rows="3" placeholder="Announcement details..."></textarea>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Subject *</label>
                <select id="postSubject" class="form-select">
                    <option value="">Select subject</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Grade Level *</label>
                <select id="postGradeLevel" class="form-select">
                    <option value="">Select grade level</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Sections *</label>
                <div id="postSectionCheckboxes" style="background:#0f172a; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); display:flex; flex-wrap:wrap; gap:8px;">
                    <span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>
                </div>
            </div>

            {{-- ── Add to Calendar toggle ──────────────────────────────── --}}
            <div class="cal-toggle-block">
                <label class="cal-toggle-row">
                    <div class="cal-toggle-info">
                        <svg width="15" height="15" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                            <path d="M8 14h.01M12 14h.01"/>
                        </svg>
                        <div>
                            <div class="cal-toggle-label">Add to Calendar</div>
                            <div class="cal-toggle-hint">Pin this announcement on the school calendar</div>
                        </div>
                    </div>
                    <input type="checkbox" id="adminAddToCalendar" class="toggle toggle-sm"
                           onchange="adminToggleCalDate(this.checked)">
                </label>
                <div class="cal-date-wrap" id="adminCalDateWrap">
                    <label class="cal-date-label">
                        Calendar Date <span style="color:#f87171;">*</span>
                    </label>
                    <input type="date" id="adminCalendarDate" class="cal-date-input">
                    <p class="cal-date-hint">The date this will appear on the calendar.</p>
                </div>
            </div>
            {{-- ── End calendar toggle ─────────────────────────────────── --}}
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.06);">
            <button class="btn btn-outline" onclick="adminClosePostModal()">Cancel</button>
            <button class="btn btn-primary" onclick="adminSaveAnnouncement()" id="adminPostSubmitBtn">Post Announcement</button>
        </div>
    </div>
</div>

{{-- ADMIN: VIEW MODAL (with Edit & Delete) --}}
<div id="viewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 style="font-size:15px; font-weight:700; color:#e2e8f0;">Announcement Details</h3>
            <button onclick="closeViewModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Title</div>
                <div id="modalTitle" style="font-size:14px; font-weight:600; color:#e2e8f0;"></div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Description</div>
                <div id="modalDescription" style="font-size:13px; color:#94a3b8; line-height:1.6;"></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Subject</div>
                    <div id="modalSubject" style="font-size:13px; color:#94a3b8;"></div>
                </div>
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Sections</div>
                    <div id="modalSections" style="font-size:13px; color:#94a3b8;"></div>
                </div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Posted By</div>
                <div id="modalPostedBy" style="font-size:13px; color:#60a5fa; font-weight:600;"></div>
            </div>
            {{-- Calendar indicator --}}
            <div id="modalCalendarInfo" style="display:none; background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(37,99,235,0.25);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">📅 On Calendar</div>
                <div id="modalCalendarDate" style="font-size:13px; color:#60a5fa; font-weight:600;"></div>
            </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary" onclick="adminOpenEditModal(currentAnnouncement.id)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Edit
            </button>
            <button class="btn btn-danger" onclick="adminConfirmDelete(currentAnnouncement.id)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Delete
            </button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const viewModal = document.getElementById('viewModal');
const postModal = document.getElementById('postModal');
let currentAnnouncement = {}; // for view modal

// ── Calendar toggle (admin) ───────────────────────────────────────
function adminToggleCalDate(show) {
    const wrap = document.getElementById('adminCalDateWrap');
    wrap.classList.toggle('visible', show);
    if (!show) document.getElementById('adminCalendarDate').value = '';
}

// ── View Modal ────────────────────────────────────────────────────
function openViewModal(id, title, description, subject_name, section_names, posted_by, add_to_calendar, calendar_date) {
    currentAnnouncement = { id, title, description, subject_name, section_names, posted_by, add_to_calendar, calendar_date };
    document.getElementById('modalTitle').textContent       = title;
    document.getElementById('modalDescription').textContent = description || '—';
    document.getElementById('modalSubject').textContent     = subject_name;
    document.getElementById('modalSections').textContent    = section_names || '—';
    document.getElementById('modalPostedBy').textContent    = posted_by;

    const calInfo = document.getElementById('modalCalendarInfo');
    if (add_to_calendar && calendar_date) {
        document.getElementById('modalCalendarDate').textContent = calendar_date;
        calInfo.style.display = 'block';
    } else {
        calInfo.style.display = 'none';
    }
    viewModal.style.display = 'flex';
}
function closeViewModal() { viewModal.style.display = 'none'; }

// ── Post / Edit Modal ─────────────────────────────────────────────
function adminClosePostModal() {
    postModal.style.display = 'none';
    document.getElementById('adminEditAnnouncementId').value = '';
    document.getElementById('postTitle').value       = '';
    document.getElementById('postDescription').value = '';
    document.getElementById('postSubject').value     = '';
    document.getElementById('postGradeLevel').value  = '';
    document.getElementById('postSectionCheckboxes').innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
    document.getElementById('adminAddToCalendar').checked = false;
    adminToggleCalDate(false);
    document.getElementById('adminPostModalTitle').textContent = 'Post Announcement';
    document.getElementById('adminPostSubmitBtn').textContent = 'Post Announcement';
}

// ── Open edit modal ───────────────────────────────────────────────
function adminOpenEditModal(id) {
    // Close the view modal first (like teacher does)
    closeViewModal();

    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) loadingModal.style.display = 'flex';

    $.ajax({
        url: '/announcements/' + id,
        method: 'GET',
        success: function(response) {
            if (loadingModal) loadingModal.style.display = 'none';
            if (response.status === 'success') {
                const a = response.data;
                document.getElementById('adminEditAnnouncementId').value = a.id;
                document.getElementById('postTitle').value = a.title;
                document.getElementById('postDescription').value = a.description || '';
                document.getElementById('postSubject').value = a.subject_id;
                document.getElementById('adminAddToCalendar').checked = !!a.add_to_calendar;
                adminToggleCalDate(!!a.add_to_calendar);
                if (a.add_to_calendar && a.calendar_date) {
                    document.getElementById('adminCalendarDate').value = a.calendar_date;
                }

                // Set grade level and load sections
                if (a.grade_level_id) {
                    document.getElementById('postGradeLevel').value = a.grade_level_id;
                    loadSectionsForGrade(a.grade_level_id, a.section_ids);
                } else {
                    // Fallback: try to load sections without a grade (should not happen)
                    loadSectionsForGrade(null, a.section_ids);
                }

                document.getElementById('adminPostModalTitle').textContent = 'Edit Announcement';
                document.getElementById('adminPostSubmitBtn').textContent = 'Update Announcement';
                postModal.style.display = 'flex';
            } else {
                showPopup('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            if (loadingModal) loadingModal.style.display = 'none';
            showPopup('Error', xhr.responseJSON?.message || 'Failed to load announcement.', 'error');
        }
    });
}

// Helper to load sections and check the ones belonging to the announcement
function loadSectionsForGrade(gradeId, selectedSectionIds = []) {
    const wrap = document.getElementById('postSectionCheckboxes');
    wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Loading...</span>';
    if (!gradeId) {
        wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
        return;
    }
    $.get(`/fields/sections/${gradeId}`, function (response) {
        wrap.innerHTML = '';
        if (response.status === 'success' && response.data.length > 0) {
            response.data.forEach(sec => {
                const checked = selectedSectionIds.includes(sec.id) ? 'checked' : '';
                wrap.innerHTML += `
                    <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:#1e293b; border:1px solid rgba(255,255,255,0.07); padding:5px 10px; border-radius:6px; font-size:12px; color:#94a3b8;">
                        <input type="checkbox" value="${sec.id}" ${checked} style="accent-color:#2563eb;">
                        ${sec.section_name}
                    </label>`;
            });
        } else {
            wrap.innerHTML = '<span style="color:#334155; font-size:12px;">No sections found.</span>';
        }
    });
}

// ── Save (Create or Update) ───────────────────────────────────────
function adminSaveAnnouncement() {
    const editId = document.getElementById('adminEditAnnouncementId').value;
    const title  = document.getElementById('postTitle').value.trim();
    const desc   = document.getElementById('postDescription').value.trim();
    const subj   = document.getElementById('postSubject').value;
    const sections = [...document.querySelectorAll('#postSectionCheckboxes input:checked')].map(c => c.value);
    const addToCal = document.getElementById('adminAddToCalendar').checked;
    const calDate  = document.getElementById('adminCalendarDate').value;

    if (!title)           { showPopup('Validation', 'Title is required.', 'warning'); return; }
    if (!subj)            { showPopup('Validation', 'Please select a subject.', 'warning'); return; }
    if (!sections.length) { showPopup('Validation', 'Please select at least one section.', 'warning'); return; }
    if (addToCal && !calDate) { showPopup('Validation', 'Please select a calendar date.', 'warning'); return; }

    const data = {
        title,
        description     : desc,
        subject_id      : subj,
        sections,
        add_to_calendar : addToCal ? 1 : 0,
        calendar_date   : addToCal ? calDate : null,
    };
    if (editId) data.id = editId;

    adminClosePostModal();
    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) loadingModal.style.display = 'flex';

    $.ajax({
        url:    editId ? '{{ route("announcements.update") }}' : '{{ route("announcements.store") }}',
        method: 'POST',
        data:   data,
        success: function (response) {
            if (loadingModal) loadingModal.style.display = 'none';
            if (response.status === 'success') {
                showPopup('Success', response.message, 'success');
                loadAnnouncements();
            } else {
                showPopup('Error', response.message, 'error');
            }
        },
        error: function (xhr) {
            if (loadingModal) loadingModal.style.display = 'none';
            const msg = xhr.responseJSON?.message || 'An error occurred.';
            showPopup('Error', msg, 'error');
        }
    });
}

// ── Confirm Delete ─────────────────────────────────────────────────
// ── Confirm Delete ─────────────────────────────────────────────────
function adminConfirmDelete(id) {
    showConfirmationModal('Delete Announcement', 'Are you sure you want to delete this announcement?', function () {
        const loadingModal = document.getElementById('loading-modal');
        if (loadingModal) loadingModal.style.display = 'flex';

        $.ajax({
            url:    '{{ route("announcements.destroy") }}',
            method: 'POST',
            data:   { id: id },
            success: function (response) {
                if (loadingModal) loadingModal.style.display = 'none';
                closeViewModal();  // Close the view modal
                if (response.status === 'success') {
                    showPopup('Deleted', response.message, 'success');
                    loadAnnouncements();
                } else {
                    showPopup('Error', response.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingModal) loadingModal.style.display = 'none';
                closeViewModal();
                const msg = xhr.responseJSON?.message || 'Delete failed.';
                showPopup('Error', msg, 'error');
            }
        });
    });
}

// ── Load subjects & grades ────────────────────────────────────────
function loadSubjects() {
    $.get('{{ route("fields.subjects") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('postSubject');
            response.data.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id; opt.textContent = s.subject_name;
                sel.appendChild(opt);
            });
        }
    });
}

function loadGradeLevels() {
    $.get('{{ route("fields.gradeLevels") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('postGradeLevel');
            response.data.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id; opt.textContent = g.grade_level_name;
                sel.appendChild(opt);
            });
        }
    });
}

// When grade changes, load sections (for create)
document.getElementById('postGradeLevel').addEventListener('change', function () {
    const gradeId = this.value;
    const wrap    = document.getElementById('postSectionCheckboxes');
    wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Loading...</span>';
    if (!gradeId) {
        wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
        return;
    }
    $.get(`{{ url('fields/sections') }}/${gradeId}`, function (response) {
        wrap.innerHTML = '';
        if (response.status === 'success' && response.data.length > 0) {
            response.data.forEach(sec => {
                wrap.innerHTML += `
                    <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:#1e293b; border:1px solid rgba(255,255,255,0.07); padding:5px 10px; border-radius:6px; font-size:12px; color:#94a3b8;">
                        <input type="checkbox" value="${sec.id}" style="accent-color:#2563eb;">
                        ${sec.section_name}
                    </label>`;
            });
        } else {
            wrap.innerHTML = '<span style="color:#334155; font-size:12px;">No sections found.</span>';
        }
    });
});

// ── Load announcements ────────────────────────────────────────────
function loadAnnouncements() {
    $.ajax({
        url: '{{ route("announcements.all") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#announcementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    const e = s => (s||'').toString().replace(/`/g,"'").replace(/\\/g,'\\\\');
                    const calBadge = row.add_to_calendar
                        ? `<span class="cal-badge">📅 ${row.calendar_date || 'On Calendar'}</span>`
                        : '';
                    tbody.append(`
                        <tr>
                            <td class="cell-date">${row.date_posted}</td>
                            <td>
                                <button style="background:none; border:none; cursor:pointer; color:#60a5fa; font-weight:600; font-size:13px; text-align:left; padding:0; font-family:inherit;"
                                    onclick="openViewModal(${row.id},\`${e(row.title)}\`,\`${e(row.description)}\`,\`${e(row.subject_name)}\`,\`${e(row.section_names)}\`,\`${e(row.posted_by)}\`,${row.add_to_calendar ? 1 : 0},\`${e(row.calendar_date)}\`)">
                                    ${row.title}
                                </button>
                                ${calBadge}
                            </td>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${row.description || '—'}</td>
                            <td>${row.subject_name || '—'}</td>
                            <td>${row.grade_level_names || '—'}</td>
                            <td>${row.section_names || '—'}</td>
                            <td>${row.posted_by || '—'}</td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="7" style="text-align:center; padding:40px; color:#334155; font-size:13px;">No announcements found.</td></tr>');
            }
        },
        error: function () {
            $('#announcementsTable').html('<tr><td colspan="7" style="text-align:center; padding:40px; color:#f87171; font-size:13px;">Failed to load announcements.</td></tr>');
        }
    });
}

document.getElementById('openPostModal').addEventListener('click', function () {
    adminClosePostModal();
    postModal.style.display = 'flex';
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeViewModal(); adminClosePostModal(); }
});

$(document).ready(function () {
    loadAnnouncements();
    loadSubjects();
    loadGradeLevels();
});
</script>


@elseif(auth()->user()->role->name === 'Teacher')

{{-- ════════════════════════════════════════════════════════════
     TEACHER ANNOUNCEMENTS
════════════════════════════════════════════════════════════ --}}

<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <button class="btn btn-primary" id="teacherOpenPostModal">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        Post Announcement
    </button>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date Posted</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Subject</th>
                    <th>Grade Level</th>
                    <th>Sections</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="teacherAnnouncementsTable">
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">
                        <span style="display:inline-flex; align-items:center; gap:6px; color:#475569; font-size:13px;">
                            Loading <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TEACHER: VIEW / EDIT / DELETE MODAL --}}
<div id="teacherViewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 style="font-size:15px; font-weight:700; color:#e2e8f0;">Announcement Details</h3>
            <button onclick="teacherCloseViewModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Title</div>
                <div id="teacherModalTitle" style="font-size:14px; font-weight:600; color:#e2e8f0;"></div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Description</div>
                <div id="teacherModalDescription" style="font-size:13px; color:#94a3b8; line-height:1.6;"></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Subject</div>
                    <div id="teacherModalSubject" style="font-size:13px; color:#94a3b8;"></div>
                </div>
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Grade Level</div>
                    <div id="teacherModalGradeLevel" style="font-size:13px; color:#94a3b8;"></div>
                </div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Sections</div>
                <div id="teacherModalSections" style="font-size:13px; color:#94a3b8;"></div>
            </div>
            {{-- Calendar indicator --}}
            <div id="teacherModalCalendarInfo" style="display:none; background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(37,99,235,0.25);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">📅 On Calendar</div>
                <div id="teacherModalCalendarDate" style="font-size:13px; color:#60a5fa; font-weight:600;"></div>
            </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="teacherCloseViewModal()">Close</button>
            <button class="btn btn-primary" onclick="teacherOpenEditModal()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Edit
            </button>
            <button class="btn btn-danger" onclick="teacherConfirmDelete()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Delete
            </button>
        </div>
    </div>
</div>

{{-- TEACHER: POST / EDIT MODAL --}}
<div id="teacherPostModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:540px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 id="teacherPostModalTitle" style="font-size:15px; font-weight:700; color:#e2e8f0;">Post Announcement</h3>
            <button onclick="teacherClosePostModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>

        <input type="hidden" id="teacherEditAnnouncementId">

        <div style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Title *</label>
                <input type="text" id="teacherAnnouncementTitle" class="form-input" placeholder="Announcement title">
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Description</label>
                <textarea id="teacherAnnouncementDescription" class="form-input" rows="3" placeholder="Announcement details..."></textarea>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Subject *</label>
                <select id="teacherAnnouncementSubject" class="form-select">
                    <option value="">Select subject</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Grade Level *</label>
                <select id="teacherAnnouncementGradeLevel" class="form-select">
                    <option value="">Select grade level</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Sections *</label>
                <div id="teacherSectionCheckboxes" style="background:#0f172a; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); display:flex; flex-wrap:wrap; gap:8px; min-height:44px;">
                    <span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>
                </div>
            </div>

            {{-- ── Add to Calendar toggle ──────────────────────────────── --}}
            <div class="cal-toggle-block">
                <label class="cal-toggle-row">
                    <div class="cal-toggle-info">
                        <svg width="15" height="15" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                            <path d="M8 14h.01M12 14h.01"/>
                        </svg>
                        <div>
                            <div class="cal-toggle-label">Add to Calendar</div>
                            <div class="cal-toggle-hint">Students in selected sections will see this on their calendar</div>
                        </div>
                    </div>
                    <input type="checkbox" id="teacherAddToCalendar" class="toggle toggle-sm"
                           onchange="teacherToggleCalDate(this.checked)">
                </label>
                <div class="cal-date-wrap" id="teacherCalDateWrap">
                    <label class="cal-date-label">
                        Calendar Date <span style="color:#f87171;">*</span>
                    </label>
                    <input type="date" id="teacherCalendarDate" class="cal-date-input">
                    <p class="cal-date-hint">The date this will appear on the calendar.</p>
                </div>
            </div>
            {{-- ── End calendar toggle ─────────────────────────────────── --}}

        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.06);">
            <button class="btn btn-outline" onclick="teacherClosePostModal()">Cancel</button>
            <button class="btn btn-primary" id="teacherPostSubmitBtn" onclick="teacherSubmitAnnouncement()">Post Announcement</button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

let teacherCurrentAnnouncement = {};
const teacherViewModal = document.getElementById('teacherViewModal');
const teacherPostModal = document.getElementById('teacherPostModal');

// ── Calendar toggle (teacher) ─────────────────────────────────────
function teacherToggleCalDate(show) {
    const wrap = document.getElementById('teacherCalDateWrap');
    if (wrap) wrap.classList.toggle('visible', show);
    if (!show) document.getElementById('teacherCalendarDate').value = '';
}

// ── View ──────────────────────────────────────────────────────────
function teacherOpenViewModal(id, title, description, subject_name, grade_level_names, section_names, add_to_calendar, calendar_date, subject_id) {
    teacherCurrentAnnouncement = { id, title, description, subject_name, grade_level_names, section_names, add_to_calendar, calendar_date, subject_id };
    document.getElementById('teacherModalTitle').textContent       = title;
    document.getElementById('teacherModalDescription').textContent = description || '—';
    document.getElementById('teacherModalSubject').textContent     = subject_name;
    document.getElementById('teacherModalGradeLevel').textContent  = grade_level_names || '—';
    document.getElementById('teacherModalSections').textContent    = section_names || '—';

    const calInfo = document.getElementById('teacherModalCalendarInfo');
    if (add_to_calendar && calendar_date) {
        document.getElementById('teacherModalCalendarDate').textContent = calendar_date;
        calInfo.style.display = 'block';
    } else {
        calInfo.style.display = 'none';
    }

    teacherViewModal.style.display = 'flex';
}
function teacherCloseViewModal() { teacherViewModal.style.display = 'none'; }

// ── Post / Edit ───────────────────────────────────────────────────
function teacherClosePostModal() {
    teacherPostModal.style.display = 'none';
    document.getElementById('teacherEditAnnouncementId').value      = '';
    document.getElementById('teacherAnnouncementTitle').value       = '';
    document.getElementById('teacherAnnouncementDescription').value = '';
    document.getElementById('teacherAnnouncementSubject').value     = '';
    document.getElementById('teacherAnnouncementGradeLevel').value  = '';
    document.getElementById('teacherSectionCheckboxes').innerHTML   = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
    document.getElementById('teacherPostModalTitle').textContent    = 'Post Announcement';
    document.getElementById('teacherPostSubmitBtn').textContent     = 'Post Announcement';
    // Reset calendar fields
    document.getElementById('teacherAddToCalendar').checked = false;
    teacherToggleCalDate(false);
}

function teacherOpenEditModal() {
    teacherCloseViewModal();
    document.getElementById('teacherPostModalTitle').textContent    = 'Edit Announcement';
    document.getElementById('teacherPostSubmitBtn').textContent     = 'Update Announcement';
    document.getElementById('teacherEditAnnouncementId').value      = teacherCurrentAnnouncement.id;
    document.getElementById('teacherAnnouncementTitle').value       = teacherCurrentAnnouncement.title;
    document.getElementById('teacherAnnouncementDescription').value = teacherCurrentAnnouncement.description || '';

    // ── Restore subject dropdown ───────────────────────────────────────────────
    if (teacherCurrentAnnouncement.subject_id) {
        document.getElementById('teacherAnnouncementSubject').value = teacherCurrentAnnouncement.subject_id;
    }

    // ── Restore calendar state ─────────────────────────────────────────────────
    const addToCal = teacherCurrentAnnouncement.add_to_calendar;
    document.getElementById('teacherAddToCalendar').checked = !!addToCal;
    teacherToggleCalDate(!!addToCal);
    if (addToCal && teacherCurrentAnnouncement.calendar_date) {
        document.getElementById('teacherCalendarDate').value = teacherCurrentAnnouncement.calendar_date;
    }

    // ── Restore sections via AJAX ──────────────────────────────────────────────
    const wrap = document.getElementById('teacherSectionCheckboxes');
    wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Loading sections...</span>';

    $.get(`{{ url('announcements') }}/${teacherCurrentAnnouncement.id}/sections`, function (res) {
        if (res.status === 'success' && res.data.length) {
            const sectionIds = res.data.map(s => String(s.section_id));
            const gradeId    = res.data[0].grade_level_id ?? '';

            // Set grade level dropdown to the first section's grade
            document.getElementById('teacherAnnouncementGradeLevel').value = gradeId;

            // Load section checkboxes for that grade, then check the right ones
            if (gradeId) {
                $.get(`{{ url('fields/sections') }}/${gradeId}`, function (secRes) {
                    wrap.innerHTML = '';
                    if (secRes.status === 'success' && secRes.data.length) {
                        secRes.data.forEach(sec => {
                            const checked = sectionIds.includes(String(sec.id)) ? 'checked' : '';
                            wrap.innerHTML += `
                                <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:#1e293b; border:1px solid rgba(255,255,255,0.07); padding:5px 10px; border-radius:6px; font-size:12px; color:#94a3b8;">
                                    <input type="checkbox" value="${sec.id}" ${checked} style="accent-color:#2563eb;">
                                    ${sec.section_name}
                                </label>`;
                        });
                    } else {
                        wrap.innerHTML = '<span style="color:#334155; font-size:12px;">No sections found.</span>';
                    }
                });
            } else {
                wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
            }
        } else {
            wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Could not load sections.</span>';
        }
    }).fail(function () {
        wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Failed to load sections.</span>';
    });

    teacherPostModal.style.display = 'flex';
}

function teacherConfirmDelete() {
    showConfirmationModal('Delete Announcement', 'Are you sure you want to delete this announcement?', function () {
        teacherCloseViewModal();
        const loadingModal = document.getElementById('loading-modal');
        if (loadingModal) loadingModal.style.display = 'flex';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            $.ajax({
                url: '{{ route("announcements.destroy") }}', method: 'POST',
                data: { id: teacherCurrentAnnouncement.id },
                success: function (response) {
                    if (loadingModal) loadingModal.style.display = 'none';
                    setTimeout(() => {
                        if (response.status === 'success') { showPopup('Deleted', response.message, 'success'); teacherLoadAnnouncements(); }
                        else                               { showPopup('Error', response.message, 'error'); }
                    }, 100);
                },
                error: function (xhr) {
                    if (loadingModal) loadingModal.style.display = 'none';
                    const msg = xhr.responseJSON?.message || 'An error occurred.';
                    setTimeout(() => showPopup('Error', msg, 'error'), 100);
                }
            });
        }));
    });
}

function teacherSubmitAnnouncement() {
    const editId   = document.getElementById('teacherEditAnnouncementId').value;
    const title    = document.getElementById('teacherAnnouncementTitle').value.trim();
    const desc     = document.getElementById('teacherAnnouncementDescription').value.trim();
    const subj     = document.getElementById('teacherAnnouncementSubject').value;
    const sections = [...document.querySelectorAll('#teacherSectionCheckboxes input:checked')].map(c => c.value);
    const addToCal = document.getElementById('teacherAddToCalendar').checked;
    const calDate  = document.getElementById('teacherCalendarDate').value;

    if (!title)           { showPopup('Validation', 'Title is required.', 'warning'); return; }
    if (!subj)            { showPopup('Validation', 'Please select a subject.', 'warning'); return; }
    if (!sections.length) { showPopup('Validation', 'Please select at least one section.', 'warning'); return; }
    if (addToCal && !calDate) { showPopup('Validation', 'Please select a calendar date.', 'warning'); return; }

    const data = {
        title,
        description     : desc,
        subject_id      : subj,
        sections,
        add_to_calendar : addToCal ? 1 : 0,
        calendar_date   : addToCal ? calDate : null,
    };
    if (editId) data.id = editId;

    teacherClosePostModal();
    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) loadingModal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        $.ajax({
            url:    editId ? '{{ route("announcements.update") }}' : '{{ route("announcements.store") }}',
            method: 'POST', data,
            success: function (response) {
                if (loadingModal) loadingModal.style.display = 'none';
                setTimeout(() => {
                    if (response.status === 'success') { showPopup('Success', response.message, 'success'); teacherLoadAnnouncements(); }
                    else                               { showPopup('Error', response.message, 'error'); }
                }, 100);
            },
            error: function (xhr) {
                if (loadingModal) loadingModal.style.display = 'none';
                const msg = xhr.responseJSON?.message || 'An error occurred.';
                setTimeout(() => showPopup('Error', msg, 'error'), 100);
            }
        });
    }));
}

// ── Grade → Sections ──────────────────────────────────────────────
document.getElementById('teacherAnnouncementGradeLevel').addEventListener('change', function () {
    const gradeId = this.value;
    const wrap    = document.getElementById('teacherSectionCheckboxes');
    wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Loading...</span>';
    if (!gradeId) { wrap.innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>'; return; }
    $.get(`{{ url('fields/sections') }}/${gradeId}`, function (response) {
        wrap.innerHTML = '';
        if (response.status === 'success' && response.data.length > 0) {
            response.data.forEach(sec => {
                wrap.innerHTML += `
                    <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:#1e293b; border:1px solid rgba(255,255,255,0.07); padding:5px 10px; border-radius:6px; font-size:12px; color:#94a3b8;">
                        <input type="checkbox" value="${sec.id}" style="accent-color:#2563eb;">
                        ${sec.section_name}
                    </label>`;
            });
        } else {
            wrap.innerHTML = '<span style="color:#334155; font-size:12px;">No sections found.</span>';
        }
    });
});

// ── Load helpers ──────────────────────────────────────────────────
function teacherLoadSubjectsAndGrades() {
    $.get('{{ route("fields.subjects") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('teacherAnnouncementSubject');
            response.data.forEach(s => {
                const o = document.createElement('option'); o.value = s.id; o.textContent = s.subject_name; sel.appendChild(o);
            });
        }
    });
    $.get('{{ route("fields.gradeLevels") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('teacherAnnouncementGradeLevel');
            response.data.forEach(g => {
                const o = document.createElement('option'); o.value = g.id; o.textContent = g.grade_level_name; sel.appendChild(o);
            });
        }
    });
}

// ── Load announcements ────────────────────────────────────────────
function teacherLoadAnnouncements() {
    $.ajax({
        url: '{{ route("announcements.list") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#teacherAnnouncementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    const esc = s => (s||'').toString().replace(/`/g,"'").replace(/\\/g,'\\\\');
                    const calBadge = row.add_to_calendar
                        ? `<span class="cal-badge">📅 ${row.calendar_date || 'On Calendar'}</span>`
                        : '';
                    tbody.append(`
                        <tr>
                            <td class="cell-date">${row.date_posted}</td>
                            <td>
                                <button style="background:none; border:none; cursor:pointer; color:#60a5fa; font-weight:600; font-size:13px; padding:0; font-family:inherit; text-align:left;"
                                    onclick="teacherOpenViewModal(${row.id},\`${esc(row.title)}\`,\`${esc(row.description)}\`,\`${esc(row.subject_name)}\`,\`${esc(row.grade_level_names)}\`,\`${esc(row.section_names)}\`,${row.add_to_calendar ? 1 : 0},\`${esc(row.calendar_date)}\`,${row.subject_id || 0})">
                                    ${row.title}
                                </button>
                                ${calBadge}
                            </td>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#64748b;">${row.description || '—'}</td>
                            <td>${row.subject_name || '—'}</td>
                            <td>${row.grade_level_names || '—'}</td>
                            <td>${row.section_names || '—'}</td>
                            <td>
                                <button class="btn btn-outline" style="padding:3px 10px; font-size:11px;"
                                    onclick="teacherOpenViewModal(${row.id},\`${esc(row.title)}\`,\`${esc(row.description)}\`,\`${esc(row.subject_name)}\`,\`${esc(row.grade_level_names)}\`,\`${esc(row.section_names)}\`,${row.add_to_calendar ? 1 : 0},\`${esc(row.calendar_date)}\`,${row.subject_id || 0})">
                                    View
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="7" style="text-align:center; padding:40px; color:#334155; font-size:13px;">No announcements found.</td></tr>');
            }
        },
        error: function (xhr) {
            $('#teacherAnnouncementsTable').html('<tr><td colspan="7" style="text-align:center; padding:40px; color:#f87171; font-size:13px;">Failed to load announcements.</td></tr>');
        }
    });
}

document.getElementById('teacherOpenPostModal').addEventListener('click', function () { 
    teacherClosePostModal(); 
    teacherPostModal.style.display = 'flex'; 
});

document.addEventListener('keydown', function (e) { 
    if (e.key === 'Escape') { 
        teacherCloseViewModal(); 
        teacherClosePostModal(); 
    } 
});

$(document).ready(function () { 
    teacherLoadAnnouncements(); 
    teacherLoadSubjectsAndGrades(); 
});
</script>

@elseif(auth()->user()->role->name === 'Student')

{{-- ════════════════════════════════════════════════════════════
     STUDENT ANNOUNCEMENTS
════════════════════════════════════════════════════════════ --}}

<div class="card">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date Posted</th>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Sections</th>
                </tr>
            </thead>
            <tbody id="studentAnnouncementsTable">
                <tr>
                    <td colspan="4" style="text-align:center; padding:40px;">
                        <span style="display:inline-flex; align-items:center; gap:6px; color:#475569; font-size:13px;">
                            Loading <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- STUDENT: VIEW MODAL --}}
<div id="studentViewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 style="font-size:15px; font-weight:700; color:#e2e8f0;">Announcement Details</h3>
            <button onclick="studentCloseViewModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Title</div>
                <div id="studentModalTitle" style="font-size:14px; font-weight:600; color:#e2e8f0;"></div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:14px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Description</div>
                <div id="studentModalDescription" style="font-size:13px; color:#94a3b8; line-height:1.6;"></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Subject</div>
                    <div id="studentModalSubject" style="font-size:13px; color:#94a3b8;"></div>
                </div>
                <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Sections</div>
                    <div id="studentModalSections" style="font-size:13px; color:#94a3b8;"></div>
                </div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Posted By</div>
                <div id="studentModalPostedBy" style="font-size:13px; color:#60a5fa; font-weight:600;"></div>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="studentCloseViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const studentViewModal = document.getElementById('studentViewModal');

function studentOpenViewModal(id, title, description, subject_name, section_names, posted_by) {
    document.getElementById('studentModalTitle').textContent       = title;
    document.getElementById('studentModalDescription').textContent = description || '—';
    document.getElementById('studentModalSubject').textContent     = subject_name;
    document.getElementById('studentModalSections').textContent    = section_names || '—';
    document.getElementById('studentModalPostedBy').textContent    = posted_by || '—';
    studentViewModal.style.display = 'flex';
}
function studentCloseViewModal() { studentViewModal.style.display = 'none'; }

function studentLoadAnnouncements() {
    $.ajax({
        url: '{{ route("student.announcements") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#studentAnnouncementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    const esc = s => (s||'').toString().replace(/`/g,"'").replace(/\\/g,'\\\\');
                    tbody.append(`
                        <tr style="cursor:pointer;" onclick="studentOpenViewModal(${row.id},\`${esc(row.title)}\`,\`${esc(row.description)}\`,\`${esc(row.subject_name)}\`,\`${esc(row.section_names)}\`,\`${esc(row.posted_by)}\`)">
                            <td class="cell-date">${row.date_posted}</td>
                            <td style="font-weight:600; color:#e2e8f0;">${row.title}</td>
                            <td>${row.subject_name || '—'}</td>
                            <td>${row.section_names || '—'}</td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="4" style="text-align:center; padding:40px; color:#334155; font-size:13px;">No announcements found.</td></tr>');
            }
        },
        error: function (xhr) {
            console.error('Error loading student announcements:', xhr);
            $('#studentAnnouncementsTable').html('<tr><td colspan="4" style="text-align:center; padding:40px; color:#f87171; font-size:13px;">Failed to load announcements.</td></tr>');
        }
    });
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') studentCloseViewModal(); });
$(document).ready(function () { studentLoadAnnouncements(); });
</script>

@else

<div class="empty-state">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
    </svg>
    <h3>No access</h3>
    <p>Announcements are not available for your role.</p>
</div>

@endif

@endsection