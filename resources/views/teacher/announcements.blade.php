@extends('layouts.app')

@section('title', 'Announcements — School Information System')

@section('page-title')
<h2>Announcements</h2>
@endsection

@section('content')

{{-- Post Announcement Button --}}
<div class="dashboard-widgets mb-6">
    <div style="width: max-content;">
        <button class="btn btn-primary" id="openPostModal">Post Announcement</button>
    </div>
</div>

{{-- Announcements Table --}}
<div class="card card-body dashboard-widget-card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date Posted</th>
                <th>Title</th>
                <th>Description</th>
                <th>Subject</th>
                <!-- <th>Grade Level</th>
                <th>Sections</th> -->
            </tr>
        </thead>
        <tbody id="announcementsTable">
            <tr>
                <td colspan="6" style="text-align:center;">
                    <span style="display:inline-flex; align-items:center; gap:6px; color:#94a3b8; font-size:13px;">
                        Loading <span class="loading loading-dots loading-sm"></span>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- VIEW MODAL --}}
<div id="viewModal" style="display:none; position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:10px; width:400px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.3);">
        <h3 style="margin-bottom:15px; font-weight:600;">Announcement Details</h3>
        <div id="modalContent" style="margin-bottom:15px;">
            <p><strong>Title:</strong> <span id="modalTitle"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
            <p><strong>Grade Level:</strong> <span id="modalGradeLevel"></span></p>
            <p><strong>Sections:</strong> <span id="modalSections"></span></p>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary" onclick="openEditModal()">Edit</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

