@extends('layouts.app')

@section('title', 'User Management — School Information System')

@section('page-title')
    <h2>User Management</h2>
@endsection

@push('styles')
<style>
/* ── Edit Modal ─────────────────────────────────────────────────────────── */
.um-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.65); z-index: 200;
    align-items: center; justify-content: center;
    backdrop-filter: blur(3px);
}
.um-backdrop.open { display: flex; }
.um-modal {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: 14px; width: 100%; max-width: 600px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    margin: 16px; animation: umIn .2s ease-out;
}
@keyframes umIn {
    from { opacity:0; transform:translateY(16px) scale(.98); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
.um-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px 14px; border-bottom: 1px solid var(--dk-b2);
    position: sticky; top: 0; background: var(--dk-surface); z-index: 1;
}
.um-modal-header h3 { font-size: 15px; font-weight: 700; color: var(--dk-t1); margin: 0; }
.um-modal-close {
    background: none; border: none; cursor: pointer;
    color: var(--dk-t4); font-size: 18px; line-height: 1; padding: 4px;
}
.um-modal-body { padding: 20px 24px; }
.um-section-divider {
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--dk-t4);
    padding: 10px 0 6px; border-bottom: 1px solid var(--dk-b2);
    margin-bottom: 14px; grid-column: 1/-1;
}
.um-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.um-form-full { grid-column: 1/-1; }
.um-form-label {
    display: block; font-size: 11px; font-weight: 700; color: var(--dk-t4);
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px;
}
.um-form-input, .um-form-select {
    width: 100%; padding: 8px 12px;
    border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
    font-size: 13px; color: var(--dk-t2);
    background: var(--dk-surface2); font-family: var(--font-body);
    outline: none; box-sizing: border-box; transition: border-color .2s;
}
.um-form-input:focus, .um-form-select:focus { border-color: rgba(96,165,250,0.4); }
.um-form-input:disabled { opacity: .5; cursor: not-allowed; }
.um-form-select option { background: #111827; color: #e2e8f0; }
.um-modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 14px 24px 20px; border-top: 1px solid var(--dk-b2);
    position: sticky; bottom: 0; background: var(--dk-surface);
}
.um-error {
    display: none; background: rgba(220,38,38,0.12);
    color: #f87171; border: 1px solid rgba(220,38,38,0.3);
    padding: 9px 13px; border-radius: 6px;
    font-size: 13px; font-weight: 600; margin-bottom: 14px;
    grid-column: 1/-1;
}

