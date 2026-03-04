@extends('layouts.app')

@section('title', 'Dashboard — School Information System')

@section('page-title')
    <h2>Dashboard</h2>
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
        <div class="stat-value">1,284</div>
        <div class="stat-meta"><span>+12</span> new this month</div>
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
        <div class="stat-value">64</div>
        <div class="stat-meta">Across <span>12</span> departments</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Classes Today</span>
            <div class="stat-icon amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">48</div>
        <div class="stat-meta"><span>3</span> cancelled, <span>2</span> rescheduled</div>
    </div>
</div>

{{-- Quick Actions + Upcoming Events side by side --}}
<div class="dashboard-widgets">

    <div class="card card-body dashboard-widget-card">
        <div class="section-title" style="margin-bottom:14px;">Quick Actions</div>
        <div class="quick-actions-list">
            <a href="{{ route('students.create') }}" class="btn btn-primary" style="justify-content:center;">+ Add New User</a>
            <a href="{{ route('grades.index') }}" class="btn btn-outline" style="justify-content:center;">Manage Users</a>
            <a href="{{ route('enrollment.index') }}" class="btn btn-outline" style="justify-content:center;">Manage Information</a>
        </div>
    </div>

    <div class="card card-body dashboard-widget-card">
        <div class="section-title" style="margin-bottom:14px;">Upcoming Events</div>
        <div class="events-scroll">
            @foreach([
                ['Mar 28','Final Exams Begin','amber'],
                ['Apr 02','Grade Submission Deadline','red'],
                ['Apr 10','Enrollment Opening – SY 25-26','blue'],
                ['Apr 15','Moving Up Ceremony','green'],
            ] as $ev)
            <div class="event-item">
                <div class="event-date-badge {{ $ev[2] }}">
                    {{ explode(' ',$ev[0])[0] }}<br>{{ explode(' ',$ev[0])[1] }}
                </div>
                <span class="event-label">{{ $ev[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection