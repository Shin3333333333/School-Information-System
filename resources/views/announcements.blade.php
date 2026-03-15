@extends('layouts.app')

@section('title', 'Announcements — School Information System')

@section('page-title')
<h2>Announcements</h2>
@endsection

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

{{-- ADMIN: POST MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:540px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 style="font-size:15px; font-weight:700; color:#e2e8f0;">Post Announcement</h3>
            <button onclick="closePostModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <div style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Title</label>
                <input type="text" id="postTitle" class="form-input" placeholder="Announcement title">
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Description</label>
                <textarea id="postDescription" class="form-input" rows="3" placeholder="Announcement details..."></textarea>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Subject</label>
                <select id="postSubject" class="form-select">
                    <option value="">Select subject</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Grade Level</label>
                <select id="postGradeLevel" class="form-select">
                    <option value="">All grade levels</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Sections</label>
                <div id="postSectionCheckboxes" style="background:#0f172a; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); display:flex; flex-wrap:wrap; gap:8px;">
                    <span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>
                </div>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.06);">
            <button class="btn btn-outline" onclick="closePostModal()">Cancel</button>
            <button class="btn btn-primary" onclick="adminSubmitAnnouncement()">Post Announcement</button>
        </div>
    </div>
</div>

{{-- ADMIN: VIEW MODAL --}}
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
        </div>
        <div style="display:flex; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const viewModal = document.getElementById('viewModal');
const postModal = document.getElementById('postModal');

// ── View Modal ────────────────────────────────────────────────────
function openViewModal(id, title, description, subject_name, section_names, posted_by) {
    document.getElementById('modalTitle').textContent       = title;
    document.getElementById('modalDescription').textContent = description || '—';
    document.getElementById('modalSubject').textContent     = subject_name;
    document.getElementById('modalSections').textContent    = section_names || '—';
    document.getElementById('modalPostedBy').textContent    = posted_by;
    viewModal.style.display = 'flex';
}
function closeViewModal() { viewModal.style.display = 'none'; }

// ── Post Modal ────────────────────────────────────────────────────
function closePostModal() {
    postModal.style.display = 'none';
    document.getElementById('postTitle').value       = '';
    document.getElementById('postDescription').value = '';
    document.getElementById('postSubject').value     = '';
    document.getElementById('postGradeLevel').value  = '';
    document.getElementById('postSectionCheckboxes').innerHTML = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
}

function adminSubmitAnnouncement() {
    const title    = document.getElementById('postTitle').value.trim();
    const desc     = document.getElementById('postDescription').value.trim();
    const subj     = document.getElementById('postSubject').value;
    const sections = [...document.querySelectorAll('#postSectionCheckboxes input:checked')].map(c => c.value);

    if (!title)           { showPopup('Validation', 'Title is required.', 'warning'); return; }
    if (!subj)            { showPopup('Validation', 'Please select a subject.', 'warning'); return; }
    if (!sections.length) { showPopup('Validation', 'Please select at least one section.', 'warning'); return; }

    closePostModal();
    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) loadingModal.style.display = 'flex';

    requestAnimationFrame(() => requestAnimationFrame(() => {
        $.ajax({
            url:    '{{ route("announcements.store") }}',
            method: 'POST',
            data:   { title, description: desc, subject_id: subj, sections },
            success: function (response) {
                if (loadingModal) loadingModal.style.display = 'none';
                setTimeout(() => {
                    if (response.status === 'success') { showPopup('Success', response.message, 'success'); loadAnnouncements(); }
                    else                               { showPopup('Error', response.message, 'error'); }
                }, 100);
            },
            error: function () {
                if (loadingModal) loadingModal.style.display = 'none';
                setTimeout(() => showPopup('Error', 'An error occurred.', 'error'), 100);
            }
        });
    }));
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

document.getElementById('postGradeLevel').addEventListener('change', function () {
    const gradeId = this.value;
    const wrap    = document.getElementById('postSectionCheckboxes');
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

// ── Load announcements ────────────────────────────────────────────
function loadAnnouncements() {
    $.ajax({
        url: '{{ route("announcements.all") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#announcementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    tbody.append(`
                        <tr>
                            <td class="cell-date">${row.date_posted}</td>
                            <td>
                                <button style="background:none; border:none; cursor:pointer; color:#60a5fa; font-weight:600; font-size:13px; text-align:left; padding:0; font-family:inherit;"
                                    onclick="openViewModal(${row.id},\`${(row.title||'').replace(/`/g,"'")}\`,\`${(row.description||'').replace(/`/g,"'")}\`,\`${(row.subject_name||'').replace(/`/g,"'")}\`,\`${(row.section_names||'').replace(/`/g,"'")}\`,\`${(row.posted_by||'').replace(/`/g,"'")}\`)">
                                    ${row.title}
                                </button>
                            </td>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${row.description || '—'}</td>
                            <td>${row.subject_name || '—'}</td>
                            <td>${row.grade_level_names || '—'}</td>
                            <td>${row.section_names || '—'}</td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap:7px;">
                                    <span style="width:26px; height:26px; border-radius:50%; background:rgba(37,99,235,0.18); color:#60a5fa; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0;">
                                        ${(row.posted_by||'?').charAt(0).toUpperCase()}
                                    </span>
                                    <span style="color:#94a3b8; font-size:13px;">${row.posted_by || '—'}</span>
                                </span>
                            </td>
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
    closePostModal();
    postModal.style.display = 'flex';
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeViewModal(); closePostModal(); }
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
                    <th>Actions</th>
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

{{-- TEACHER: VIEW / EDIT / DELETE MODAL --}}
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
                    <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Grade Level</div>
                    <div id="modalGradeLevel" style="font-size:13px; color:#94a3b8;"></div>
                </div>
            </div>
            <div style="background:#0f172a; border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:10.5px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Sections</div>
                <div id="modalSections" style="font-size:13px; color:#94a3b8;"></div>
            </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary" onclick="openEditModal()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Edit
            </button>
            <button class="btn btn-danger" onclick="confirmDeleteAnnouncement()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Delete
            </button>
        </div>
    </div>
</div>

{{-- TEACHER: POST / EDIT MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#111827; border:1px solid rgba(255,255,255,0.08); border-radius:14px; width:540px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.5); margin:16px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 id="postModalTitle" style="font-size:15px; font-weight:700; color:#e2e8f0;">Post Announcement</h3>
            <button onclick="closePostModal()" style="background:none; border:none; cursor:pointer; color:#475569; font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>

        <input type="hidden" id="editAnnouncementId">

        <div style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Title</label>
                <input type="text" id="announcementTitle" class="form-input" placeholder="Announcement title">
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Description</label>
                <textarea id="announcementDescription" class="form-input" rows="3" placeholder="Announcement details..."></textarea>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Subject</label>
                <select id="announcementSubject" class="form-select">
                    <option value="">Select subject</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Grade Level</label>
                <select id="announcementGradeLevel" class="form-select">
                    <option value="">All grade levels</option>
                </select>
            </div>
            <div>
                <label class="filter-label" style="display:block; margin-bottom:5px;">Sections</label>
                <div id="sectionCheckboxes" style="background:#0f172a; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); display:flex; flex-wrap:wrap; gap:8px; min-height:44px;">
                    <span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.06);">
            <button class="btn btn-outline" onclick="closePostModal()">Cancel</button>
            <button class="btn btn-primary" id="postSubmitBtn" onclick="submitAnnouncement()">Post Announcement</button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

let currentAnnouncement = {};
const viewModal = document.getElementById('viewModal');
const postModal = document.getElementById('postModal');

// ── View ──────────────────────────────────────────────────────────
function openViewModal(id, title, description, subject_name, grade_level_names, section_names) {
    currentAnnouncement = { id, title, description, subject_name, grade_level_names, section_names };
    document.getElementById('modalTitle').textContent       = title;
    document.getElementById('modalDescription').textContent = description || '—';
    document.getElementById('modalSubject').textContent     = subject_name;
    document.getElementById('modalGradeLevel').textContent  = grade_level_names || '—';
    document.getElementById('modalSections').textContent    = section_names || '—';
    viewModal.style.display = 'flex';
}
function closeViewModal() { viewModal.style.display = 'none'; }

// ── Post / Edit ───────────────────────────────────────────────────
function closePostModal() {
    postModal.style.display = 'none';
    document.getElementById('editAnnouncementId').value     = '';
    document.getElementById('announcementTitle').value      = '';
    document.getElementById('announcementDescription').value = '';
    document.getElementById('announcementSubject').value    = '';
    document.getElementById('announcementGradeLevel').value = '';
    document.getElementById('sectionCheckboxes').innerHTML  = '<span style="color:#334155; font-size:12px;">Select a grade level to load sections</span>';
    document.getElementById('postModalTitle').textContent   = 'Post Announcement';
    document.getElementById('postSubmitBtn').textContent    = 'Post Announcement';
}

function openEditModal() {
    closeViewModal();
    document.getElementById('postModalTitle').textContent   = 'Edit Announcement';
    document.getElementById('postSubmitBtn').textContent    = 'Update Announcement';
    document.getElementById('editAnnouncementId').value     = currentAnnouncement.id;
    document.getElementById('announcementTitle').value      = currentAnnouncement.title;
    document.getElementById('announcementDescription').value = currentAnnouncement.description || '';
    postModal.style.display = 'flex';
}

function confirmDeleteAnnouncement() {
    showConfirmationModal('Delete Announcement', 'Are you sure you want to delete this announcement?', function () {
        closeViewModal();
        const loadingModal = document.getElementById('loading-modal');
        if (loadingModal) loadingModal.style.display = 'flex';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            $.ajax({
                url: '{{ route("announcements.destroy") }}', method: 'POST',
                data: { id: currentAnnouncement.id },
                success: function (response) {
                    if (loadingModal) loadingModal.style.display = 'none';
                    setTimeout(() => {
                        if (response.status === 'success') { showPopup('Deleted', response.message, 'success'); loadAnnouncements(); }
                        else                               { showPopup('Error', response.message, 'error'); }
                    }, 100);
                },
                error: function () {
                    if (loadingModal) loadingModal.style.display = 'none';
                    setTimeout(() => showPopup('Error', 'An error occurred.', 'error'), 100);
                }
            });
        }));
    });
}

function submitAnnouncement() {
    const editId   = document.getElementById('editAnnouncementId').value;
    const title    = document.getElementById('announcementTitle').value.trim();
    const desc     = document.getElementById('announcementDescription').value.trim();
    const subj     = document.getElementById('announcementSubject').value;
    const sections = [...document.querySelectorAll('#sectionCheckboxes input:checked')].map(c => c.value);

    if (!title)           { showPopup('Validation', 'Title is required.', 'warning'); return; }
    if (!subj)            { showPopup('Validation', 'Please select a subject.', 'warning'); return; }
    if (!sections.length) { showPopup('Validation', 'Please select at least one section.', 'warning'); return; }

    const data = { title, description: desc, subject_id: subj, sections };
    if (editId) data.id = editId;

    closePostModal();
    const loadingModal = document.getElementById('loading-modal');
    if (loadingModal) loadingModal.style.display = 'flex';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        $.ajax({
            url:    editId ? '{{ route("announcements.update") }}' : '{{ route("announcements.store") }}',
            method: 'POST', data,
            success: function (response) {
                if (loadingModal) loadingModal.style.display = 'none';
                setTimeout(() => {
                    if (response.status === 'success') { showPopup('Success', response.message, 'success'); loadAnnouncements(); }
                    else                               { showPopup('Error', response.message, 'error'); }
                }, 100);
            },
            error: function () {
                if (loadingModal) loadingModal.style.display = 'none';
                setTimeout(() => showPopup('Error', 'An error occurred.', 'error'), 100);
            }
        });
    }));
}

