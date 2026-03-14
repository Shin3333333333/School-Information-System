@extends('layouts.app')

@section('title', 'Sections')

@section('page-title')
    <h2>Section Management</h2>
@endsection

@section('content')

<div class="card">

    <div class="card-toolbar">
        <div class="search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#94a3b8" stroke-width="2"/>
                <path d="m21 21-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input type="text" id="sec-search" placeholder="Search section or grade…" class="search-input" autocomplete="off">
        </div>

        <select id="sec-grade-filter" class="form-select" style="width:180px;">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
            @endforeach
        </select>

        <button class="btn btn-primary" id="btn-add-section">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            Add Section
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Section Name</th>
                    <th>Grade Level</th>
                    <th>Students Enrolled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="section-tbody">
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--gray-400);">
                        Loading…
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination" id="pagination-wrap" style="display:none;">
        <span class="page-info" id="page-info"></span>
        <div class="page-buttons" id="page-buttons"></div>
    </div>

</div>

{{-- Modal — completely isolated from page event system --}}
<div id="sec-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:10000; align-items:center; justify-content:center;">
    <div id="sec-modal-box" style="background:#fff; border-radius:14px; width:100%; max-width:460px; box-shadow:0 10px 40px rgba(0,0,0,.15); padding:28px; margin:16px;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 id="sec-modal-title" style="font-size:15px; font-weight:600; color:#0f172a; margin:0;">Add Section</h3>
            <button id="btn-modal-close" style="border:none; background:none; cursor:pointer; color:#94a3b8; padding:4px; font-size:18px; line-height:1;">✕</button>
        </div>

        <div id="sec-modal-error" style="display:none; background:#fee2e2; color:#dc2626; padding:10px 14px; border-radius:6px; font-size:13px; font-weight:600; margin-bottom:14px;"></div>

        <input type="hidden" id="sec-edit-id">

        <div style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">
                    SECTION NAME <span style="color:#dc2626">*</span>
                </label>
                <input type="text" id="sec-name-input" placeholder="e.g. Rizal, Bonifacio…" maxlength="50"
                    style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; box-sizing:border-box;">
            </div>
            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">
                    GRADE LEVEL <span style="color:#dc2626">*</span>
                </label>
                <select id="sec-grade-input"
                    style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select grade level</option>
                    @foreach($gradeLevels as $gl)
                        <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button id="btn-modal-cancel" style="padding:8px 16px; border:1.5px solid #e2e8f0; background:#fff; color:#475569; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Cancel</button>
            <button id="btn-modal-save" style="padding:8px 16px; background:#2563eb; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Save Section</button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    var currentPage  = 1;
    var searchTimer  = null;
    var CSRF         = '{{ csrf_token() }}';
    var URL_LIST     = '{{ route("admin.sections.list") }}';
    var URL_STORE    = '{{ route("admin.sections.store") }}';
    var URL_UPDATE   = '{{ route("admin.sections.update") }}';
    var URL_DESTROY  = '{{ route("admin.sections.destroy") }}';

    // ── Modal elements ────────────────────────────────────────────
    var backdrop   = document.getElementById('sec-modal-backdrop');
    var modalBox   = document.getElementById('sec-modal-box');
    var modalTitle = document.getElementById('sec-modal-title');
    var modalError = document.getElementById('sec-modal-error');
    var editId     = document.getElementById('sec-edit-id');
    var nameInput  = document.getElementById('sec-name-input');
    var gradeInput = document.getElementById('sec-grade-input');
    var saveBtn    = document.getElementById('btn-modal-save');

    // ── Open / Close ──────────────────────────────────────────────
    function openModal(title, saveTxt, id, name, gradeId) {
        modalTitle.textContent  = title;
        saveBtn.textContent     = saveTxt;
        editId.value            = id   || '';
        nameInput.value         = name || '';
        gradeInput.value        = gradeId || '';
        modalError.style.display = 'none';
        backdrop.style.display  = 'flex';
        setTimeout(function() { nameInput.focus(); }, 50);
    }

    function closeModal() {
        backdrop.style.display = 'none';
    }

    // ── Backdrop click — only close if clicking backdrop itself ───
    backdrop.addEventListener('click', function(e) {
        if (e.target === backdrop) closeModal();
    });

    // ── Button wiring ─────────────────────────────────────────────
    document.getElementById('btn-add-section').addEventListener('click', function() {
        openModal('Add Section', 'Save Section', '', '', '');
    });

    document.getElementById('btn-modal-close').addEventListener('click', closeModal);
    document.getElementById('btn-modal-cancel').addEventListener('click', closeModal);

    document.getElementById('btn-modal-save').addEventListener('click', function() {
        var id      = editId.value;
        var name    = nameInput.value.trim();
        var gradeId = gradeInput.value;

        modalError.style.display = 'none';

        if (!name)    { showError('Section name is required.');    return; }
        if (!gradeId) { showError('Please select a grade level.'); return; }

        var url  = id ? URL_UPDATE : URL_STORE;
        var data = { section_name: name, grade_level_id: gradeId, _token: CSRF };
        if (id) data.id = id;

        saveBtn.disabled    = true;
        saveBtn.textContent = 'Saving…';

        $.ajax({
            url: url, method: 'POST', data: data,
            success: function(res) {
                closeModal();
                showPopup('Success', res.message, 'success');
                loadSections(currentPage);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong.';
                showError(msg);
            },
            complete: function() {
                saveBtn.disabled    = false;
                saveBtn.textContent = id ? 'Update Section' : 'Save Section';
            }
        });
    });

    function showError(msg) {
        modalError.textContent   = msg;
        modalError.style.display = 'block';
    }

    // ── Load sections ─────────────────────────────────────────────
    function loadSections(page) {
        currentPage = page || 1;
        var search  = document.getElementById('sec-search').value.trim();
        var gradeId = document.getElementById('sec-grade-filter').value;

        document.getElementById('section-tbody').innerHTML =
            '<tr><td colspan="5" style="text-align:center;padding:40px;">' +
                '<div style="display:flex;align-items:center;justify-content:center;gap:8px;color:#94a3b8;">' +
                    'Loading<span class="loading loading-dots loading-md"></span>' +
                '</div>' +
            '</td></tr>';

        $.ajax({
            url: URL_LIST,
            data: { page: currentPage, search: search, grade_level_id: gradeId },
            success: function(res) {
                renderTable(res);
                renderPagination(res);
            },
            error: function() {
                document.getElementById('section-tbody').innerHTML =
                    '<tr><td colspan="5" style="text-align:center;padding:40px;color:#dc2626;">Failed to load sections.</td></tr>';
            }
        });
    }

    // ── Render table ──────────────────────────────────────────────
    function renderTable(res) {
        var tbody  = document.getElementById('section-tbody');
        var offset = (res.current_page - 1) * res.per_page;

        if (!res.data || !res.data.length) {
            tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><h3>No sections found</h3><p>Try adjusting your search or add a new section.</p></div></td></tr>';
            return;
        }

        tbody.innerHTML = res.data.map(function(row, i) {
            return '<tr>' +
                '<td class="cell-date">' + (offset + i + 1) + '</td>' +
                '<td><span class="detail-text">' + esc(row.section_name) + '</span></td>' +
                '<td><span class="status-badge status-active">' + esc(row.grade_level_name) + '</span></td>' +
                '<td><span style="font-weight:600;color:#334155;">' + row.student_enrolled + '</span> <span style="font-size:12px;color:#94a3b8;">students</span></td>' +
                '<td><div style="display:flex;gap:6px;">' +
                    '<button class="btn btn-outline" style="padding:5px 10px;font-size:12px;" data-action="edit" data-id="' + row.id + '" data-name="' + esc(row.section_name) + '" data-grade="' + row.grade_level_id + '">Edit</button>' +
                    '<button class="btn" style="padding:5px 10px;font-size:12px;background:#fee2e2;color:#dc2626;border-color:#fee2e2;" data-action="delete" data-id="' + row.id + '" data-name="' + esc(row.section_name) + '" data-enrolled="' + row.student_enrolled + '">Delete</button>' +
                '</div></td>' +
            '</tr>';
        }).join('');

        // Attach action buttons via delegation — no inline onclick
        tbody.querySelectorAll('[data-action="edit"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                openModal('Edit Section', 'Update Section',
                    this.dataset.id, this.dataset.name, this.dataset.grade);
            });
        });

        tbody.querySelectorAll('[data-action="delete"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var id       = this.dataset.id;
                var name     = this.dataset.name;
                var enrolled = parseInt(this.dataset.enrolled);
                if (enrolled > 0) {
                    showPopup('Cannot Delete', '"' + name + '" has ' + enrolled + ' enrolled student(s).', 'warning');
                    return;
                }
                showConfirmationModal('Delete Section', 'Are you sure you want to delete "' + name + '"?', function() {
                    $.ajax({
                        url: URL_DESTROY, method: 'POST',
                        data: { id: id, _token: CSRF },
                        success: function(res) {
                            showPopup('Deleted', res.message, 'success');
                            loadSections(currentPage);
                        },
                        error: function(xhr) {
                            showPopup('Error', (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed.', 'error');
                        }
                    });
                });
            });
        });
    }

    // ── Render pagination ─────────────────────────────────────────
    function renderPagination(res) {
        var wrap    = document.getElementById('pagination-wrap');
        var info    = document.getElementById('page-info');
        var buttons = document.getElementById('page-buttons');

        if (res.last_page <= 1) { wrap.style.display = 'none'; return; }

        wrap.style.display = 'flex';
        var from = (res.current_page - 1) * res.per_page + 1;
        var to   = Math.min(res.current_page * res.per_page, res.total);
        info.textContent = 'Showing ' + from + '–' + to + ' of ' + res.total;

        var html = '';
        for (var p = 1; p <= res.last_page; p++) {
            html += '<button class="page-btn' + (p === res.current_page ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
        }
        buttons.innerHTML = html;

        buttons.querySelectorAll('.page-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                loadSections(parseInt(this.dataset.page));
            });
        });
    }

    // ── Helpers ───────────────────────────────────────────────────
    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Init ──────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        loadSections(1);

        document.getElementById('sec-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() { loadSections(1); }, 350);
        });

        document.getElementById('sec-grade-filter').addEventListener('change', function() {
            loadSections(1);
        });
    });

})();
</script>
@endpush