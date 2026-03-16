@extends('layouts.app')

@section('title', 'Policies — School Information System')

@section('page-title')
<h2>School Policies</h2>
@endsection

@section('content')

<style>
    .policies-layout {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 20px;
        align-items: start;
    }

    /* ── Info Panel ── */
    .info-panel { display: flex; flex-direction: column; gap: 16px; }

    .info-section {
        padding: 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--dk-b1);
        background: var(--dk-surface);
        position: relative;
        overflow: hidden;
    }
    .info-section::before {
        content: ''; position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
    }
    .info-section.mission::before { background: var(--blue-600); }
    .info-section.vision::before  { background: #16a34a; }
    .info-section.values::before  { background: #d97706; }

    .info-section-label {
        font-size: 10px; font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; margin-bottom: 8px;
        display: flex; align-items: center; gap: 6px;
    }
    .info-section.mission .info-section-label { color: #60a5fa; }
    .info-section.vision  .info-section-label { color: #4ade80; }
    .info-section.values  .info-section-label { color: #fbbf24; }

    .info-section-text {
        font-size: 13px; color: var(--dk-t3); line-height: 1.7;
    }

    .core-values-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }

    .core-value-chip {
        padding: 5px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        background: rgba(217,119,6,0.12); color: #fbbf24;
        border: 1px solid rgba(217,119,6,0.25);
    }

    .info-edit-btn {
        position: absolute; top: 14px; right: 14px;
        width: 28px; height: 28px;
        border: 1px solid var(--dk-b1); background: var(--dk-surface2);
        border-radius: 6px; display: flex; align-items: center;
        justify-content: center; cursor: pointer; color: var(--dk-t4);
        transition: all .15s;
    }
    .info-edit-btn:hover { background: var(--dk-b1); color: var(--dk-t1); }

    /* ── Policies Panel ── */
    .policies-panel { display: flex; flex-direction: column; }

    .policy-card {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 16px 20px; border-bottom: 1px solid var(--dk-b2);
        cursor: pointer; transition: background .12s;
    }
    .policy-card:last-child { border-bottom: none; }
    .policy-card:hover { background: rgba(255,255,255,0.03); }

    .policy-card-icon {
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 15px;
    }
    .policy-card-body { flex: 1; min-width: 0; }
    .policy-card-title {
        font-size: 13.5px; font-weight: 600; color: var(--dk-t1); margin-bottom: 3px;
    }
    .policy-card-meta {
        font-size: 12px; color: var(--dk-t4);
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }

    @media (max-width: 860px) { .policies-layout { grid-template-columns: 1fr; } }
</style>

<div class="policies-layout">

    {{-- ── Left: School Info Panel ── --}}
    <div class="info-panel">

        {{-- Mission --}}
        <div class="info-section mission">
            @if(auth()->user()->role->name === 'Admin')
            <button class="info-edit-btn" onclick="openInfoModal('mission')" title="Edit Mission">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            @endif
            <div class="info-section-label">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                Our Mission
            </div>
            <p class="info-section-text" id="missionText">
                <span style="display:flex;align-items:center;gap:6px;color:var(--dk-t4);font-size:13px;">
                    <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                    Loading...
                </span>
            </p>
        </div>

        {{-- Vision --}}
        <div class="info-section vision">
            @if(auth()->user()->role->name === 'Admin')
            <button class="info-edit-btn" onclick="openInfoModal('vision')" title="Edit Vision">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            @endif
            <div class="info-section-label">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Our Vision
            </div>
            <p class="info-section-text" id="visionText">
                <span style="display:flex;align-items:center;gap:6px;color:var(--dk-t4);font-size:13px;">
                    <span class="loading loading-dots loading-sm" style="color:#4ade80;"></span>
                    Loading...
                </span>
            </p>
        </div>

        {{-- Core Values --}}
        <div class="info-section values">
            @if(auth()->user()->role->name === 'Admin')
            <button class="info-edit-btn" onclick="openInfoModal('values')" title="Edit Core Values">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            @endif
            <div class="info-section-label">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Core Values
            </div>
            <div class="core-values-grid" id="coreValuesGrid">
                <span style="display:flex;align-items:center;gap:6px;color:var(--dk-t4);font-size:13px;">
                    <span class="loading loading-dots loading-sm" style="color:#fbbf24;"></span>
                    Loading...
                </span>
            </div>
        </div>

    </div>

    {{-- ── Right: Policies Panel ── --}}
    <div class="table-card" style="border-radius:var(--radius-lg); overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--dk-b2);">
            <span style="font-family:var(--font-display); font-size:14px; font-weight:700; color:var(--dk-t1);">School Policies</span>
            <div style="display:flex; gap:8px; align-items:center;">
                <select id="categoryFilter" class="form-select" style="font-size:12px; padding:6px 10px; width:auto;">
                    <option value="">All Categories</option>
                    <option value="Attendance">Attendance</option>
                    <option value="Grading">Grading</option>
                    <option value="Conduct">Conduct</option>
                    <option value="Uniform">Uniform</option>
                    <option value="Enrollment">Enrollment</option>
                    <option value="General">General</option>
                </select>
                @if(auth()->user()->role->name === 'Admin')
                <button class="btn btn-primary" id="openPostModal" style="font-size:12px; padding:6px 12px;">+ Add Policy</button>
                @endif
            </div>
        </div>

        <div class="policies-panel" id="policiesContainer">
            <div style="padding:40px; text-align:center; color:var(--dk-t4); font-size:13px; display:flex; align-items:center; justify-content:center; gap:6px;">
                <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                Loading policies...
            </div>
        </div>
    </div>

</div>

{{-- VIEW MODAL --}}
<div id="viewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:var(--dk-surface); border:1px solid var(--dk-b1); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.5); margin:16px; max-height:80vh; overflow-y:auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <div id="modalIconWrap" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"></div>
            <div>
                <h3 id="modalTitle" style="font-weight:700; font-size:15px; color:var(--dk-t1); margin:0 0 3px;"></h3>
                <span id="modalCategoryBadge"></span>
            </div>
        </div>
        <div style="background:var(--dk-surface2); border-radius:8px; padding:14px; margin-bottom:16px; border:1px solid var(--dk-b1);">
            <p id="modalDescription" style="font-size:13.5px; color:var(--dk-t3); line-height:1.7; white-space:pre-line; margin:0;"></p>
        </div>
        <div style="display:flex; align-items:center; justify-content:space-between; font-size:12px; color:var(--dk-t4); margin-bottom:16px;">
            <span>Effective: <strong id="modalDate" style="color:var(--dk-t2);"></strong></span>
            <span id="modalStatus"></span>
        </div>
        @if(auth()->user()->role->name === 'Admin')
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary" onclick="openEditModal()">Edit</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
        @else
        <div style="display:flex; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
        @endif
    </div>
</div>

{{-- POST / EDIT MODAL (admin only) --}}
@if(auth()->user()->role->name === 'Admin')
<div id="postModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:var(--dk-surface); border:1px solid var(--dk-b1); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.5); margin:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <h3 id="postModalTitle" style="font-size:15px; font-weight:700; color:var(--dk-t1); margin:0;">Add New Policy</h3>
            <button onclick="closePostModal()" style="background:none; border:none; cursor:pointer; color:var(--dk-t4); font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <form id="policyForm">
            <input type="hidden" id="editPolicyId" value="">

            <div style="margin-bottom:12px;">
                <label class="filter-label" style="display:block; margin-bottom:5px;">Title</label>
                <input type="text" id="policyTitle" class="form-input" placeholder="e.g. Attendance Policy">
            </div>
            <div style="margin-bottom:12px;">
                <label class="filter-label" style="display:block; margin-bottom:5px;">Description</label>
                <textarea id="policyDescription" class="form-input" rows="4" placeholder="Full policy details..."></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                    <label class="filter-label" style="display:block; margin-bottom:5px;">Category</label>
                    <select id="policyCategory" class="form-select">
                        <option value="">— Select —</option>
                        <option value="Attendance">Attendance</option>
                        <option value="Grading">Grading</option>
                        <option value="Conduct">Conduct</option>
                        <option value="Uniform">Uniform</option>
                        <option value="Enrollment">Enrollment</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label" style="display:block; margin-bottom:5px;">Status</label>
                    <select id="policyStatus" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:18px;">
                <label class="filter-label" style="display:block; margin-bottom:5px;">Effective Date</label>
                <input type="date" id="policyDate" class="form-input">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; padding-top:14px; border-top:1px solid var(--dk-b2);">
                <button type="button" class="btn btn-outline" onclick="closePostModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="postSubmitBtn">Save Policy</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT INFO MODAL (Mission / Vision / Core Values) --}}
<div id="infoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:var(--dk-surface); border:1px solid var(--dk-b1); border-radius:14px; width:480px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.5); margin:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <h3 id="infoModalTitle" style="font-size:15px; font-weight:700; color:var(--dk-t1); margin:0;">Edit Mission</h3>
            <button onclick="closeInfoModal()" style="background:none; border:none; cursor:pointer; color:var(--dk-t4); font-size:18px; line-height:1; padding:4px;">✕</button>
        </div>
        <form id="infoForm">
            <input type="hidden" id="infoField" value="">
            <div style="margin-bottom:14px;">
                <label class="filter-label" id="infoFieldLabel" style="display:block; margin-bottom:5px;">Mission Statement</label>
                <textarea id="infoText" class="form-input" rows="5" placeholder="Enter text here..."></textarea>
                <div id="coreValuesHint" style="display:none; font-size:12px; color:var(--dk-t4); margin-top:6px;">
                    Separate each value with a comma. e.g. Integrity, Excellence, Service
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; padding-top:14px; border-top:1px solid var(--dk-b2);">
                <button type="button" class="btn btn-outline" onclick="closeInfoModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const viewModal = document.getElementById('viewModal');
let currentPolicy = {};
let allPolicies   = [];
let schoolInfo    = {};

const categoryConfig = {
    Attendance: { bg: 'rgba(37,99,235,0.12)',   color: '#60a5fa', icon: '📋' },
    Grading:    { bg: 'rgba(22,163,74,0.12)',    color: '#4ade80', icon: '📊' },
    Conduct:    { bg: 'rgba(220,38,38,0.12)',    color: '#f87171', icon: '⚖️' },
    Uniform:    { bg: 'rgba(217,119,6,0.12)',    color: '#fbbf24', icon: '👔' },
    Enrollment: { bg: 'rgba(139,92,246,0.12)',   color: '#c4b5fd', icon: '📝' },
    General:    { bg: 'rgba(100,116,139,0.12)',  color: '#94a3b8', icon: '📄' },
};

function statusBadge(status) {
    const isActive = status === 'Active';
    return `<span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10.5px; font-weight:700;
        background:${isActive ? 'rgba(22,163,74,0.12)' : 'rgba(100,116,139,0.12)'};
        color:${isActive ? '#4ade80' : '#94a3b8'};
        border:1px solid ${isActive ? 'rgba(22,163,74,0.25)' : 'rgba(100,116,139,0.2)'};"
    >${status}</span>`;
}

// ── School Info ───────────────────────────────────────────────────────────────
function loadSchoolInfo() {
    $.ajax({
        url: '{{ route("policies.info") }}', method: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                schoolInfo = response.data;
                document.getElementById('missionText').textContent = schoolInfo.mission || '—';
                document.getElementById('visionText').textContent  = schoolInfo.vision  || '—';

                const grid = document.getElementById('coreValuesGrid');
                grid.innerHTML = '';
                let values = [];
                try { values = JSON.parse(schoolInfo.core_values || '[]'); } catch(e) {}
                if (values.length > 0) {
                    values.forEach(v => {
                        const chip = document.createElement('span');
                        chip.className = 'core-value-chip';
                        chip.textContent = v.trim();
                        grid.appendChild(chip);
                    });
                } else {
                    grid.innerHTML = '<span style="color:var(--dk-t4);font-size:13px;">No values set.</span>';
                }
            }
        }
    });
}

@if(auth()->user()->role->name === 'Admin')
// ── Info Modal ────────────────────────────────────────────────────────────────
var infoModal  = document.getElementById('infoModal');
var postModal  = document.getElementById('postModal');

function openInfoModal(field) {
    const labels = { mission: 'Mission Statement', vision: 'Vision Statement', values: 'Core Values' };
    const titles = { mission: 'Edit Mission', vision: 'Edit Vision', values: 'Edit Core Values' };

    document.getElementById('infoModalTitle').textContent   = titles[field];
    document.getElementById('infoFieldLabel').textContent   = labels[field];
    document.getElementById('infoField').value              = field;
    document.getElementById('coreValuesHint').style.display = field === 'values' ? 'block' : 'none';

    let value = '';
    if (field === 'values') {
        try { value = JSON.parse(schoolInfo.core_values || '[]').join(', '); } catch(e) {}
    } else {
        value = schoolInfo[field] || '';
    }
    document.getElementById('infoText').value = value;
    infoModal.style.display = 'flex';
}

function closeInfoModal() { infoModal.style.display = 'none'; }

$('#infoForm').on('submit', function (e) {
    e.preventDefault();
    const field = $('#infoField').val();
    let   value = $('#infoText').val().trim();

    if (field === 'values') {
        const arr = value.split(',').map(v => v.trim()).filter(Boolean);
        value = JSON.stringify(arr);
    }

    closeInfoModal();
    const loadingEl = document.getElementById('loading-modal');
    if (loadingEl) loadingEl.style.display = 'flex';

    $.ajax({
        url:    '{{ route("admin.policies.info.update") }}',  {{-- FIXED: was policies.info.update --}}
        method: 'POST',
        data:   { field, value },
        success: function (response) {
            if (loadingEl) loadingEl.style.display = 'none';
            if (response.status === 'success') { showPopup('Success', response.message, 'success'); loadSchoolInfo(); }
            else { showPopup('Error', response.message, 'error'); }
        },
        error: function () {
            if (loadingEl) loadingEl.style.display = 'none';
            showPopup('Error', 'An error occurred.', 'error');
        }
    });
});

// ── Post / Edit Modal ─────────────────────────────────────────────────────────
function openEditModal() {
    closeViewModal();
    $('#postModalTitle').text('Edit Policy');
    $('#postSubmitBtn').text('Update Policy');
    $('#editPolicyId').val(currentPolicy.id);
    $('#policyTitle').val(currentPolicy.title);
    $('#policyDescription').val(currentPolicy.description);
    $('#policyCategory').val(currentPolicy.category);
    $('#policyDate').val(currentPolicy.effective_date);
    $('#policyStatus').val(currentPolicy.status);
    postModal.style.display = 'flex';
}

function closePostModal() {
    postModal.style.display = 'none';
    document.getElementById('policyForm').reset();
    $('#postModalTitle').text('Add New Policy');
    $('#postSubmitBtn').text('Save Policy');
    $('#editPolicyId').val('');
}

document.getElementById('openPostModal').addEventListener('click', function () {
    closePostModal();
    postModal.style.display = 'flex';
});

function confirmDelete() {
    showConfirmationModal('Delete Policy', 'Are you sure you want to delete this policy?', function () {
        closeViewModal();
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';
        $.ajax({
            url:    '{{ route("admin.policies.destroy") }}',  {{-- FIXED: was policies.destroy --}}
            method: 'POST',
            data:   { id: currentPolicy.id },
            success: function (response) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (response.status === 'success') { showPopup('Success', response.message, 'success'); loadPolicies(); }
                else { showPopup('Error', response.message, 'error'); }
            },
            error: function () {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', 'An error occurred while deleting.', 'error');
            }
        });
    });
}

$('#policyForm').on('submit', function (e) {
    e.preventDefault();
    const editId = $('#editPolicyId').val();

    if (!$('#policyTitle').val())    { showPopup('Validation', 'Please enter a title.', 'warning'); return; }
    if (!$('#policyCategory').val()) { showPopup('Validation', 'Please select a category.', 'warning'); return; }
    if (!$('#policyDate').val())     { showPopup('Validation', 'Please select an effective date.', 'warning'); return; }

    const data = {
        title:          $('#policyTitle').val(),
        description:    $('#policyDescription').val(),
        category:       $('#policyCategory').val(),
        effective_date: $('#policyDate').val(),
        status:         $('#policyStatus').val(),
    };
    if (editId) data.id = editId;

    closePostModal();
    const loadingEl = document.getElementById('loading-modal');
    if (loadingEl) loadingEl.style.display = 'flex';

    $.ajax({
        // FIXED: was policies.update / policies.store
        url:    editId ? '{{ route("admin.policies.update") }}' : '{{ route("admin.policies.store") }}',
        method: 'POST', data,
        success: function (response) {
            if (loadingEl) loadingEl.style.display = 'none';
            if (response.status === 'success') { showPopup('Success', response.message, 'success'); loadPolicies(); }
            else { showPopup('Error', response.message, 'error'); }
        },
        error: function () {
            if (loadingEl) loadingEl.style.display = 'none';
            showPopup('Error', 'An error occurred. Please try again.', 'error');
        }
    });
});
@endif

// ── Policies List ─────────────────────────────────────────────────────────────
function renderPolicies(data) {
    const container = document.getElementById('policiesContainer');
    container.innerHTML = '';

    if (!data || data.length === 0) {
        container.innerHTML = '<div style="padding:40px; text-align:center; color:var(--dk-t4); font-size:13px;">No policies found.</div>';
        return;
    }

    data.forEach(row => {
        const cfg  = categoryConfig[row.category] || categoryConfig.General;
        const card = document.createElement('div');
        card.className = 'policy-card';
        card.innerHTML = `
            <div class="policy-card-icon" style="background:${cfg.bg}; font-size:18px;">${cfg.icon}</div>
            <div class="policy-card-body">
                <div class="policy-card-title">${row.title}</div>
                <div class="policy-card-meta">
                    <span style="background:${cfg.bg}; color:${cfg.color}; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700;">${row.category}</span>
                    <span>•</span>
                    <span>Effective ${row.effective_date}</span>
                    <span>•</span>
                    ${statusBadge(row.status)}
                </div>
            </div>`;
        card.addEventListener('click', function () {
            openViewModal(row.id, row.title, row.description || '', row.category, row.effective_date, row.status);
        });
        container.appendChild(card);
    });
}

$('#categoryFilter').on('change', function () {
    const filter = $(this).val();
    renderPolicies(filter ? allPolicies.filter(p => p.category === filter) : allPolicies);
});

// ── View Modal ────────────────────────────────────────────────────────────────
function openViewModal(id, title, description, category, effective_date, status) {
    currentPolicy = { id, title, description, category, effective_date, status };
    const cfg = categoryConfig[category] || categoryConfig.General;
    document.getElementById('modalTitle').textContent        = title;
    document.getElementById('modalDescription').textContent  = description || '—';
    document.getElementById('modalDate').textContent         = effective_date;
    document.getElementById('modalStatus').innerHTML         = statusBadge(status);
    document.getElementById('modalIconWrap').textContent     = cfg.icon;
    document.getElementById('modalIconWrap').style.background = cfg.bg;
    document.getElementById('modalCategoryBadge').innerHTML  =
        `<span style="font-size:11px;font-weight:700;color:${cfg.color};background:${cfg.bg};padding:2px 8px;border-radius:10px;">${category}</span>`;
    viewModal.style.display = 'flex';
}
function closeViewModal() { viewModal.style.display = 'none'; }

// ── Load ──────────────────────────────────────────────────────────────────────
function loadPolicies() {
    $.ajax({
        url: '{{ route("policies.list") }}', method: 'GET',
        success: function (response) {
            allPolicies = response.status === 'success' ? response.data : [];
            renderPolicies(allPolicies);
        },
        error: function () {
            document.getElementById('policiesContainer').innerHTML =
                '<div style="padding:40px;text-align:center;color:var(--red-400);font-size:13px;">Failed to load policies.</div>';
        }
    });
}

document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeViewModal(); });
$(document).ready(function () { loadSchoolInfo(); loadPolicies(); });
</script>

@endsection