// ── Grade → Sections ──────────────────────────────────────────────
document.getElementById('announcementGradeLevel').addEventListener('change', function () {
    const gradeId = this.value;
    const wrap    = document.getElementById('sectionCheckboxes');
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
function loadSubjectsAndGrades() {
    $.get('{{ route("fields.subjects") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('announcementSubject');
            response.data.forEach(s => {
                const o = document.createElement('option'); o.value = s.id; o.textContent = s.subject_name; sel.appendChild(o);
            });
        }
    });
    $.get('{{ route("fields.gradeLevels") }}', function (response) {
        if (response.status === 'success') {
            const sel = document.getElementById('announcementGradeLevel');
            response.data.forEach(g => {
                const o = document.createElement('option'); o.value = g.id; o.textContent = g.grade_level_name; sel.appendChild(o);
            });
        }
    });
}

// ── Load announcements ────────────────────────────────────────────
function loadAnnouncements() {
    $.ajax({
        url: '{{ route("announcements.list") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#announcementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    const esc = s => (s||'').toString().replace(/`/g,"'").replace(/\\/g,'\\\\');
                    tbody.append(`
                        <tr>
                            <td class="cell-date">${row.date_posted}</td>
                            <td>
                                <button style="background:none; border:none; cursor:pointer; color:#60a5fa; font-weight:600; font-size:13px; padding:0; font-family:inherit; text-align:left;"
                                    onclick="openViewModal(${row.id},\`${esc(row.title)}\`,\`${esc(row.description)}\`,\`${esc(row.subject_name)}\`,\`${esc(row.grade_level_names)}\`,\`${esc(row.section_names)}\`)">
                                    ${row.title}
                                </button>
                            </td>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#64748b;">${row.description || '—'}</td>
                            <td>${row.subject_name || '—'}</td>
                            <td>${row.grade_level_names || '—'}</td>
                            <td>${row.section_names || '—'}</td>
                            <td>
                                <button class="btn btn-outline" style="padding:3px 10px; font-size:11px;"
                                    onclick="openViewModal(${row.id},\`${esc(row.title)}\`,\`${esc(row.description)}\`,\`${esc(row.subject_name)}\`,\`${esc(row.grade_level_names)}\`,\`${esc(row.section_names)}\`)">
                                    View
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="7" style="text-align:center; padding:40px; color:#334155; font-size:13px;">No announcements found.</td></tr>');
            }
        }
    });
}

document.getElementById('openPostModal').addEventListener('click', function () { closePostModal(); postModal.style.display = 'flex'; });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeViewModal(); closePostModal(); } });

$(document).ready(function () { loadAnnouncements(); loadSubjectsAndGrades(); });
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
            <tbody id="announcementsTable">
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
        </div>
        <div style="display:flex; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const viewModal = document.getElementById('viewModal');

function openViewModal(id, title, description, subject_name, section_names, posted_by) {
    document.getElementById('modalTitle').textContent       = title;
    document.getElementById('modalDescription').textContent = description || '—';
    document.getElementById('modalSubject').textContent     = subject_name;
    document.getElementById('modalSections').textContent    = section_names || '—';
    document.getElementById('modalPostedBy').textContent    = posted_by || '—';
    viewModal.style.display = 'flex';
}
function closeViewModal() { viewModal.style.display = 'none'; }

function loadAnnouncements() {
    $.ajax({
        url: '{{ route("announcements.list") }}', method: 'GET',
        success: function (response) {
            const tbody = $('#announcementsTable');
            tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                $.each(response.data, function (i, row) {
                    const esc = s => (s||'').toString().replace(/`/g,"'");
                    tbody.append(`
                        <tr style="cursor:pointer;" onclick="openViewModal(
                            ${row.id},
                            \`${esc(row.title)}\`,
                            \`${esc(row.description)}\`,
                            \`${esc(row.subject_name)}\`,
                            \`${esc(row.section_names)}\`,
                            \`${esc(row.posted_by)}\`
                        )">
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
        error: function () {
            $('#announcementsTable').html('<tr><td colspan="4" style="text-align:center; padding:40px; color:#f87171; font-size:13px;">Failed to load announcements.</td></tr>');
        }
    });
}

document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeViewModal(); });
$(document).ready(function () { loadAnnouncements(); });
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