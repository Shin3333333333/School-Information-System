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
                <th>Subject</th>
                <!-- <th>Section</th> -->
            </tr>
        </thead>
        <tbody id="announcementsTable">
            <tr>
                <td colspan="4" style="text-align:center;">Loading...</td>
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
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-primary">Edit</button>
            <button class="btn btn-danger" onclick="confirm('Are you sure to delete?')">Delete</button>
        </div>
    </div>
</div>

{{-- POST MODAL --}}
<div id="postModal" style="display:none; position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:var(--radius-lg); width:400px; padding:20px; box-shadow:var(--shadow-md);">
        <h3 class="section-title" style="margin-bottom:15px;">Post New Announcement</h3>
        <form id="postForm">
            <label class="filter-label mb-1">Subject</label>
            <select id="subjectSelect" name="subject_id" class="form-select mb-3">
                <option value="">— Select Subject —</option>
            </select>

            <label class="filter-label mb-1">Title</label>
            <input type="text" name="title" id="postTitle" class="form-input mb-3">

            <label class="filter-label mb-1">Description</label>
            <textarea name="description" id="postDescription" class="form-input mb-3" rows="4"></textarea>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn btn-outline" onclick="closePostModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Post</button>
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

    function openViewModal(title, description, subject) {
        modalTitle.textContent       = title;
        modalDescription.textContent = description;
        modalSubject.textContent     = subject;
        viewModal.style.display      = 'flex';
    }

    function closeViewModal() { viewModal.style.display = 'none'; }
    function closePostModal() { postModal.style.display = 'none'; }

    document.getElementById('openPostModal').addEventListener('click', function () {
        postModal.style.display = 'flex';

        $.ajax({
            url:    '{{ route("announcements.index") }}',
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    const select = $('#subjectSelect');
                    select.find('option:not(:first)').remove();

                    $.each(response.data, function (i, subject) {
                        select.append(
                            `<option value="${subject.id}">${subject.subject_name}</option>`
                        );
                    });
                } else {
                    alert('Failed to load subjects: ' + response.message);
                }
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr.responseText);
                alert('An error occurred while loading subjects.');
            }
        });
    });
    $('#postForm').on('submit', function (e) {
    e.preventDefault();

    const data = {
        title:       $('#postTitle').val(),
        description: $('#postDescription').val(),
        subject_id:  $('#subjectSelect').val(),
        date_posted: new Date().toISOString().slice(0, 10), // today's date YYYY-MM-DD
    };

    $.ajax({
        url:    '{{ route("announcements.store") }}',
        method: 'POST',
        data:   data,
        success: function (response) {
            if (response.status === 'success') {
                closePostModal();
                alert(response.message);
                location.reload(); // refresh table
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function (xhr) {
            console.error('AJAX Error:', xhr.responseText);
            alert('An error occurred while posting the announcement.');
        }
    });
});
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
                                <button class="btn-ghost" onclick="openViewModal('${row.title}', '${row.subject_name}', '${row.section_name}')">
                                    ${row.title}
                                </button>
                            </td>
                            <td>${row.subject_name}</td>
                            <!-- <td>${row.section_name}</td> -->
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="4" style="text-align:center;">No announcements found.</td></tr>');
            }
        },
        error: function (xhr) {
            console.error('AJAX Error:', xhr.responseText);
            $('#announcementsTable').html('<tr><td colspan="4" style="text-align:center;">Failed to load announcements.</td></tr>');
        }
    });
}

// Load on page ready
$(document).ready(function () {
    loadAnnouncements();
});
</script>

@endsection
