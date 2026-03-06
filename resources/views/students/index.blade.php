@extends('layouts.app')

@section('title', 'Students — School Information System')

@section('page-title')
    <h2>Students</h2>
@endsection

@section('content')

{{-- ── Stat Cards ── --}}
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
        <div class="stat-value">1,284</div>
        <div class="stat-meta"><span>+12</span> joined this month</div>
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
        <div class="stat-value">1,198</div>
        <div class="stat-meta"><span>93.3%</span> attendance rate</div>
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
        <div class="stat-value">24</div>
        <div class="stat-meta">Across <span>5</span> grade levels</div>
    </div>
</div>

{{-- ── Main Card ── --}}
<div class="card">

    {{-- Toolbar: toggle + search (right-aligned) --}}
    <div class="card-toolbar">
        <div class="view-toggle">
            <button class="vtog-btn" onclick="setGender(this,'student')">Student</button>
            <button class="vtog-btn" onclick="setGender(this,'teacher')">Teacher</button>
            <button class="vtog-btn active" onclick="setGender(this,'all')">All</button>
        </div>
        <div class="search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#94a3b8" stroke-width="2"/>
                <path d="m21 21-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input type="text" placeholder="Search user, ID, class…" class="search-input">
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-toggle">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Filter
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="filter-group">
            <span class="filter-label">Grade Level</span>
            <select class="form-select" style="min-width:130px;">
                <option>All Grades</option>
                <option>Grade 7</option>
                <option>Grade 8</option>
                <option>Grade 9</option>
                <option>Grade 10</option>
                <option>Grade 11</option>
                <option>Grade 12</option>
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Section</span>
            <select class="form-select" style="min-width:120px;">
                <option>All Sections</option>
                <option>Section A</option>
                <option>Section B</option>
                <option>Section C</option>
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Enrolled From</span>
            <input type="date" class="form-input" style="min-width:145px;" value="2024-06-01">
        </div>

        <div class="filter-group">
            <span class="filter-label">Enrolled To</span>
            <input type="date" class="form-input" style="min-width:145px;" value="2025-03-24">
        </div>

        <div class="filter-actions">
            <button class="btn btn-outline">Reset</button>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                Add User
            </a>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="display:inline;vertical-align:middle;margin-right:6px;">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M4 20v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Student Details
                    </th>
                    <th>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="display:inline;vertical-align:middle;margin-right:6px;">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Role
                    </th>
                    <th>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="display:inline;vertical-align:middle;margin-right:6px;">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Status
                    </th>
                </tr>
            </thead>
            <tbody id="student-table-body">
                <!-- AJAX will inject rows here -->
                <tr>
                    <td colspan="3" style="text-align:center;">Loading students...</td>
                </tr>
            </tbody>

        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        <span class="page-info">Showing 1–7 of 1,284 students</span>
        <div class="page-buttons">
            <button class="page-btn">‹</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <span style="padding:0 4px;color:var(--gray-400);font-size:12px;display:flex;align-items:center;">…</span>
            <button class="page-btn">184</button>
            <button class="page-btn">›</button>
        </div>
    </div>

</div>{{-- /card --}}

@endsection

@push('scripts')

<script>
    
let allStudents = [];
let currentRoleFilter = 'all';
let role = '';
function setGender(btn, role) {
    document.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    currentRoleFilter = role;
    console.log(currentRoleFilter);
    renderStudents(currentRoleFilter);
}

$(document).ready(function() {
    fetchStudents();
});

function fetchStudents() {
    $.ajax({
        url: "{{ route('students.index') }}",
        type: "GET",
        dataType: "json",
        success: function(response) {
            console.log(response);

            allStudents = response.data; // store globally
            renderStudents();
        },
        error: function(xhr) {
            let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Fatal Error";
            $('#student-table-body').html(
                `<tr><td colspan="3" style="color:red; text-align:center;">${errorMsg}</td></tr>`
            );
        }
    });
}

function renderStudents() {
    let tbody = $('#student-table-body');
    tbody.empty();

    if (!allStudents || allStudents.length === 0) {
        tbody.append('<tr><td colspan="3" style="text-align:center;">No records found.</td></tr>');
        return;
    }

    let filter = currentRoleFilter.trim().toLowerCase();

    let filtered = allStudents.filter(student => {
        let role = student.role_name?.trim().toLowerCase() ?? '';

        if (filter === 'all') return true;

        let isMatch = role === filter;
        console.log("Checking student:", student.name, "role:", role, "against filter:", filter, "=>", isMatch);

        return isMatch;
    });

    if (filtered.length === 0) {
        tbody.append('<tr><td colspan="3" style="text-align:center;">No matching records.</td></tr>');
        return;
    }

    $.each(filtered, function(i, student) {
        let status = (student.status ?? 'inactive').toLowerCase();
        let isActive = status === 'active';
        let dotClass = isActive ? 'dot-green' : 'dot-red';
        let badgeClass = isActive ? 'status-active' : 'status-inactive';
        let statusText = status.charAt(0).toUpperCase() + status.slice(1);

        let row = `
            <tr>
                <td>
                    <div class="cell-detail">
                        <div class="detail-dot ${dotClass}"></div>
                        <div>
                            <div class="detail-text">${student.name ?? 'N/A'}</div>
                            <div class="detail-sub">${student.email ?? 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="cell-currency">
                        <div style="font-weight:500;">${student.role_name ?? 'N/A'}</div>
                    </div>
                </td>
                <td>
                    <span class="status-badge ${badgeClass}">
                        <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor">
                            <circle cx="4" cy="4" r="4"/>
                        </svg>
                        ${statusText}
                    </span>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}
</script>
@endpush