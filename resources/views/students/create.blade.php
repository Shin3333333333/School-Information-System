@extends('layouts.app')

@section('title', 'Enroll Student — School Information System')

@section('page-title')
    <h2>Add New User</h2>
@endsection

@section('content')

<div class="form-page-wrap">

    <div class="form-breadcrumb">
        <a href="{{ route('students.index') }}" class="btn btn-ghost" style="padding:6px 10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <span class="form-breadcrumb-label">User Management / Add New</span>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="section-title">User Information</span>
            <span class="status-badge status-pending">Draft</span>
        </div>

        <form method="POST" action="{{ route('students.store') }}" class="card-body form-section">
            @csrf

            {{-- Personal Information --}}
            <div>
                <div class="form-section-divider">Personal Information</div>
                <div class="form-grid-3">
                    <div class="filter-group">
                        <span class="filter-label">Last Name *</span>
                        <input type="text" name="last_name" class="form-input" placeholder="dela Cruz" required>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">First Name *</span>
                        <input type="text" name="first_name" class="form-input" placeholder="Juan" required>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Middle Name</span>
                        <input type="text" name="middle_name" class="form-input" placeholder="Pedro">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Date of Birth *</span>
                        <input type="date" name="dob" class="form-input" required>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Sex *</span>
                        <select name="sex" class="form-select" required>
                            <option value="">Select…</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Civil Status</span>
                        <select name="civil_status" class="form-select">
                            <option>Single</option>
                            <option>Married</option>
                        </select>
                    </div>
                    <div class="filter-group full-width">
                        <span class="filter-label">Address *</span>
                        <input type="text" name="address" class="form-input" placeholder="House No., Street, Barangay, City, Province">
                    </div>
                </div>
            </div>

            {{-- Placement --}}
            <div>
                <div class="form-section-divider">Placement</div>
                <div class="form-grid-3">
                    <div class="filter-group">
                        <span class="filter-label">Academic Year *</span>
                        <select name="academic_year" class="form-select" required>
                            <option>2024–2025</option>
                            <option>2025–2026</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Grade Level *</span>
                        <select name="grade_level" class="form-select" required>
                            <option value="">Select…</option>
                            @for($g=7;$g<=12;$g++)
                                <option>Grade {{ $g }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Section *</span>
                        <select name="section" class="form-select" required>
                            <option value="">Select…</option>
                            <option>Section A</option>
                            <option>Section B</option>
                            <option>Section C</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Type *</span>
                        <select name="student_type" class="form-select">
                            <option>Student</option>
                            <option>Teacher</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">LRN (DepEd)</span>
                        <input type="text" name="lrn" class="form-input" placeholder="12-digit number" maxlength="12">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Contact Number *</span>
                        <input type="tel" name="contact" class="form-input" placeholder="09XX XXX XXXX" required>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Email Address</span>
                        <input type="email" name="email" class="form-input" placeholder="guardian@email.com">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('students.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/>
                        <polyline points="17 21 17 13 7 13 7 21" stroke="currentColor" stroke-width="2"/>
                        <polyline points="7 3 7 8 15 8" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Save Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection