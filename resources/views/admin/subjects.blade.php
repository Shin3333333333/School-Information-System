{{-- resources/views/admin/subjects.blade.php --}}
@extends('layouts.app')

@section('title', 'Subject Management')

@section('page-title')
<h2>Subject Management</h2>
@endsection

@push('styles')
<style>
    .subjects-header {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .subjects-header h2 {
        font-family: var(--font-display); font-size: 1.25rem;
        font-weight: 700; color: var(--dk-t1); margin: 0 0 4px;
    }
    .subjects-header p { margin: 0; font-size: 0.82rem; color: var(--dk-t3); }

    /* Table card */
    .table-card { background: var(--dk-surface); border: 1px solid var(--dk-b1); border-radius: var(--radius-lg); overflow: hidden; }
    .table-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; border-bottom: 1px solid var(--dk-b2);
        flex-wrap: wrap; gap: 10px; background: var(--dk-surface);
    }
    .table-toolbar .left { display: flex; align-items: center; gap: 10px; }
    .search-input {
        padding: 7px 12px 7px 34px;
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem; color: var(--dk-t2);
        background: var(--dk-surface2) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center;
        outline: none; width: 220px; transition: border-color .2s, box-shadow .2s;
    }
    .search-input::placeholder { color: var(--dk-t4); }
    .search-input:focus { border-color: rgba(96,165,250,0.4); box-shadow: 0 0 0 3px rgba(96,165,250,0.08); }
    .record-count { font-size: 0.78rem; color: var(--dk-t4); }
    .btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem; font-weight: 600;
        cursor: pointer; transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }
    .btn-add:hover { background: var(--blue-700); transform: translateY(-1px); }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    .data-table thead tr { background: var(--dk-surface2); }
    .data-table thead th {
        padding: 11px 16px; text-align: left;
        font-size: 0.72rem; font-weight: 700; color: var(--dk-t4);
        text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 1.5px solid var(--dk-b2); white-space: nowrap;
    }
    .data-table tbody tr { border-bottom: 1px solid var(--dk-b2); background: var(--dk-surface); transition: background .12s; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255,255,255,0.03); }
    .data-table td { padding: 12px 16px; color: var(--dk-t2); vertical-align: middle; }

    /* Action buttons */
    .action-btns { display: flex; gap: 6px; }
    .btn-icon {
        width: 30px; height: 30px; border: none; border-radius: 7px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all .18s;
    }
    .btn-icon.edit         { background: rgba(37,99,235,0.15); color: #60a5fa; }
    .btn-icon.edit:hover   { background: var(--blue-600);       color: #fff; }
    .btn-icon.delete       { background: rgba(220,38,38,0.15);  color: #f87171; }
    .btn-icon.delete:hover { background: var(--red-600);        color: #fff; }

    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; color: var(--dk-t4); }
    .empty-state svg { margin-bottom: 12px; opacity: .3; }
    .empty-state p { font-size: 0.88rem; margin: 0; }

    /* Skeleton */
    .skeleton-cell {
        height: 14px; border-radius: 6px;
        background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 75%);
        background-size: 200% 100%; animation: shimmer 1.4s infinite;
    }
    @keyframes shimmer { to { background-position: -200% 0; } }

    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.65);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; backdrop-filter: blur(3px);
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl); width: 100%; max-width: 420px;
        box-shadow: var(--shadow-xl); animation: modalIn .22s ease-out;
    }
    @keyframes modalIn { from { opacity:0; transform:translateY(20px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px 16px; border-bottom: 1px solid var(--dk-b2);
    }
    .modal-header h3 { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--dk-t1); margin: 0; }
    .modal-close {
        width: 30px; height: 30px; border: none; background: rgba(255,255,255,0.06);
        border-radius: 8px; cursor: pointer; font-size: 1rem; color: var(--dk-t3);
        display: flex; align-items: center; justify-content: center; transition: all .18s;
    }
    .modal-close:hover { background: rgba(255,255,255,0.1); color: var(--dk-t1); }
    .modal-body   { padding: 20px 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid var(--dk-b2); display: flex; justify-content: flex-end; gap: 10px; }

    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 0.76rem; font-weight: 700; color: var(--dk-t3); margin-bottom: 5px; text-transform: uppercase; letter-spacing: .04em; }
    .form-control {
        width: 100%; padding: 9px 12px; border: 1.5px solid var(--dk-b1);
        border-radius: var(--radius-md); font-family: var(--font-body);
        font-size: 0.84rem; color: var(--dk-t2); background: var(--dk-surface2);
        outline: none; transition: border-color .2s, box-shadow .2s; box-sizing: border-box;
    }
    .form-control:focus { border-color: rgba(96,165,250,0.4); box-shadow: 0 0 0 3px rgba(96,165,250,0.08); }
    .form-control::placeholder { color: var(--dk-t4); }
    .form-control.is-invalid { border-color: var(--red-500); }
    .invalid-feedback { font-size: 0.74rem; color: var(--red-400); margin-top: 3px; display: none; }
    .form-control.is-invalid + .invalid-feedback { display: block; }

    .btn-cancel {
        padding: 9px 20px; background: rgba(255,255,255,0.05); color: var(--dk-t3);
        border: 1.5px solid var(--dk-b1); border-radius: var(--radius-md);
        font-family: var(--font-body); font-size: 0.84rem; font-weight: 600;
        cursor: pointer; transition: all .18s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,0.09); color: var(--dk-t1); }
    .btn-save {
        padding: 9px 24px; background: var(--blue-600); color: #fff;
        border: none; border-radius: var(--radius-md); font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 700; cursor: pointer;
        transition: background .18s, transform .15s;
        display: inline-flex; align-items: center; gap: 6px;
        box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }
    .btn-save:hover    { background: var(--blue-700); transform: translateY(-1px); }
    .btn-save:disabled { background: rgba(37,99,235,0.3); color: rgba(255,255,255,0.4); cursor: not-allowed; transform: none; box-shadow: none; }

    /* Delete confirm */
    .confirm-box {
        background: var(--dk-surface); border: 1px solid var(--dk-b1);
        border-radius: var(--radius-xl); width: 100%; max-width: 380px;
        padding: 28px 28px 22px; text-align: center; box-shadow: var(--shadow-xl);
        animation: modalIn .22s ease-out;
    }
    .confirm-icon { width: 52px; height: 52px; background: rgba(220,38,38,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin: 0 auto 14px; }
    .confirm-box h3 { font-size: 1rem; font-weight: 700; color: var(--dk-t1); margin: 0 0 6px; }
    .confirm-box p  { font-size: 0.84rem; color: var(--dk-t3); margin: 0 0 22px; }
    .confirm-actions { display: flex; justify-content: center; gap: 10px; }
    .btn-danger {
        padding: 9px 24px; background: var(--red-600); color: #fff;
        border: none; border-radius: var(--radius-md); font-family: var(--font-body);
        font-size: 0.84rem; font-weight: 700; cursor: pointer;
        transition: background .18s, transform .15s;
        box-shadow: 0 2px 8px rgba(220,38,38,0.3);
    }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-1px); }
</style>
@endpush

@section('content')

<div class="subjects-header">
    <div>
        <h2>Subject Management</h2>
        <p>Add, edit, and remove subjects offered in the school.</p>
    </div>
    <button class="btn-add" id="btnAddSubject">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
        </svg>
        Add Subject
    </button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <div class="left">
            <input type="text" class="search-input" id="subjectSearch" placeholder="Search subject name…">
            <span class="record-count" id="recordCount"></span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject Name</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody id="subjectsBody">
                <tr>
                    <td colspan="3" style="text-align:center; padding:48px;">
                        <span style="display:inline-flex; align-items:center; gap:8px; color:var(--dk-t4); font-size:13px;">
                            Loading subjects
                            <span class="loading loading-dots loading-sm" style="color:#60a5fa;"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Add / Edit Modal --}}
<div class="modal-overlay" id="subjectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add Subject</h3>
            <button class="modal-close" id="btnModalClose">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="subjectId">
            <div class="form-group">
                <label>Subject Name <span style="color:var(--red-400);">*</span></label>
                <input type="text" class="form-control" id="modalSubjectName"
                       placeholder="e.g. Mathematics, Filipino, Science" maxlength="120">
                <div class="invalid-feedback">Subject name is required.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancelModal">Cancel</button>
            <button class="btn-save" id="btnSaveSubject">
                <svg id="btnSaveIcon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <path d="M17 21v-8H7v8M7 3v5h8"/>
                </svg>
                <span class="loading loading-spinner" id="btnSaveLoader" style="width:13px;height:13px;display:none;color:#fff;"></span>
                <span id="btnSaveLabel">Save Subject</span>
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">🗑️</div>
        <h3>Delete Subject?</h3>
        <p>This action <strong>cannot be undone</strong>. The subject will be permanently removed.</p>
        <div class="confirm-actions">
            <button class="btn-cancel" id="btnCancelDelete">Cancel</button>
            <button class="btn-danger" id="btnConfirmDelete">
                <span class="loading loading-spinner" id="btnDeleteLoader" style="width:13px;height:13px;display:none;color:#fff;"></span>
                <span id="btnDeleteLabel">Yes, Delete</span>
            </button>
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
        list   : '{{ route("admin.subjects.list") }}',
        store  : '{{ route("admin.subjects.store") }}',
        update : '{{ route("admin.subjects.update") }}',
        destroy: '{{ route("admin.subjects.destroy") }}',
    };

    let allRows   = [];
    let deleteId  = null;
    let isEditing = false;

    const $tbody       = $('#subjectsBody');
    const $search      = $('#subjectSearch');
    const $recordCount = $('#recordCount');

    // ── Render ────────────────────────────────────────────────────────────────
    function renderTable(rows) {
        if (!rows.length) {
            $tbody.html(`<tr><td colspan="3">
                <div class="empty-state">
                    <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    <p>No subjects found.</p>
                </div>
            </td></tr>`);
            $recordCount.text('0 subjects');
            if (window.loadingModal) window.loadingModal.hide();
            return;
        }

        $tbody.html(rows.map((r, i) => `
            <tr data-id="${r.id}">
                <td style="color:var(--dk-t4);font-size:.8rem;">${i + 1}</td>
                <td><strong style="color:var(--dk-t1);">${r.subject_name}</strong></td>
                <td>
                    <div class="action-btns">
                        <button class="btn-icon edit" title="Edit" onclick="SubjectsAdmin.openEdit(${r.id}, '${r.subject_name.replace(/'/g, "\\'")}')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn-icon delete" title="Delete" onclick="SubjectsAdmin.confirmDelete(${r.id})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>`).join(''));

        $recordCount.text(`${rows.length} subject${rows.length !== 1 ? 's' : ''}`);

        // Hide full-screen loading modal once table is rendered
        if (window.loadingModal) window.loadingModal.hide();
    }

    // ── Load ──────────────────────────────────────────────────────────────────
    function loadSubjects() {
        $.ajax({
            url: ROUTES.list, type: 'GET', dataType: 'json',
            success: function (res) {
                if (res.status !== 'success') { showPopup('Error', res.message ?? 'Failed to load.', 'error'); return; }
                allRows = res.data ?? [];
                applySearch();
            },
            error: function (xhr) {
                if (window.loadingModal) window.loadingModal.hide();
                showPopup('Error', xhr.responseJSON?.message ?? 'Network error.', 'error');
            }
        });
    }

    function applySearch() {
        const term = ($search.val() ?? '').toLowerCase().trim();
        renderTable(term ? allRows.filter(r => r.subject_name.toLowerCase().includes(term)) : allRows);
    }

    $search.on('input', applySearch);

    // ── Modal helpers ─────────────────────────────────────────────────────────
    const $modal       = $('#subjectModal');
    const $deleteModal = $('#deleteModal');

    function openModal()  { $modal.addClass('open'); }
    function closeModal() { $modal.removeClass('open'); $('#modalSubjectName').removeClass('is-invalid'); }

    $('#btnAddSubject').on('click', function () {
        isEditing = false;
        $('#modalTitle').text('Add Subject');
        $('#btnSaveLabel').text('Save Subject');
        $('#subjectId').val('');
        $('#modalSubjectName').val('').removeClass('is-invalid');
        openModal();
        setTimeout(() => $('#modalSubjectName').focus(), 80);
    });

    $('#btnModalClose, #btnCancelModal').on('click', closeModal);
    $modal.on('click', function (e) { if ($(e.target).is($modal)) closeModal(); });

    // ── Save ──────────────────────────────────────────────────────────────────
    $('#btnSaveSubject').on('click', function () {
        const name = $('#modalSubjectName').val().trim();
        if (!name) { $('#modalSubjectName').addClass('is-invalid'); return; }
        $('#modalSubjectName').removeClass('is-invalid');

        const editing  = isEditing;
        const payload  = { subject_name: name };
        if (editing) payload.id = parseInt($('#subjectId').val());

        const $btn = $(this).prop('disabled', true);
        $('#btnSaveIcon').hide();
        $('#btnSaveLoader').css('display', 'inline-block');
        $('#btnSaveLabel').text(editing ? 'Updating…' : 'Saving…');

        $.ajax({
            url        : editing ? ROUTES.update : ROUTES.store,
            type       : 'POST',
            contentType: 'application/json',
            data       : JSON.stringify(payload),
            dataType   : 'json',
            success    : function (res) {
                $('#btnSaveIcon').show(); $('#btnSaveLoader').hide();
                $('#btnSaveLabel').text(editing ? 'Update Subject' : 'Save Subject');
                $btn.prop('disabled', false);
                if (res.status === 'success') {
                    closeModal();
                    if (window.loadingModal) window.loadingModal.show();
                    loadSubjects();
                    showPopup('Success', res.message ?? 'Saved.', 'success');
                } else {
                    showPopup('Error', res.message ?? 'Failed.', 'error');
                }
            },
            error: function (xhr) {
                $('#btnSaveIcon').show(); $('#btnSaveLoader').hide();
                $('#btnSaveLabel').text(editing ? 'Update Subject' : 'Save Subject');
                $btn.prop('disabled', false);
                showPopup('Error', xhr.responseJSON?.message ?? 'Server error.', 'error');
            }
        });
    });

    // ── Delete ────────────────────────────────────────────────────────────────
    $('#btnCancelDelete').on('click', function () { $deleteModal.removeClass('open'); deleteId = null; });
    $deleteModal.on('click', function (e) { if ($(e.target).is($deleteModal)) { $deleteModal.removeClass('open'); deleteId = null; } });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteId) return;
        const $btn = $(this).prop('disabled', true);
        $('#btnDeleteLoader').css('display', 'inline-block');
        $('#btnDeleteLabel').text('Deleting…');

        $.ajax({
            url: ROUTES.destroy, type: 'POST', contentType: 'application/json',
            data: JSON.stringify({ id: deleteId }), dataType: 'json',
            success: function (res) {
                $('#btnDeleteLoader').hide(); $('#btnDeleteLabel').text('Yes, Delete');
                $btn.prop('disabled', false);
                $deleteModal.removeClass('open'); deleteId = null;
                if (res.status === 'success') {
                    if (window.loadingModal) window.loadingModal.show();
                    loadSubjects();
                    showPopup('Deleted', res.message ?? 'Subject removed.', 'success');
                } else {
                    showPopup('Error', res.message ?? 'Delete failed.', 'error');
                }
            },
            error: function (xhr) {
                $('#btnDeleteLoader').hide(); $('#btnDeleteLabel').text('Yes, Delete');
                $btn.prop('disabled', false);
                showPopup('Error', xhr.responseJSON?.message ?? 'Server error.', 'error');
            }
        });
    });

    // Enter key submits modal
    $('#modalSubjectName').on('keydown', function (e) { if (e.key === 'Enter') $('#btnSaveSubject').trigger('click'); });

    // ── Expose for inline onclick ─────────────────────────────────────────────
    window.SubjectsAdmin = {
        openEdit: function (id, name) {
            isEditing = true;
            $('#modalTitle').text('Edit Subject');
            $('#btnSaveLabel').text('Update Subject');
            $('#subjectId').val(id);
            $('#modalSubjectName').val(name).removeClass('is-invalid');
            openModal();
            setTimeout(() => $('#modalSubjectName').focus(), 80);
        },
        confirmDelete: function (id) {
            deleteId = id;
            $deleteModal.addClass('open');
        }
    };

    // ── Init ──────────────────────────────────────────────────────────────────
    loadSubjects();
});
</script>
@endpush