/* ── Delete Options Modal ─────────────────────────────────────────────────── */
.delete-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.65); z-index: 250;
    align-items: center; justify-content: center;
    backdrop-filter: blur(3px);
}
.delete-backdrop.open { display: flex; }
.delete-modal {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: 14px; width: 100%; max-width: 400px;
    padding: 24px; box-shadow: 0 20px 60px rgba(0,0,0,.5);
    margin: 16px;
}
.delete-modal h3 { font-size: 15px; font-weight: 700; color: var(--dk-t1); margin: 0 0 8px; }
.delete-modal p { font-size: 13px; color: var(--dk-t3); margin-bottom: 20px; }
.delete-actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn-delete-soft { background: rgba(37,99,235,0.12); color: #60a5fa; border:1px solid rgba(37,99,235,0.25); }
.btn-delete-hard { background: rgba(220,38,38,0.12); color: #f87171; border:1px solid rgba(220,38,38,0.25); }

/* ── Row action buttons ─────────────────────────────────────────────────── */
.row-actions { display: flex; gap: 6px; align-items: center; }
.btn-row-edit {
    padding: 4px 10px; font-size: 0.75rem; font-weight: 600;
    border-radius: 5px; cursor: pointer;
    background: rgba(37,99,235,0.12); color: #60a5fa;
    border: 1px solid rgba(37,99,235,0.25);
    transition: all .15s;
}
.btn-row-edit:hover { background: rgba(37,99,235,0.22); }
.btn-row-edit:disabled {
    opacity: 0.5; cursor: not-allowed; background: rgba(100,116,139,0.1); color: #94a3b8;
    border-color: rgba(100,116,139,0.2);
}
.btn-row-delete {
    padding: 4px 10px; font-size: 0.75rem; font-weight: 600;
    border-radius: 5px; cursor: pointer;
    background: rgba(220,38,38,0.12); color: #f87171;
    border: 1px solid rgba(220,38,38,0.25);
    transition: all .15s;
}
.btn-row-delete:hover { background: rgba(220,38,38,0.22); }
.btn-row-delete:disabled {
    opacity: 0.5; cursor: not-allowed; background: rgba(100,116,139,0.1); color: #94a3b8;
    border-color: rgba(100,116,139,0.2);
}
</style>
@endpush

@section('content')

{{-- Stat Cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Users</span>
            <div class="stat-icon blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value" id="total-users-stat">...</div>
        <div class="stat-meta" id="total-users-meta">...</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Active</span>
            <div class="stat-icon green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M22 4 12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value" id="active-users-stat">...</div>
        <div class="stat-meta" id="active-users-meta">...</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">New This Month</span>
            <div class="stat-icon red">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12l7-7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value" id="new-this-month-stat">...</div>
        <div class="stat-meta" id="new-this-month-meta">...</div>
    </div>
</div>

{{-- Main Card --}}
<div class="card">
    <div class="card-toolbar">
        <div class="view-toggle">
            <button class="vtog-btn" onclick="setRoleFilter(this,'student')">Student</button>
            <button class="vtog-btn" onclick="setRoleFilter(this,'teacher')">Teacher</button>
            <button class="vtog-btn active" onclick="setRoleFilter(this,'all')">All</button>
        </div>
        <div class="search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#94a3b8" stroke-width="2"/>
                <path d="m21 21-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input type="text" id="searchInput" placeholder="Search user, ID, class…" class="search-input">
        </div>
    </div>

    <div class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select id="filterStatus" class="form-select" style="min-width:120px;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="filter-actions" style="margin-left:auto;">
            <button class="btn btn-outline" id="btnResetFilters">Reset</button>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                Add User
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User Details</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody id="student-table-body">
                <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--dk-t4);">
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                </td></tr>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <span class="page-info" id="pagination-info"></span>
    </div>
</div>

{{-- Edit Modal --}}
<div id="umBackdrop" class="um-backdrop">
    <div class="um-modal">
        <div class="um-modal-header">
            <h3 id="umModalTitle">Edit User</h3>
            <button class="um-modal-close" id="btnUmClose">✕</button>
        </div>

        <div class="um-modal-body">
            {{-- Loading Spinner --}}
            <div id="umModalLoading" style="display: flex; justify-content: center; padding: 40px;">
                <span class="loading loading-dots loading-lg" style="color:#60a5fa;"></span>
            </div>

            {{-- Form Content --}}
            <div id="umFormContent" style="display: none;">
                <div class="um-form-grid">
                    <div id="umError" class="um-error"></div>

                    <input type="hidden" id="umId">
                    <input type="hidden" id="umType"> {{-- 1=teacher, 2=student --}}

                    {{-- Personal Information --}}
                    <div class="um-section-divider">Personal Information</div>
                    <div>
                        <label class="um-form-label">Last Name *</label>
                        <input type="text" id="umLname" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">First Name *</label>
                        <input type="text" id="umFname" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Middle Name</label>
                        <input type="text" id="umMname" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Date of Birth</label>
                        <input type="date" id="umDob" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Sex</label>
                        <select id="umSex" class="um-form-select">
                            <option value="">Select…</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="um-form-label">Civil Status</label>
                        <select id="umCivilStatus" class="um-form-select">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </div>
                    <div class="um-form-full">
                        <label class="um-form-label">Address</label>
                        <input type="text" id="umAddress" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Contact Number</label>
                        <input type="tel" id="umContact" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Email *</label>
                        <input type="email" id="umEmail" class="um-form-input">
                    </div>
                    <div>
                        <label class="um-form-label">Status</label>
                        <select id="umStatus" class="um-form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Student-only fields --}}
                    <div id="umStudentSection" style="display: none;">
                        <div class="um-section-divider">Academic Information</div>
                        <div class="um-form-full">
                            <label class="um-form-label">LRN</label>
                            <input type="text" id="umLrn" class="um-form-input" maxlength="12">
                        </div>
                        <div>
                            <label class="um-form-label">Grade Level</label>
                            <select id="umGradeLevel" class="um-form-select">
                                <option value="">Select grade…</option>
                                @foreach($gradeLevels as $gl)
                                    <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="um-form-label">Section</label>
                            <div style="position:relative;">
                                <select id="umSection" class="um-form-select">
                                    <option value="">Select grade level first…</option>
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">
                                            {{ $sec->section_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="umSectionLoader" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); display:none;">
                                    <span class="loading loading-dots loading-sm"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Teacher-only fields --}}
                    <div id="umTeacherSection" style="display: none;">
                        <div class="um-section-divider">Faculty Details</div>
                        <div>
                            <label class="um-form-label">Employee ID</label>
                            <input type="text" id="umEmployeeId" class="um-form-input">
                        </div>
                        <div>
                            <label class="um-form-label">Department</label>
                            <select id="umDepartment" class="um-form-select">
                                <option value="">Select…</option>
                                <option>Junior High School</option>
                                <option>Senior High School</option>
                                <option>Administration</option>
                                <option>Guidance</option>
                            </select>
                        </div>
                        <div>
                            <label class="um-form-label">Position</label>
                            <input type="text" id="umPosition" class="um-form-input">
                        </div>
                        <div>
                            <label class="um-form-label">Specialization</label>
                            <input type="text" id="umSpecialization" class="um-form-input">
                        </div>
                        <div>
                            <label class="um-form-label">Employment Status</label>
                            <select id="umEmploymentStatus" class="um-form-select">
                                <option>Permanent</option>
                                <option>Temporary</option>
                                <option>Contractual</option>
                                <option>Part-time</option>
                            </select>
                        </div>
                        <div>
                            <label class="um-form-label">Date Hired</label>
                            <input type="date" id="umDateHired" class="um-form-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="um-modal-footer">
            <button class="btn btn-outline" id="btnUmCancel">Cancel</button>
            <button class="btn btn-primary" id="btnUmSave">Save Changes</button>
        </div>
    </div>
</div>

{{-- Delete Options Modal --}}
<div id="deleteBackdrop" class="delete-backdrop">
    <div class="delete-modal">
        <h3 id="deleteModalTitle">Delete User</h3>
        <p id="deleteModalMessage">Choose an action for <span id="deleteUserName"></span>.</p>
        <div class="delete-actions">
            <button class="btn btn-outline" id="deleteCancelBtn">Cancel</button>
            <button class="btn btn-delete-soft" id="deleteSoftBtn">Set Inactive</button>
            <button class="btn btn-delete-hard" id="deleteHardBtn">Permanently Delete</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const URL_LIST    = '{{ route("students.index") }}';
    const URL_UPDATE  = '{{ route("students.update", ":id") }}';
    const URL_DESTROY = '{{ route("students.destroy", ":id") }}'; // soft delete (mode 26)
    const URL_HARD_DELETE = '{{ url("students/hard-delete") }}/'; // base URL for hard delete
    const URL_SHOW    = '{{ route("students.show", ":id") }}';

    let allStudents       = [];
    let currentRoleFilter = 'all';

    // ── Load data ─────────────────────────────────────────────────────────────
    function fetchStudents() {
        $.ajax({
            url: URL_LIST, type: 'GET', dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                allStudents = res.data ?? [];
                renderStudents();
                updateStatCards();
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message ?? 'Failed to load users.';
                $('#student-table-body').html(
                    `<tr><td colspan="4" style="text-align:center; color:var(--red-400); padding:30px;">${msg}</td></tr>`
                );
            }
        });
    }

    // ── Stat Cards ──────────────────────────────────────────────────────────
    function updateStatCards() {
        const total  = allStudents.length;
        const active = allStudents.filter(s => (s.status ?? '').toLowerCase() === 'active').length;
        const now    = new Date();
        const newMo  = allStudents.filter(s => {
            const d = new Date(s.created_at);
            return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
        }).length;
        const pct = total > 0 ? (active / total * 100).toFixed(1) : 0;

        $('#total-users-stat').text(total.toLocaleString());
        $('#total-users-meta').html(`<span>+${newMo}</span> joined this month`);
        $('#active-users-stat').text(active.toLocaleString());
        $('#active-users-meta').html(`<span>${pct}%</span> of users are active`);
        $('#new-this-month-stat').text(newMo);
        $('#new-this-month-meta').text('new users this month');
    }

    // ── Filters ─────────────────────────────────────────────────────────────
    window.setRoleFilter = function (btn, role) {
        document.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentRoleFilter = role;
        renderStudents();
    };

    $('#searchInput, #filterStatus').on('input change', renderStudents);
    $('#btnResetFilters').on('click', function () {
        $('#searchInput').val('');
        $('#filterStatus').val('');
        document.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
        document.querySelector('.vtog-btn:last-child').classList.add('active');
        currentRoleFilter = 'all';
        renderStudents();
    });

    // ── Render Table ─────────────────────────────────────────────────────────
    function renderStudents() {
        const search  = ($('#searchInput').val() ?? '').toLowerCase().trim();
        const status  = ($('#filterStatus').val() ?? '').toLowerCase();
        const roleF   = currentRoleFilter.toLowerCase();

        let rows = allStudents.filter(s => {
            const roleName = (s.role_name ?? '').trim().toLowerCase();
            if (roleF !== 'all' && roleName !== roleF) return false;
            if (status && (s.status ?? '').toLowerCase() !== status) return false;
            if (search) {
                const haystack = `${s.name} ${s.email}`.toLowerCase();
                if (!haystack.includes(search)) return false;
            }
            return true;
        });

        const tbody = $('#student-table-body').empty();

        if (!rows.length) {
            tbody.append(`<tr><td colspan="4" style="text-align:center; padding:36px; color:var(--dk-t4);">No records found.</td></tr>`);
            $('#pagination-info').text('Showing 0 of ' + allStudents.length);
            return;
        }

        rows.forEach(function (s) {
            const isActive  = (s.status ?? '').toLowerCase() === 'active';
            const dotClass  = isActive ? 'dot-green' : 'dot-red';
            const badgeCls  = isActive ? 'status-active' : 'status-inactive';
            const statusTxt = (s.status ?? 'Inactive');
            const roleName  = s.role_name ?? 'Unknown';
            const isAdmin   = roleName.toLowerCase() === 'admin';

            const editBtn = isAdmin
                ? '<button class="btn-row-edit" disabled title="Admin accounts cannot be edited">Edit</button>'
                : `<button class="btn-row-edit" data-id="${s.id}" data-role="${roleName.toLowerCase()}">Edit</button>`;

            const deleteBtn = isAdmin
                ? '<button class="btn-row-delete" disabled title="Admin accounts cannot be deleted">Delete</button>'
                : `<button class="btn-row-delete" data-id="${s.id}" data-name="${(s.name ?? '').replace(/"/g, '&quot;')}">Delete</button>`;

            tbody.append(`
                <tr>
                    <td>
                        <div class="cell-detail">
                            <div class="detail-dot ${dotClass}"></div>
                            <div>
                                <div class="detail-text">${s.name ?? 'N/A'}</div>
                                <div class="detail-sub">${s.email ?? 'N/A'}</div>
                            </div>
                        </div>
                    </td>
                    <td><div style="font-weight:500;">${roleName}</div></td>
                    <td>
                        <span class="status-badge ${badgeCls}">
                            <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                            ${statusTxt}
                        </span>
                    </td>
                    <td>
                        <div class="row-actions" style="justify-content:center;">
                            ${editBtn}
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>`);
        });

        $('#pagination-info').text(`Showing ${rows.length} of ${allStudents.length}`);
    }

    // ── Edit Modal ───────────────────────────────────────────────────────────
    const $backdrop = $('#umBackdrop');

    function openEditModal(userId, roleHint) {
        $('#umModalLoading').show();
        $('#umFormContent').hide();
        $('#umError').hide().text('');
        $('#umModalTitle').text('Edit User');
        $('#btnUmSave').prop('disabled', true).text('Loading…');
        $backdrop.addClass('open');

        $.ajax({
            url: URL_SHOW.replace(':id', userId),
            type: 'GET', dataType: 'json',
            success: function (res) {
                $('#umModalLoading').hide();
                if (res.status !== 'success') {
                    showPopup('Error', res.message, 'error');
                    closeModal();
                    return;
                }
                populateModal(res.data);
                $('#umFormContent').show();
                $('#btnUmSave').prop('disabled', false).text('Save Changes');
            },
            error: function (xhr) {
                $('#umModalLoading').hide();
                showPopup('Error', xhr.responseJSON?.message ?? 'Failed to load user.', 'error');
                closeModal();
            }
        });
    }

    function populateModal(data) {
        const user = data.user || {};
        const d    = data.details || {};
        const isTeacher = (user.role_id == 1);

        $('#umId').val(user.id);
        $('#umType').val(user.role_id == 1 ? 1 : 2);
        $('#umEmail').val(user.email || '');
        $('#umStatus').val(user.status || 'Active');

        $('#umFname').val(d.first_name || d.fname || '');
        $('#umLname').val(d.last_name || d.lname || '');
        $('#umMname').val(d.middle_name || d.mname || '');
        $('#umDob').val(d.dob || d.birthdate || '');
        $('#umSex').val(d.sex || '');
        $('#umCivilStatus').val(d.civil_status || d.Civil_status || 'Single');
        $('#umAddress').val(d.address || '');
        $('#umContact').val(d.contact || d.contact_no || '');

        if (isTeacher) {
            showTeacherFields();
            $('#umEmployeeId').val(d.employee_id || '');
            $('#umDepartment').val(d.department || '');
            $('#umPosition').val(d.position || '');
            $('#umSpecialization').val(d.specialization || '');
            $('#umEmploymentStatus').val(d.employment_status || 'Permanent');
            $('#umDateHired').val(d.date_hired || '');
        } else {
            showStudentFields();
            $('#umLrn').val(d.lrn || d.student_no || '');

            const grade = d.section_id
                ? ($('#umSection option[value="' + d.section_id + '"]').data('grade') || '')
                : '';
            $('#umGradeLevel').val(grade);
            filterSectionsByGrade(grade);
            $('#umSection').val(d.section_id || '');
        }
    }

    function showStudentFields() {
        $('#umStudentSection').show();
        $('#umTeacherSection').hide();
    }

    function showTeacherFields() {
        $('#umStudentSection').hide();
        $('#umTeacherSection').show();
    }

    $('#umGradeLevel').on('change', function () {
        filterSectionsByGrade($(this).val());
        $('#umSection').val('');
    });

    function filterSectionsByGrade(gradeId) {
        const $loader = $('#umSectionLoader');
        const $select = $('#umSection');

        $loader.show();
        $select.prop('disabled', true);

        setTimeout(() => {
            $select.find('option').each(function () {
                if (!$(this).val()) return;
                $(this).toggle(!gradeId || $(this).data('grade') == gradeId);
            });
            $loader.hide();
            $select.prop('disabled', false);
        }, 300);
    }

    function closeModal() {
        $backdrop.removeClass('open');
    }

    $('#btnUmClose, #btnUmCancel').on('click', closeModal);
    $backdrop.on('click', function (e) {
        if ($(e.target).is($backdrop)) closeModal();
    });

    $(document).on('click', '.btn-row-edit:not(:disabled)', function () {
        const id   = $(this).data('id');
        const role = $(this).data('role');
        openEditModal(id, role);
    });

    // ── Delete Options Modal ─────────────────────────────────────────────────
    const $deleteBackdrop = $('#deleteBackdrop');

    function openDeleteModal(id, name) {
        $deleteBackdrop.data('current-id', id);
        $('#deleteUserName').text(name);
        $deleteBackdrop.addClass('open');
    }

    function closeDeleteModal() {
        $deleteBackdrop.removeClass('open');
        $deleteBackdrop.removeData('current-id');
    }

    $('#deleteCancelBtn').on('click', closeDeleteModal);
    $deleteBackdrop.on('click', function (e) {
        if ($(e.target).is($deleteBackdrop)) closeDeleteModal();
    });

    // Soft delete (mode 26)
    $('#deleteSoftBtn').on('click', function () {
        const id = $deleteBackdrop.data('current-id');
        if (!id) return;
        closeDeleteModal();
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:         URL_DESTROY.replace(':id', id),
            method:      'POST',
            contentType: 'application/json',
            data:        JSON.stringify({ id: id, _method: 'DELETE' }),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    showPopup('Done', res.message, 'success');
                    fetchStudents();
                } else {
                    showPopup('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', xhr.responseJSON?.message ?? 'Soft delete failed.', 'error');
            }
        });
    });

    // Hard delete (mode 27)
    $('#deleteHardBtn').on('click', function () {
        const id = $deleteBackdrop.data('current-id');
        if (!id) return;
        closeDeleteModal();
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:         URL_HARD_DELETE + id,
            method:      'POST',
            contentType: 'application/json',
            data:        JSON.stringify({ id: id, _method: 'DELETE' }),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    showPopup('Deleted', res.message, 'success');
                    fetchStudents();
                } else {
                    showPopup('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', xhr.responseJSON?.message ?? 'Hard delete failed.', 'error');
            }
        });
    });

    // ── Wire Delete buttons (delegated) ─────────────────────────────────────
    $(document).on('click', '.btn-row-delete:not(:disabled)', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        openDeleteModal(id, name);
    });

    // ── Save Changes ────────────────────────────────────────────────────────
    $('#btnUmSave').on('click', function () {
        $('#umError').hide();

        const id      = $('#umId').val();
        const type    = $('#umType').val();
        const isTeach = type == 1;

        const fname  = $('#umFname').val().trim();
        const lname  = $('#umLname').val().trim();
        const email  = $('#umEmail').val().trim();
        if (!fname) { showErr('First name is required.'); return; }
        if (!lname) { showErr('Last name is required.'); return; }
        if (!email) { showErr('Email is required.'); return; }

        const payload = {
            id:           id,
            student_type: type,
            first_name:   fname,
            last_name:    lname,
            middle_name:  $('#umMname').val().trim() || null,
            dob:          $('#umDob').val() || null,
            sex:          $('#umSex').val() || null,
            civil_status: $('#umCivilStatus').val(),
            address:      $('#umAddress').val().trim() || null,
            contact:      $('#umContact').val().trim() || null,
            email:        email,
            status:       $('#umStatus').val(),
        };

        if (isTeach) {
            payload.employee_id       = $('#umEmployeeId').val().trim() || null;
            payload.department        = $('#umDepartment').val() || null;
            payload.position          = $('#umPosition').val().trim() || null;
            payload.specialization    = $('#umSpecialization').val().trim() || null;
            payload.employment_status = $('#umEmploymentStatus').val();
            payload.date_hired        = $('#umDateHired').val() || null;
        } else {
            payload.lrn          = $('#umLrn').val().trim() || null;
            payload.grade_level  = $('#umGradeLevel').val() || null;
            payload.section      = $('#umSection').val() || null;
        }

        const $btn = $(this).prop('disabled', true).text('Saving…');
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:         URL_UPDATE.replace(':id', id),
            method:      'POST',
            contentType: 'application/json',
            data:        JSON.stringify({ ...payload, _method: 'PUT' }),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    closeModal();
                    showPopup('Success', res.message, 'success');
                    fetchStudents();
                } else {
                    showErr(res.message ?? 'Save failed.');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showErr(xhr.responseJSON?.message ?? 'Something went wrong.');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    function showErr(msg) {
        $('#umError').text(msg).show();
        $('#umError')[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    fetchStudents();
});
</script>
@endpush