@extends('layouts.app')

@section('title', 'Announcements — School Information System')

@section('page-title')
<h2>Announcements</h2>
@endsection

@section('content')

{{-- Announcements Table --}}
<div class="card card-body dashboard-widget-card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date Posted</th>
                <th>Posted By</th>
            </tr>
        </thead>
        <tbody>
            <tr onclick="openViewModal('Final Exams Begin','Exams schedule for all subjects','All Classes')" style="cursor:pointer;">
                <td>Final Exams Begin</td>
                <td>Mar 28, 2026</td>
                <td>Admin</td>
            </tr>
            <tr onclick="openViewModal('Grade Submission Deadline','Deadline for submitting grades','All Classes')" style="cursor:pointer;">
                <td>Grade Submission Deadline</td>
                <td>Apr 02, 2026</td>
                <td>Registrar</td>
            </tr>
            <tr onclick="openViewModal('Library Closure','Library closed for inventory','All Classes')" style="cursor:pointer;">
                <td>Library Closure</td>
                <td>Mar 15, 2026</td>
                <td>Librarian</td>
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
            <p><strong>Posted By:</strong> <span id="modalPostedBy"></span></p>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
    const viewModal = document.getElementById('viewModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalPostedBy = document.getElementById('modalPostedBy');

    function openViewModal(title, description, postedBy) {
        modalTitle.textContent = title;
        modalDescription.textContent = description;
        modalPostedBy.textContent = postedBy;
        viewModal.style.display = 'flex';
    }

    function closeViewModal() {
        viewModal.style.display = 'none';
    }
</script>

@endsection