@extends('layouts.app')

@section('title', 'Student Dashboard — School Information System')

@section('page-title')
<h2>Student Dashboard</h2>
@endsection

@section('content')

{{-- Stats Cards --}}
<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Classes Today</span>
        </div>
        <div class="stat-value">3</div>
        <div class="stat-meta">
            Next: <span class="amber">Math – Section A</span> at <span class="amber">10:00 AM - 11:00 AM</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">New Announcements</span>
        </div>
        <div class="stat-value">2</div>
        <div class="stat-meta">
            Unread
        </div>
    </div>
</div>

{{-- Dashboard Widgets --}}
<div class="dashboard-widgets">

    {{-- Quick Actions --}}
    <div class="card dashboard-widget-card">
        <div class="card-header">
            <h3 class="section-title">Quick Actions</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions-list">
                <a href="{{ route('student.schedule') }}" class="btn btn-primary">View Schedule</a>
                <a href="{{ route('student.announcements') }}" class="btn btn-outline">View Announcements</a>
            </div>
        </div>
    </div>

    {{-- Upcoming Events Placeholder --}}
    <div class="card dashboard-widget-card">
        <div class="card-header">
            <h3 class="section-title">Upcoming</h3>
        </div>
        <div class="card-body">
            <p class="stat-meta">No upcoming events</p>
        </div>
    </div>

</div>

@endsection