{{-- resources/views/profile.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('page-title')
<h2>My Profile</h2>
@endsection

@push('styles')
<style>
/* ══ PROFILE HERO ═══════════════════════════════════════════ */
.profile-hero {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
    min-height: 180px;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #1e3a8a 100%);
    border: 1px solid rgba(255,255,255,0.07);
}
.profile-hero-bg {
    position: absolute; inset: 0; pointer-events: none; overflow: hidden;
}
.profile-hero-bg::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(37,99,235,0.35) 0%, transparent 70%);
    border-radius: 50%;
}
.profile-hero-bg::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 20%;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%);
    border-radius: 50%;
}
.profile-hero-content {
    position: relative; z-index: 1;
    display: flex; align-items: flex-end; gap: 24px;
    padding: 28px 28px 24px;
}
.profile-avatar-wrap {
    position: relative; flex-shrink: 0;
}
.profile-avatar-ring {
    width: 88px; height: 88px; border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    padding: 3px;
    box-shadow: 0 0 0 4px rgba(59,130,246,0.2), 0 8px 32px rgba(37,99,235,0.45);
    animation: avatarGlow 3s ease-in-out infinite alternate;
}
@keyframes avatarGlow {
    from { box-shadow: 0 0 0 4px rgba(59,130,246,0.2), 0 8px 32px rgba(37,99,235,0.45); }
    to   { box-shadow: 0 0 0 8px rgba(59,130,246,0.12), 0 8px 40px rgba(139,92,246,0.5); }
}
.profile-avatar-inner {
    width: 100%; height: 100%; border-radius: 50%;
    background: linear-gradient(135deg, #1e40af, #4f46e5);
    display: flex; align-items: center; justify-content: center;
    font-size: 30px; font-weight: 800; color: #fff;
    font-family: var(--font-display); letter-spacing: -1px;
}
.profile-online-dot {
    position: absolute; bottom: 4px; right: 4px;
    width: 14px; height: 14px; border-radius: 50%;
    background: #22c55e; border: 2.5px solid #0f172a;
    box-shadow: 0 0 0 0 rgba(34,197,94,0.5);
    animation: onlinePulse 2s infinite;
}
@keyframes onlinePulse {
    0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
    70%  { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.profile-hero-info { flex: 1; min-width: 0; }
.profile-hero-name {
    font-family: var(--font-display); font-size: 22px; font-weight: 800;
    color: #fff; letter-spacing: -.5px; margin-bottom: 4px;
}
.profile-hero-email { font-size: 13px; color: rgba(255,255,255,0.5); margin-bottom: 10px; }
.profile-hero-badges { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.profile-role-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
    letter-spacing: .2px;
}
.role-admin   { background: rgba(139,92,246,0.2); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.35); }
.role-teacher { background: rgba(37,99,235,0.2);  color: #93c5fd; border: 1px solid rgba(37,99,235,0.35); }
.role-student { background: rgba(22,163,74,0.2);  color: #86efac; border: 1px solid rgba(22,163,74,0.35); }

.profile-hero-actions {
    display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    align-self: flex-start; padding-top: 4px;
}

/* ══ STAT CHIPS IN HERO ══════════════════════════════════════ */
.profile-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600;
    background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.65);
}
.profile-chip svg { opacity: .7; }

/* ══ STATS ROW ═══════════════════════════════════════════════ */
.profile-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.profile-stat-card {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: var(--radius-lg); padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    transition: border-color .2s, transform .2s;
}
.profile-stat-card:hover { border-color: rgba(255,255,255,0.14); transform: translateY(-2px); }
.profile-stat-icon {
    width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.psi-blue  { background: rgba(37,99,235,0.15); color: #60a5fa; }
.psi-purple{ background: rgba(139,92,246,0.15); color: #c4b5fd; }
.psi-green { background: rgba(22,163,74,0.15);  color: #4ade80; }
.psi-amber { background: rgba(217,119,6,0.15);  color: #fbbf24; }
.profile-stat-body { flex: 1; min-width: 0; }
.profile-stat-label { font-size: 10.5px; font-weight: 700; color: var(--dk-t4); text-transform: uppercase; letter-spacing: .5px; }
.profile-stat-value { font-size: 14px; font-weight: 700; color: var(--dk-t1); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ══ MAIN LAYOUT ═════════════════════════════════════════════ */
.profile-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

/* ══ FORM CARD ═══════════════════════════════════════════════ */
.profile-form-card {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: var(--radius-lg); overflow: hidden;
    transition: border-color .2s;
}
.profile-form-card:hover { border-color: rgba(255,255,255,0.1); }

.profile-section-header {
    padding: 14px 20px; border-bottom: 1px solid var(--dk-b2);
    display: flex; align-items: center; justify-content: space-between;
}
.profile-section-header-left { display: flex; align-items: center; gap: 10px; }
.profile-section-icon {
    width: 30px; height: 30px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.psi-form-blue   { background: rgba(37,99,235,0.15); color: #60a5fa; }
.psi-form-amber  { background: rgba(217,119,6,0.15);  color: #fbbf24; }
.psi-form-red    { background: rgba(220,38,38,0.15);  color: #f87171; }
.profile-section-title { font-family: var(--font-display); font-size: 13.5px; font-weight: 700; color: var(--dk-t1); }
.profile-section-sub   { font-size: 11.5px; color: var(--dk-t4); margin-top: 1px; }

.profile-section-body { padding: 22px 20px; }

.profile-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.profile-form-grid.three { grid-template-columns: 1fr 1fr 1fr; }
.profile-form-full  { grid-column: 1/-1; }

.profile-field  { display: flex; flex-direction: column; gap: 5px; }
.profile-label  {
    font-size: 10.5px; font-weight: 700; color: var(--dk-t4);
    text-transform: uppercase; letter-spacing: .05em;
}
.profile-input, .profile-select {
    padding: 9px 12px; border: 1.5px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-md); font-size: 13px;
    color: var(--dk-t1); background: var(--dk-surface2);
    font-family: var(--font-body); outline: none;
    transition: border-color .2s, box-shadow .2s; width: 100%; box-sizing: border-box;
}
.profile-input:focus, .profile-select:focus {
    border-color: rgba(96,165,250,0.45);
    box-shadow: 0 0 0 3px rgba(96,165,250,0.08);
}
.profile-input:disabled, .profile-select:disabled { opacity: .5; cursor: not-allowed; }
.profile-input::placeholder { color: var(--dk-t4); }
.profile-select option { background: #111827; color: #e2e8f0; }
.profile-select {
    appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
}

.profile-read-only {
    padding: 9px 12px; border: 1.5px solid rgba(255,255,255,0.05);
    border-radius: var(--radius-md); font-size: 13px;
    color: var(--dk-t3); background: rgba(255,255,255,0.02);
    font-style: italic; display: flex; align-items: center; gap: 6px;
}
.profile-read-only::before {
    content: '';
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--dk-t4); flex-shrink: 0;
}

.profile-divider {
    font-size: 10px; font-weight: 800; color: var(--dk-t4);
    text-transform: uppercase; letter-spacing: .08em;
    padding: 12px 0 8px; border-bottom: 1px solid var(--dk-b2);
    margin-bottom: 0; grid-column: 1/-1;
    display: flex; align-items: center; gap: 8px;
}
.profile-divider::after { content:''; flex: 1; height: 1px; background: var(--dk-b2); }

.profile-footer {
    padding: 14px 20px; border-top: 1px solid var(--dk-b2);
    display: flex; justify-content: flex-end; gap: 10px;
    background: var(--dk-surface2);
}

/* Password strength */
.pw-strength { margin-top: 5px; height: 4px; border-radius: 4px; background: var(--dk-b1); overflow: hidden; }
.pw-strength-bar { height: 100%; border-radius: 4px; transition: width .3s ease, background .3s ease; width: 0; }
.pw-hint { font-size: 11px; color: var(--dk-t4); margin-top: 4px; font-weight: 500; }

/* Password input wrapper */
.pw-input-wrap { position: relative; }
.pw-toggle-btn {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--dk-t4); padding: 2px; transition: color .15s;
}
.pw-toggle-btn:hover { color: var(--dk-t2); }

/* Activity chip */
.profile-activity-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2);
    color: #4ade80;
}

@media (max-width: 860px) {
    .profile-stats-row { grid-template-columns: 1fr 1fr; }
    .profile-form-grid { grid-template-columns: 1fr; }
    .profile-form-grid.three { grid-template-columns: 1fr 1fr; }
    .profile-hero-content { flex-direction: column; align-items: flex-start; gap: 16px; }
}
@media (max-width: 600px) {
    .profile-stats-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

@php
    $initials = strtoupper(substr($user->name, 0, 1));
    $parts    = explode(' ', $user->name);
    if (count($parts) > 1) $initials .= strtoupper(substr($parts[1], 0, 1));
    $roleName  = $role == 3 ? 'Administrator' : ($role == 1 ? 'Teacher' : 'Student');
    $roleClass = $role == 3 ? 'role-admin' : ($role == 1 ? 'role-teacher' : 'role-student');
@endphp

{{-- ── HERO BANNER ─────────────────────────────────────────────────────────── --}}
<div class="profile-hero">
    <div class="profile-hero-bg"></div>
    <div class="profile-hero-content">
        <div class="profile-avatar-wrap">
            <div class="profile-avatar-ring">
                <div class="profile-avatar-inner">{{ $initials }}</div>
            </div>
            <div class="profile-online-dot"></div>
        </div>

        <div class="profile-hero-info">
            <div class="profile-hero-name">{{ $user->name }}</div>
            <div class="profile-hero-email">{{ $user->email }}</div>
            <div class="profile-hero-badges">
                <span class="profile-role-badge {{ $roleClass }}">
                    @if($role == 3)
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        {{ $roleName }}
                    @else
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                        {{ $roleName }}
                    @endif
                </span>

                <span class="profile-activity-chip">
                    <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;animation:onlinePulse 2s infinite;"></span>
                    Online Now
                </span>

                @if($role == 1 && $profile)
                <span class="profile-chip">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/></svg>
                    {{ $profile->department ?? 'No Department' }}
                </span>
                @elseif($role == 2 && $profile)
                <span class="profile-chip">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    {{ $profile->grade_level_name ?? 'No Grade' }} — {{ $profile->section_name ?? 'No Section' }}
                </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
@if($role == 1 && $profile)
<div class="profile-stats-row" style="grid-template-columns: repeat(4, 1fr);">
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Employee ID</div>
            <div class="profile-stat-value">{{ $profile->employee_id ?? '—' }}</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-purple">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Department</div>
            <div class="profile-stat-value">{{ $profile->department ?? '—' }}</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Position</div>
            <div class="profile-stat-value">{{ $profile->position ?? '—' }}</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-amber">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Status</div>
            <div class="profile-stat-value">{{ $profile->employment_status ?? '—' }}</div>
        </div>
    </div>
</div>
@elseif($role == 2 && $profile)
<div class="profile-stats-row">
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Grade Level</div>
            <div class="profile-stat-value">{{ $profile->grade_level_name ?? '—' }}</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-purple">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Section</div>
            <div class="profile-stat-value">{{ $profile->section_name ?? '—' }}</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM4 9h16M9 4v16"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">LRN / Student No.</div>
            <div class="profile-stat-value">{{ $profile->student_no ?? '—' }}</div>
        </div>
    </div>
</div>
@elseif($role == 3)
<div class="profile-stats-row" style="grid-template-columns: repeat(2, 1fr);">
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-purple">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Role</div>
            <div class="profile-stat-value">System Administrator</div>
        </div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon psi-blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        </div>
        <div class="profile-stat-body">
            <div class="profile-stat-label">Member Since</div>
            <div class="profile-stat-value">{{ $user->created_at?->format('M Y') ?? '—' }}</div>
        </div>
    </div>
</div>
@endif

{{-- ── FORMS ────────────────────────────────────────────────────────────────── --}}
<div class="profile-layout">

    {{-- ── Personal / Account Info ─────────────────────────────────────────── --}}
    <div class="profile-form-card">
        <div class="profile-section-header">
            <div class="profile-section-header-left">
                <div class="profile-section-icon psi-form-blue">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                </div>
                <div>
                    <div class="profile-section-title">
                        @if($role == 3) Account Information @else Personal Information @endif
                    </div>
                    <div class="profile-section-sub">Update your personal details</div>
                </div>
            </div>
        </div>

        <form id="profileForm">
        <div class="profile-section-body">

            @if($role == 3)
            {{-- Admin: name + email --}}
            <div class="profile-form-grid">
                <div class="profile-field profile-form-full">
                    <label class="profile-label">Full Name *</label>
                    <input type="text" name="name" class="profile-input" value="{{ $user->name }}" required placeholder="Your full name">
                </div>
                <div class="profile-field profile-form-full">
                    <label class="profile-label">Email Address *</label>
                    <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required placeholder="your@email.com">
                </div>
            </div>

            @elseif($role == 1 && $profile)
            {{-- Teacher --}}
            <div class="profile-form-grid three">
                <div class="profile-divider">Personal Information</div>
                <div class="profile-field">
                    <label class="profile-label">Last Name *</label>
                    <input type="text" name="lname" class="profile-input" value="{{ $profile->lname }}" required>
                </div>
                <div class="profile-field">
                    <label class="profile-label">First Name *</label>
                    <input type="text" name="fname" class="profile-input" value="{{ $profile->fname }}" required>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Middle Name</label>
                    <input type="text" name="mname" class="profile-input" value="{{ $profile->mname }}">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Date of Birth</label>
                    <input type="date" name="birthdate" class="profile-input" value="{{ $profile->birthdate }}">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Sex</label>
                    <select name="sex" class="profile-input profile-select">
                        <option value="">Select…</option>
                        <option value="Male"   {{ $profile->sex === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $profile->sex === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Civil Status</label>
                    <select name="civil_status" class="profile-input profile-select">
                        <option value="Single"  {{ $profile->civil_status === 'Single'  ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ $profile->civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                    </select>
                </div>
                <div class="profile-field profile-form-full">
                    <label class="profile-label">Address</label>
                    <input type="text" name="address" class="profile-input" value="{{ $profile->address }}" placeholder="Street, City, Province">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Contact Number</label>
                    <input type="tel" name="contact_no" class="profile-input" value="{{ $profile->contact_no }}" placeholder="+63 9XX XXX XXXX">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Email Address *</label>
                    <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required>
                </div>

                <div class="profile-divider">Faculty Details</div>
                <div class="profile-field">
                    <label class="profile-label">Employee ID</label>
                    <div class="profile-read-only">{{ $profile->employee_id ?? '—' }}</div>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Department</label>
                    <select name="department" class="profile-input profile-select">
                        <option value="">Select…</option>
                        @foreach(['Junior High School','Senior High School','Administration','Guidance'] as $dept)
                        <option value="{{ $dept }}" {{ $profile->department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Position / Designation</label>
                    <input type="text" name="position" class="profile-input" value="{{ $profile->position }}" placeholder="e.g. Head Teacher III">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Specialization</label>
                    <input type="text" name="specialization" class="profile-input" value="{{ $profile->specialization }}" placeholder="e.g. Mathematics">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Employment Status</label>
                    <select name="employment_status" class="profile-input profile-select">
                        @foreach(['Permanent','Temporary','Contractual','Part-time'] as $es)
                        <option value="{{ $es }}" {{ $profile->employment_status === $es ? 'selected' : '' }}>{{ $es }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Date Hired</label>
                    <input type="date" name="date_hired" class="profile-input" value="{{ $profile->date_hired }}">
                </div>
            </div>

            @elseif($role == 2 && $profile)
            {{-- Student --}}
            <div class="profile-form-grid three">
                <div class="profile-divider">Personal Information</div>
                <div class="profile-field">
                    <label class="profile-label">Last Name *</label>
                    <input type="text" name="lname" class="profile-input" value="{{ $profile->lname }}" required>
                </div>
                <div class="profile-field">
                    <label class="profile-label">First Name *</label>
                    <input type="text" name="fname" class="profile-input" value="{{ $profile->fname }}" required>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Middle Name</label>
                    <input type="text" name="mname" class="profile-input" value="{{ $profile->mname }}">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Date of Birth</label>
                    <input type="date" name="birthdate" class="profile-input" value="{{ $profile->birthdate }}">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Sex</label>
                    <select name="sex" class="profile-input profile-select">
                        <option value="">Select…</option>
                        <option value="Male"   {{ $profile->sex === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $profile->sex === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Civil Status</label>
                    <select name="Civil_status" class="profile-input profile-select">
                        <option value="Single"  {{ $profile->Civil_status === 'Single'  ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ $profile->Civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                    </select>
                </div>
                <div class="profile-field profile-form-full">
                    <label class="profile-label">Address</label>
                    <input type="text" name="address" class="profile-input" value="{{ $profile->address }}" placeholder="Street, City, Province">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Contact Number</label>
                    <input type="tel" name="contact_no" class="profile-input" value="{{ $profile->contact_no }}" placeholder="+63 9XX XXX XXXX">
                </div>
                <div class="profile-field">
                    <label class="profile-label">Email Address *</label>
                    <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required>
                </div>

                <div class="profile-divider">Academic Information <span style="font-weight:400;font-style:italic;color:var(--dk-t4);">(managed by admin)</span></div>
                <div class="profile-field">
                    <label class="profile-label">LRN</label>
                    <div class="profile-read-only">{{ $profile->student_no ?? '—' }}</div>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Grade Level</label>
                    <div class="profile-read-only">{{ $profile->grade_level_name ?? '—' }}</div>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Section</label>
                    <div class="profile-read-only">{{ $profile->section_name ?? '—' }}</div>
                </div>
            </div>
            @endif

        </div>
        <div class="profile-footer">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('profileForm').reset()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Reset
            </button>
            <button type="submit" class="btn btn-primary" id="btnSaveProfile">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Changes
            </button>
        </div>
        </form>
    </div>

    {{-- ── Change Password ──────────────────────────────────────────────────── --}}
    <div class="profile-form-card">
        <div class="profile-section-header">
            <div class="profile-section-header-left">
                <div class="profile-section-icon psi-form-amber">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <div class="profile-section-title">Change Password</div>
                    <div class="profile-section-sub">Keep your account secure</div>
                </div>
            </div>

            {{-- Password security tip --}}
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--dk-t4);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Use 8+ chars, numbers & symbols
            </div>
        </div>
        <form id="passwordForm">
        <div class="profile-section-body">
            <div class="profile-form-grid">
                <div class="profile-field profile-form-full">
                    <label class="profile-label">Current Password *</label>
                    <div class="pw-input-wrap">
                        <input type="password" name="current_password" id="currentPassword" class="profile-input" placeholder="Enter current password" autocomplete="current-password" style="padding-right:38px;">
                        <button type="button" class="pw-toggle-btn" onclick="togglePw('currentPassword', this)" tabindex="-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="profile-field">
                    <label class="profile-label">New Password *</label>
                    <div class="pw-input-wrap">
                        <input type="password" name="new_password" id="newPassword" class="profile-input" placeholder="Min. 8 characters" autocomplete="new-password" style="padding-right:38px;">
                        <button type="button" class="pw-toggle-btn" onclick="togglePw('newPassword', this)" tabindex="-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-strength"><div class="pw-strength-bar" id="pwStrengthBar"></div></div>
                    <div class="pw-hint" id="pwHint">Enter a password</div>
                </div>
                <div class="profile-field">
                    <label class="profile-label">Confirm New Password *</label>
                    <div class="pw-input-wrap">
                        <input type="password" name="new_password_confirmation" id="confirmPassword" class="profile-input" placeholder="Repeat new password" autocomplete="new-password" style="padding-right:38px;">
                        <button type="button" class="pw-toggle-btn" onclick="togglePw('confirmPassword', this)" tabindex="-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-hint" id="confirmHint" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="profile-footer">
            <button type="button" class="btn btn-outline" onclick="resetPasswordForm()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Reset
            </button>
            <button type="submit" class="btn btn-primary" id="btnSavePassword">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Change Password
            </button>
        </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Toggle password visibility ────────────────────────────────────────────────
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.style.color = isPass ? 'var(--blue-400)' : 'var(--dk-t4)';
}

// ── Reset password form ───────────────────────────────────────────────────────
function resetPasswordForm() {
    document.getElementById('passwordForm').reset();
    document.getElementById('pwStrengthBar').style.width = '0';
    document.getElementById('pwStrengthBar').style.background = '';
    document.getElementById('pwHint').textContent = 'Enter a password';
    document.getElementById('pwHint').style.color = '';
    document.getElementById('confirmHint').style.display = 'none';
}

$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // ── Password strength ─────────────────────────────────────────────────────
    $('#newPassword').on('input', function () {
        const val = $(this).val();
        const bar  = document.getElementById('pwStrengthBar');
        const hint = document.getElementById('pwHint');
        let strength = 0;
        if (val.length >= 8)             strength++;
        if (/[A-Z]/.test(val))           strength++;
        if (/[0-9]/.test(val))           strength++;
        if (/[^A-Za-z0-9]/.test(val))   strength++;

        const colors = ['', '#ef4444', '#f59e0b', '#22c55e', '#16a34a'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        bar.style.width      = (strength * 25) + '%';
        bar.style.background = colors[strength] || '';
        hint.textContent     = val ? labels[strength] || '' : 'Enter a password';
        hint.style.color     = colors[strength] || 'var(--dk-t4)';
    });

    // ── Confirm password match indicator ──────────────────────────────────────
    $('#confirmPassword').on('input', function () {
        const confHint = document.getElementById('confirmHint');
        const newPw  = $('#newPassword').val();
        const confPw = $(this).val();
        if (!confPw) { confHint.style.display = 'none'; return; }
        confHint.style.display = 'block';
        if (newPw === confPw) {
            confHint.textContent = '✓ Passwords match';
            confHint.style.color = '#22c55e';
        } else {
            confHint.textContent = '✗ Passwords do not match';
            confHint.style.color = '#ef4444';
        }
    });

    // ── Profile form submit ───────────────────────────────────────────────────
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        const data = {};
        $(this).serializeArray().forEach(f => { data[f.name] = f.value; });
        const $btn = $('#btnSaveProfile').prop('disabled', true).html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 0.8s linear infinite"><path d="M21 12a9 9 0 1 1-3-6.7"/></svg> Saving…');
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:    '{{ route("profile.update") }}',
            method: 'POST',
            contentType: 'application/json',
            data:   JSON.stringify(data),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    showPopup('Success', res.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showPopup('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', xhr.responseJSON?.message ?? 'Failed to save.', 'error');
            },
            complete: function () {
                $('#btnSaveProfile').prop('disabled', false).html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Save Changes');
            }
        });
    });

    // ── Password form submit ──────────────────────────────────────────────────
    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();
        const newPw  = $('#newPassword').val();
        const confPw = $('#confirmPassword').val();

        if (newPw.length < 8) {
            showPopup('Validation', 'New password must be at least 8 characters.', 'warning');
            return;
        }
        if (newPw !== confPw) {
            showPopup('Validation', 'New passwords do not match.', 'warning');
            return;
        }

        const $btn = $('#btnSavePassword').prop('disabled', true).html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 0.8s linear infinite"><path d="M21 12a9 9 0 1 1-3-6.7"/></svg> Changing…');
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:    '{{ route("profile.password") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                current_password:          $('#currentPassword').val(),
                new_password:              newPw,
                new_password_confirmation: confPw,
            }),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    showPopup('Success', res.message, 'success');
                    resetPasswordForm();
                } else {
                    showPopup('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', xhr.responseJSON?.message ?? 'Failed to change password.', 'error');
            },
            complete: function () {
                $('#btnSavePassword').prop('disabled', false).html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Change Password');
            }
        });
    });
});
</script>
<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
@endpush
