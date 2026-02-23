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
            <tbody>
                @php
                $students = [
                    ['name'=>'Maria Santos','id'=>'maria@gmail.com','role'=>'Student','status'=>'active','active'=>true],
                    ['name'=>'Juan dela Cruz','id'=>'juan@gmail.com','role'=>'Teacher','status'=>'inactive','active'=>false],
                    ['name'=>'Ana Reyes','id'=>'ana@gmail.com','role'=>'Student','status'=>'active','active'=>true],
                    ['name'=>'Carlos Mendoza','id'=>'carlos@gmail.com','role'=>'Student','status'=>'inactive','active'=>false],
                    ['name'=>'Sofia Villanueva','id'=>'sofia@gmail.com','role'=>'Student','status'=>'active','active'=>true],
                    ['name'=>'Miguel Torres','id'=>'miguel@gmail.com','role'=>'Student','status'=>'inactive','active'=>false],
                    ['name'=>'Isabella Ramos','id'=>'isabella@gmail.com','role'=>'Student','status'=>'active','active'=>true],
                ];
                @endphp

                @foreach($students as $i => $student)
                <tr class="{{ $i === 4 ? 'highlighted' : '' }}">
                    <td>
                        <div class="cell-detail">
                            <div class="detail-dot {{ $student['active'] ? 'dot-green' : 'dot-red' }}"></div>
                            <div>
                                <div class="detail-text">{{ $student['name'] }}</div>
                                <div class="detail-sub">{{ $student['id'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="cell-currency">
                            <div style="font-weight:500;">{{ $student['role'] }}</div>
                        </div>
                    </td>
                    <td>
                        @if($student['status'] === 'active')
                            <span class="status-badge status-active">
                                <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                                Active
                            </span>
                        @else
                            <span class="status-badge status-inactive">
                                <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                                Inactive
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
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
function setGender(btn, gender) {
    document.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>
@endpush