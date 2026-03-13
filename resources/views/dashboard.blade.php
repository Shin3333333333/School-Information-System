@extends('layouts.app')

@section('title', 'Dashboard — School Information System')

@section('page-title')
    <h2><span style="text-transform: capitalize;">{{ $roleName }}</span> Dashboard</h2>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="stats-grid">
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
        <div class="stat-meta"><span>+{{ $newThisMonth }}</span> new this month</div>
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
        <div class="stat-meta">Active faculty</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Users</span>
            <div class="stat-icon amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalStudents + $totalTeachers) }}</div>
        <div class="stat-meta">Students &amp; teachers</div>
    </div>
</div>

{{-- Quick Actions + Upcoming Events side by side --}}
<div class="dashboard-widgets">

    <div class="card card-body dashboard-widget-card">
        <div class="section-title" style="margin-bottom:14px;">Quick Actions</div>
        <div class="quick-actions-list">
            <a href="{{ route('students.create') }}" class="btn btn-primary" style="justify-content:center;">+ Add New User</a>
            <a href="{{ route('students.index') }}" class="btn btn-outline" style="justify-content:center;">Manage Users</a>
            <a href="{{ route('enrollment.index') }}" class="btn btn-outline" style="justify-content:center;">Manage Information</a>
        </div>
    </div>

    <div class="card card-body dashboard-widget-card">
        <div class="section-title" style="margin-bottom:14px;">Announcements</div>
        <div class="events-scroll">
            @php $colors = ['blue', 'amber', 'green', 'red']; @endphp
            @forelse($upcomingEvents as $i => $ev)
            <div class="event-item">
                <div class="event-date-badge {{ $colors[$i % count($colors)] }}">
                    {{ \Carbon\Carbon::parse($ev->date_posted)->format('M') }}<br>
                    {{ \Carbon\Carbon::parse($ev->date_posted)->format('d') }}
                </div>
                <div>
                    <span class="event-label">{{ $ev->title }}</span>
                    <div style="font-size:12px; color:var(--gray-400); margin-top:2px;">
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

@endsection