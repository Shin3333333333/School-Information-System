@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('page-title')
<h2>Teacher Dashboard</h2>
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
            Next:
            <span class="amber">Math – Section A</span>
            at <span class="amber">10:00 AM - 12:00 PM</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Students</span>
        </div>
        <div class="stat-value">120</div>
        <div class="stat-meta">Across all classes</div>
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
                <a href="{{ route('teacher.class-list') }}" class="btn btn-primary">View Class List</a>
                <a href="{{ route('teacher.announcements') }}" class="btn btn-outline">Manage Announcements</a>
            </div>
        </div>
    </div>

    {{-- Placeholder for future widget --}}
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
