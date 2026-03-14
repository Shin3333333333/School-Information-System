@extends('layouts.app')

@section('title', 'Dashboard — School Information System')

@section('page-title')
    <h2><span style="text-transform: capitalize;">{{ $roleName }}</span> Dashboard</h2>
@endsection

@section('content')

<style>
    .stats-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    .stats-grid-4 .stat-card {
        margin: 0;
    }
    .dashboard-row {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }
    .dashboard-row.col-2 { grid-template-columns: 1fr 1fr; }
    .dashboard-row.col-3-2 { grid-template-columns: 1.5fr 1fr; }
    .dashboard-row.col-2-3 { grid-template-columns: 1fr 1.5fr; }

    .recent-user-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .recent-user-row:last-child { border-bottom: none; }
    .recent-user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--blue-100);
        color: var(--blue-600);
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .recent-user-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-800);
    }
    .recent-user-meta {
        font-size: 11.5px;
        color: var(--gray-400);
        margin-top: 1px;
    }
    .recent-user-role {
        margin-left: auto;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 10px;
        flex-shrink: 0;
    }
    .role-admin   { background: #dbeafe; color: #1d4ed8; }
    .role-teacher { background: #dcfce7; color: #16a34a; }
    .role-student { background: #fef3c7; color: #d97706; }

    .mini-stat {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        background: var(--gray-50);
        border: 1px solid var(--gray-100);
    }
    .mini-stat-label { font-size: 12px; color: var(--gray-500); }
    .mini-stat-value { font-size: 18px; font-weight: 700; color: var(--gray-800); }

    @media (max-width: 900px) {
        .stats-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .dashboard-row.col-2,
        .dashboard-row.col-3-2,
        .dashboard-row.col-2-3 { grid-template-columns: 1fr; }
    }
</style>

{{-- ── Row 1: Stat Cards ── --}}
<div class="stats-grid-4">

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Students</span>
            <div class="stat-icon blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalStudents) }}</div>
        <div class="stat-meta">
            <span style="color:var(--green-600); font-weight:600;">+{{ $newThisMonth }}</span> new this month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Teachers</span>
            <div class="stat-icon green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalTeachers) }}</div>
        <div class="stat-meta">
            <span style="color:var(--green-600); font-weight:600;">+{{ $newTeachersThisMonth }}</span> new this month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Sections</span>
            <div class="stat-icon amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalSections) }}</div>
        <div class="stat-meta">Across all grade levels</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Users</span>
            <div class="stat-icon red">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalStudents + $totalTeachers) }}</div>
        <div class="stat-meta">Students &amp; teachers</div>
    </div>

</div>

{{-- ── Row 2: Mini Stats ── --}}
<div class="dashboard-row col-2" style="margin-bottom:20px;">
    <div class="card card-body" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
        <div class="mini-stat">
            <div>
                <div class="mini-stat-label">Announcements</div>
                <div class="mini-stat-value">{{ $totalAnnouncements }}</div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color:var(--blue-400);">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
            </svg>
        </div>
        <div class="mini-stat">
            <div>
                <div class="mini-stat-label">Events</div>
                <div class="mini-stat-value">{{ $totalEvents }}</div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color:var(--green-400);">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="mini-stat">
            <div>
                <div class="mini-stat-label">Active Policies</div>
                <div class="mini-stat-value">{{ $totalActivePolicies }}</div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color:var(--amber-400);">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    {{-- Enrollment Trend --}}
    <div class="card card-body">
        <div class="section-title" style="margin-bottom:14px;">Student Enrollment Trend</div>
        <canvas id="enrollmentTrendChart" height="80"></canvas>
    </div>
</div>

{{-- ── Row 3: Students per Grade + Announcements ── --}}
<div class="dashboard-row col-3-2">

    <div class="card card-body">
        <div class="section-title" style="margin-bottom:14px;">Students per Grade Level</div>
        <canvas id="studentsPerGradeChart" height="160"></canvas>
    </div>

    <div class="card card-body">
        <div class="section-title" style="margin-bottom:14px;">Recent Announcements</div>
        <div class="events-scroll">
            @php $colors = ['blue', 'amber', 'green', 'red']; @endphp
            @forelse($upcomingEvents as $i => $ev)
            <div class="event-item">
                <div class="event-date-badge {{ $colors[$i % count($colors)] }}">
                    {{ \Carbon\Carbon::parse($ev->date_posted)->format('M') }}<br>
                    {{ \Carbon\Carbon::parse($ev->date_posted)->format('d') }}
                </div>
                <div style="min-width:0;">
                    <span class="event-label">{{ $ev->title }}</span>
                    <div style="font-size:12px; color:var(--gray-400); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $ev->subject_name }} &mdash; {{ $ev->section_names }}
                    </div>
                </div>
            </div>
            @empty
            <div style="color:var(--gray-400); font-size:13px; text-align:center; padding:20px 0;">
                No announcements found.
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Row 4: Recently Added Users ── --}}
<div class="card card-body" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="section-title">Recently Added Users</span>
        <a href="{{ route('students.index') }}" style="font-size:12px; color:var(--blue-600); text-decoration:none; font-weight:500;">
            View all →
        </a>
    </div>
    <div style="padding: 4px 0;">
        @forelse($recentUsers as $ru)
        <div class="recent-user-row">
            <div class="recent-user-avatar">
                {{ strtoupper(substr($ru->name, 0, 1)) }}{{ strtoupper(substr(strstr($ru->name, ' '), 1, 1)) }}
            </div>
            <div style="flex:1; min-width:0;">
                <div class="recent-user-name">{{ $ru->name }}</div>
                <div class="recent-user-meta">{{ $ru->email }}</div>
            </div>
            <div style="font-size:12px; color:var(--gray-400); margin-right:10px;">
                {{ \Carbon\Carbon::parse($ru->created_at)->diffForHumans() }}
            </div>
            <span class="recent-user-role role-{{ strtolower($ru->role_name) }}">
                {{ $ru->role_name }}
            </span>
        </div>
        @empty
        <div style="color:var(--gray-400); font-size:13px; text-align:center; padding:20px 0;">
            No recent users found.
        </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── Students per Grade Level ──────────────────────────────────────────────
    const gradeLabels = @json($studentsPerGrade->pluck('grade_level_name'));
    const gradeCounts = @json($studentsPerGrade->pluck('total'));

    new Chart(document.getElementById('studentsPerGradeChart'), {
        type: 'bar',
        data: {
            labels: gradeLabels,
            datasets: [{
                label: 'Students',
                data: gradeCounts,
                backgroundColor: [
                    'rgba(99,102,241,0.15)',
                    'rgba(16,185,129,0.15)',
                    'rgba(245,158,11,0.15)',
                    'rgba(239,68,68,0.15)',
                    'rgba(59,130,246,0.15)',
                    'rgba(168,85,247,0.15)',
                ],
                borderColor: [
                    'rgba(99,102,241,1)',
                    'rgba(16,185,129,1)',
                    'rgba(245,158,11,1)',
                    'rgba(239,68,68,1)',
                    'rgba(59,130,246,1)',
                    'rgba(168,85,247,1)',
                ],
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // ── Enrollment Trend ──────────────────────────────────────────────────────
    const trendLabels = @json($enrollmentTrend->pluck('month'));
    const trendCounts = @json($enrollmentTrend->pluck('total'));

    new Chart(document.getElementById('enrollmentTrendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'New Students',
                data: trendCounts,
                borderColor: 'rgba(99,102,241,1)',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(99,102,241,1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>

@endsection