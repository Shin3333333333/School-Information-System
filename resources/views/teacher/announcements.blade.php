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
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="cell-date">Mar 28, 2026</td>
                <td>
                    <button class="btn-ghost" onclick="openViewModal('Final Exams Begin','Exams schedule for all subjects','All Classes')">
                        Final Exams Begin
                    </button>
                </td>
                <td>Exams schedule for all subjects</td>
            </tr>
            <tr>
                <td class="cell-date">Apr 02, 2026</td>
                <td>
                    <button class="btn-ghost" onclick="openViewModal('Grade Submission Deadline','Deadline for submitting grades','All Classes')">
                        Grade Submission Deadline
                    </button>
                </td>
                <td>Deadline for submitting grades</td>
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
        <form>
            <label class="filter-label mb-1">Subject</label>
            <select class="form-select mb-3">
                <option value="all">All Classes</option>
                <option value="math">Math</option>
                <option value="science">Science</option>
            </select>

            <label class="filter-label mb-1">Title</label>
            <input type="text" class="form-input mb-3">

            <label class="filter-label mb-1">Description</label>
            <textarea class="form-input mb-3" rows="4"></textarea>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn btn-outline" onclick="closePostModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>
    </div>
</div>


<script>
    const viewModal = document.getElementById('viewModal');
    const postModal = document.getElementById('postModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalSubject = document.getElementById('modalSubject');

    function openViewModal(title, description, subject) {
        modalTitle.textContent = title;
        modalDescription.textContent = description;
        modalSubject.textContent = subject;
        viewModal.style.display = 'flex';
    }
    function closeViewModal() { viewModal.style.display = 'none'; }

    document.getElementById('openPostModal').addEventListener('click', () => {
        postModal.style.display = 'flex';
    });
    function closePostModal() { postModal.style.display = 'none'; }
</script>

@endsection
