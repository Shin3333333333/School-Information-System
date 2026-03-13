@extends('layouts.app')

@section('title', 'All Announcements — School Information System')

@section('page-title')
<h2>All Announcements</h2>
@endsection

@section('content')

{{-- Announcements Table --}}
<div class="card card-body dashboard-widget-card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date Posted</th>
                <th>Title</th>
                <th>Description</th>
                <th>Subject</th>
                <th>Posted By</th>
            </tr>
        </thead>
        <tbody id="announcementsTable">
            <tr>
                <td colspan="5" style="text-align:center;">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- VIEW MODAL --}}
<div id="viewModal" style="display:none; position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:10px; width:420px; padding:24px; box-shadow:0 4px 16px rgba(0,0,0,0.3);">
        <h3 style="margin-bottom:16px; font-weight:600;">Announcement Details</h3>
        <div id="modalContent" style="margin-bottom:16px; display:flex; flex-direction:column; gap:10px;">
            <p><strong>Title:</strong> <span id="modalTitle"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
            <p><strong>Sections:</strong> <span id="modalSections"></span></p>
            <p style="padding-top:8px; border-top:1px solid var(--gray-100);">
                <strong>Posted By:</strong>
                <span id="modalPostedBy" style="color:var(--blue-600); font-weight:500;"></span>
            </p>
        </div>
        <div style="display:flex; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const viewModal        = document.getElementById('viewModal');
    const modalTitle       = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalSubject     = document.getElementById('modalSubject');
    const modalSections    = document.getElementById('modalSections');
    const modalPostedBy    = document.getElementById('modalPostedBy');

    function openViewModal(id, title, description, subject_name, section_names, posted_by) {
        modalTitle.textContent       = title;
        modalDescription.textContent = description;
        modalSubject.textContent     = subject_name;
        modalSections.textContent    = section_names || '—';
        modalPostedBy.textContent    = posted_by;
        viewModal.style.display      = 'flex';
    }

    function closeViewModal() { viewModal.style.display = 'none'; }

    function loadAnnouncements() {
        $.ajax({
            url:    '{{ route("announcements.all") }}',
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
                                        \`${row.section_names}\`,
                                        \`${row.posted_by}\`
                                    )">
                                        ${row.title}
                                    </button>
                                </td>
                                <td>${row.description}</td>
                                <td>${row.subject_name}</td>
                                <td>
                                    <span style="display:inline-flex; align-items:center; gap:6px;">
                                        <span style="
                                            width:26px; height:26px; border-radius:50%;
                                            background:var(--blue-100); color:var(--blue-600);
                                            display:inline-flex; align-items:center; justify-content:center;
                                            font-size:11px; font-weight:700;
                                        ">
                                            ${row.posted_by.charAt(0).toUpperCase()}
                                        </span>
                                        ${row.posted_by}
                                    </span>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="5" style="text-align:center;">No announcements found.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr.responseText);
                $('#announcementsTable').html('<tr><td colspan="5" style="text-align:center;">Failed to load announcements.</td></tr>');
            }
        });
    }

    $(document).ready(function () {
        loadAnnouncements();
    });
</script>

@endsection