{{-- POST MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:var(--radius-lg); width:400px; padding:20px; box-shadow:var(--shadow-md);">
        <h3 class="section-title" id="postModalTitle" style="margin-bottom:15px;">Post New Announcement</h3>
        <form id="postForm">
            <input type="hidden" id="editAnnouncementId" value="">

            <label class="filter-label mb-1">Subject</label>
            <select id="subjectSelect" name="subject_id" class="form-select mb-3">
                <option value="">— Select Subject —</option>
            </select>

            <label class="filter-label mb-1">Title</label>
            <input type="text" name="title" id="postTitle" class="form-input mb-3">

            <label class="filter-label mb-1">Description</label>
            <textarea name="description" id="postDescription" class="form-input mb-3" rows="4"></textarea>

            {{-- Grade Level Filter --}}
            <label class="filter-label mb-1">
                Filter by Grade Level
                <span style="font-size:11px; font-weight:400; color:var(--gray-400); margin-left:4px;">(filters sections)</span>
            </label>
            <div id="gradeLevelCheckboxes" style="max-height:120px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:6px; padding:10px; margin-bottom:12px;">
                <div id="gradeLevelLoader" style="display:flex; align-items:center; gap:6px; color:#94a3b8; font-size:13px;">
                    Loading <span class="loading loading-dots loading-sm"></span>
                </div>
                <div id="gradeLevelGrid" style="display:none; grid-template-columns: 1fr 1fr; gap:8px;"></div>
            </div>

            {{-- Sections --}}
            <label class="filter-label mb-1">Sections</label>
            <div id="sectionCheckboxes" style="max-height:150px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:6px; padding:10px; margin-bottom:12px;">
                <div id="sectionLoader" style="display:flex; align-items:center; gap:6px; color:#94a3b8; font-size:13px;">
                    Loading <span class="loading loading-dots loading-sm"></span>
                </div>
                <div id="sectionGrid" style="display:none; grid-template-columns: 1fr 1fr; gap:8px;"></div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn btn-outline" onclick="closePostModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="postSubmitBtn">Post</button>
            </div>
        </form>
    </div>
</div>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const viewModal        = document.getElementById('viewModal');
    const postModal        = document.getElementById('postModal');
    const modalTitle       = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalSubject     = document.getElementById('modalSubject');
    const modalSections    = document.getElementById('modalSections');

    let currentAnnouncement = {};

    // ── View Modal ────────────────────────────────────────────────────────────
    function openViewModal(id, title, description, subject_name, subject_id, section_names, grade_level_names) {
        currentAnnouncement = { id, title, description, subject_name, subject_id, section_names, grade_level_names };
        modalTitle.textContent                                  = title;
        modalDescription.textContent                           = description;
        modalSubject.textContent                               = subject_name;
        modalSections.textContent                              = section_names     || '—';
        document.getElementById('modalGradeLevel').textContent = grade_level_names || '—';
        viewModal.style.display = 'flex';
    }

    function closeViewModal() { viewModal.style.display = 'none'; }

    // ── Post/Edit Modal ───────────────────────────────────────────────────────
    function closePostModal() {
        postModal.style.display = 'none';
        document.getElementById('postForm').reset();
        $('#postModalTitle').text('Post New Announcement');
        $('#postSubmitBtn').text('Post');
        $('#editAnnouncementId').val('');
        $('#sectionLoader').show();
        $('#sectionGrid').hide().html('');
        $('#gradeLevelLoader').show();
        $('#gradeLevelGrid').hide().html('');
        $('#subjectSelect').find('option:not(:first)').remove();
        $(document).off('change', '.grade-level-filter');
    }

    // ── Render Sections ───────────────────────────────────────────────────────
    function renderSections(sections, preselectedSectionNames = []) {
        const grid = $('#sectionGrid');
        grid.html('');
        if (sections.length === 0) {
            grid.html('<span style="color:var(--gray-400); font-size:13px; grid-column:span 2;">No sections for selected grade level.</span>');
            return;
        }
        $.each(sections, function (i, section) {
            const isChecked = preselectedSectionNames.includes(section.section_name) ? 'checked' : '';
            grid.append(`
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" name="section_ids[]" value="${section.id}" ${isChecked}>
                    ${section.section_name}
                </label>
            `);
        });
    }

    // ── Load Modal Fields ─────────────────────────────────────────────────────
    function loadModalFields(preselectedSubjectId = null, preselectedSectionNames = [], preselectedGradeLevelNames = []) {
        // Load subjects
        $.ajax({
            url:    '{{ route("announcements.index") }}',
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    const select = $('#subjectSelect');
                    select.find('option:not(:first)').remove();
                    $.each(response.data, function (i, subject) {
                        select.append(`<option value="${subject.id}">${subject.subject_name}</option>`);
                    });
                    if (preselectedSubjectId) select.val(preselectedSubjectId);
                } else {
                    showPopup('Error', 'Failed to load subjects.', 'error');
                }
            },
            error: function () {
                showPopup('Error', 'An error occurred while loading subjects.', 'error');
            }
        });

        // Load all sections (stored for filtering)
        $.ajax({
            url:    '{{ route("fields.sections") }}',
            method: 'GET',
            success: function (response) {
                const loader = $('#sectionLoader');
                const grid   = $('#sectionGrid');
                if (response.status === 'success') {
                    grid.data('allSections', response.data);

                    // If editing with preselected grade levels, filter immediately
                    if (preselectedGradeLevelNames.length > 0) {
                        // We filter after grade levels load — store for later
                        grid.data('pendingGradeLevelFilter', preselectedGradeLevelNames);
                    }

                    renderSections(response.data, preselectedSectionNames);
                    loader.hide();
                    grid.css('display', 'grid');
                } else {
                    loader.html('<span style="color:red;">Failed to load sections.</span>');
                }
            },
            error: function () {
                $('#sectionLoader').html('<span style="color:red;">Error loading sections.</span>');
            }
        });

        // Load grade levels as filters
        $.ajax({
            url:    '{{ route("fields.gradeLevels") }}',
            method: 'GET',
            success: function (response) {
                const loader = $('#gradeLevelLoader');
                const grid   = $('#gradeLevelGrid');
                if (response.status === 'success') {
                    grid.html('');
                    $.each(response.data, function (i, gradeLevel) {
                        // Pre-check grade levels that match the announcement's grade levels when editing
                        const isChecked = preselectedGradeLevelNames.includes(gradeLevel.grade_level_name) ? 'checked' : '';
                        grid.append(`
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px;">
                                <input type="checkbox" class="grade-level-filter" value="${gradeLevel.id}" ${isChecked}>
                                ${gradeLevel.grade_level_name}
                            </label>
                        `);
                    });
                    loader.hide();
                    grid.css('display', 'grid');

                    // If editing, trigger filter immediately to show only relevant sections
                    if (preselectedGradeLevelNames.length > 0) {
                        const checkedGrades = $('.grade-level-filter:checked').map(function () {
                            return parseInt(this.value);
                        }).get();
                        const allSections = $('#sectionGrid').data('allSections') || [];
                        const filtered = allSections.filter(s => checkedGrades.includes(parseInt(s.grade_level_id)));
                        renderSections(filtered, preselectedSectionNames);
                    }

                    // Filter sections on grade level checkbox change
                    $(document).on('change', '.grade-level-filter', function () {
                        const checkedGrades = $('.grade-level-filter:checked').map(function () {
                            return parseInt(this.value);
                        }).get();

                        const allSections = $('#sectionGrid').data('allSections') || [];

                        const currentCheckedIds = $('input[name="section_ids[]"]:checked').map(function () {
                            return this.value;
                        }).get();

                        const filtered = checkedGrades.length > 0
                            ? allSections.filter(s => checkedGrades.includes(parseInt(s.grade_level_id)))
                            : allSections;

                        const currentCheckedNames = currentCheckedIds.map(id =>
                            allSections.find(s => s.id == id)?.section_name
                        ).filter(Boolean);

                        renderSections(filtered, currentCheckedNames);
                    });

                } else {
                    loader.html('<span style="color:red;">Failed to load grade levels.</span>');
                }
            },
            error: function () {
                $('#gradeLevelLoader').html('<span style="color:red;">Error loading grade levels.</span>');
            }
        });
    }

    document.getElementById('openPostModal').addEventListener('click', function () {
        postModal.style.display = 'flex';
        loadModalFields();
    });

    function openEditModal() {
        closeViewModal();

        $('#postModalTitle').text('Edit Announcement');
        $('#postSubmitBtn').text('Update');
        $('#editAnnouncementId').val(currentAnnouncement.id);
        $('#postTitle').val(currentAnnouncement.title);
        $('#postDescription').val(currentAnnouncement.description);

        postModal.style.display = 'flex';

        const sectionNames = currentAnnouncement.section_names
            ? currentAnnouncement.section_names.split(', ').map(s => s.trim())
            : [];

        // Pass grade level names so filters get pre-checked and sections get pre-filtered
        const gradeLevelNames = currentAnnouncement.grade_level_names
            ? currentAnnouncement.grade_level_names.split(', ').map(g => g.trim())
            : [];

        loadModalFields(currentAnnouncement.subject_id, sectionNames, gradeLevelNames);
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    function confirmDelete() {
        showConfirmationModal(
            'Delete Announcement',
            'Are you sure you want to delete this announcement?',
            function () {
                closeViewModal();
                loadingModal.show();
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        $.ajax({
                            url:    '{{ route("announcements.destroy") }}',
                            method: 'POST',
                            data:   { id: currentAnnouncement.id },
                            success: function (response) {
                                loadingModal.hide();
                                setTimeout(function() {
                                    if (response.status === 'success') {
                                        showPopup('Success', response.message, 'success');
                                        loadAnnouncements();
                                    } else {
                                        showPopup('Error', response.message, 'error');
                                    }
                                }, 100);
                            },
                            error: function () {
                                loadingModal.hide();
                                setTimeout(function() {
                                    showPopup('Error', 'An error occurred while deleting.', 'error');
                                }, 100);
                            }
                        });
                    });
                });
            }
        );
    }

    // ── Form Submit ───────────────────────────────────────────────────────────
    $('#postForm').on('submit', function (e) {
        e.preventDefault();

        const checkedSections = $('input[name="section_ids[]"]:checked');
        const editId          = $('#editAnnouncementId').val();

        if (!$('#subjectSelect').val()) {
            showPopup('Validation', 'Please select a subject.', 'warning');
            return;
        }
        if (checkedSections.length === 0) {
            showPopup('Validation', 'Please select at least one section.', 'warning');
            return;
        }

        const data = {
            title:       $('#postTitle').val(),
            description: $('#postDescription').val(),
            subject_id:  $('#subjectSelect').val(),
            date_posted: new Date().toISOString().slice(0, 10),
            section_ids: checkedSections.map(function() { return this.value; }).get()
        };

        if (editId) data.id = editId;

        closePostModal();
        loadingModal.show();

        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                $.ajax({
                    url:    editId ? '{{ route("announcements.update") }}' : '{{ route("announcements.store") }}',
                    method: 'POST',
                    data:   data,
                    success: function (response) {
                        loadingModal.hide();
                        setTimeout(function() {
                            if (response.status === 'success') {
                                showPopup('Success', response.message, 'success');
                                loadAnnouncements();
                            } else {
                                showPopup('Error', response.message, 'error');
                            }
                        }, 100);
                    },
                    error: function (xhr) {
                        loadingModal.hide();
                        setTimeout(function() {
                            console.error('AJAX Error:', xhr.responseText);
                            showPopup('Error', 'An error occurred. Please try again.', 'error');
                        }, 100);
                    }
                });
            });
        });
    });

    // ── Load Table ────────────────────────────────────────────────────────────
    function loadAnnouncements() {
        $.ajax({
            url:    '{{ route("announcements.list") }}',
            method: 'GET',
            success: function (response) {
                const tbody = $('#announcementsTable');
                tbody.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    $.each(response.data, function (i, row) {
                        tbody.append(`
                            <tr>
                                <td class="cell-date">${row.date_posted}</td>
                                <td>
                                    <button class="btn-ghost" onclick="openViewModal(
                                        ${row.id},
                                        \`${row.title}\`,
                                        \`${row.description}\`,
                                        \`${row.subject_name}\`,
                                        ${row.subject_id},
                                        \`${row.section_names || ''}\`,
                                        \`${row.grade_level_names || ''}\`
                                    )">
                                        ${row.title}
                                    </button>
                                </td>
                                <td>${row.description}</td>
                                <td>${row.subject_name}</td>
                                <!-- <td style="font-size:13px;">${row.grade_level_names || '—'}</td>
                                <td style="font-size:13px;">${row.section_names || '—'}</td> -->
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="6" style="text-align:center;">No announcements found.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr.responseText);
                $('#announcementsTable').html('<tr><td colspan="6" style="text-align:center;">Failed to load announcements.</td></tr>');
            }
        });
    }

    $(document).ready(function () {
        loadAnnouncements();
    });
</script>

@endsection