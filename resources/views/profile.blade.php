{{-- resources/views/profile.blade.php --}}
{{-- Accessible via the avatar initials button in the top bar for all roles --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('page-title')
<h2>My Profile</h2>
@endsection

@push('styles')
<style>
.profile-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 20px;
    align-items: start;
}

/* ── Avatar card ────────────────────────────────── */
.profile-avatar-card {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: var(--radius-lg); padding: 28px 20px;
    display: flex; flex-direction: column; align-items: center;
    gap: 12px; text-align: center;
}
.profile-avatar-circle {
    width: 80px; height: 80px; border-radius: 50%;
    background: linear-gradient(135deg, #1e40af, #2563eb);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 800; color: #fff;
    font-family: var(--font-display);
    box-shadow: 0 8px 24px rgba(37,99,235,0.35);
    letter-spacing: -1px;
}
.profile-name {
    font-family: var(--font-display); font-size: 1rem;
    font-weight: 700; color: var(--dk-t1); margin: 0;
}
.profile-email { font-size: 0.8rem; color: var(--dk-t4); margin: 0; }
.profile-role-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
}
.role-admin   { background: rgba(139,92,246,0.15); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.25); }
.role-teacher { background: rgba(37,99,235,0.15);  color: #60a5fa; border: 1px solid rgba(37,99,235,0.25); }
.role-student { background: rgba(22,163,74,0.15);  color: #4ade80; border: 1px solid rgba(22,163,74,0.25); }

.profile-stat {
    width: 100%; padding: 10px 14px;
    background: var(--dk-surface2); border-radius: var(--radius-md);
    border: 1px solid var(--dk-b1); text-align: left;
}
.profile-stat-label { font-size: 0.7rem; font-weight: 700; color: var(--dk-t4); text-transform: uppercase; letter-spacing: .04em; }
.profile-stat-value { font-size: 0.85rem; color: var(--dk-t2); font-weight: 600; margin-top: 2px; }

/* ── Form sections ──────────────────────────────── */
.profile-form-card {
    background: var(--dk-surface); border: 1px solid var(--dk-b1);
    border-radius: var(--radius-lg); overflow: hidden;
}
.profile-section-header {
    padding: 14px 20px; border-bottom: 1px solid var(--dk-b2);
    display: flex; align-items: center; gap: 8px;
}
.profile-section-title {
    font-family: var(--font-display); font-size: 0.88rem;
    font-weight: 700; color: var(--dk-t1);
}
.profile-section-body { padding: 20px; }

.profile-form-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
}
.profile-form-grid.three { grid-template-columns: 1fr 1fr 1fr; }
.profile-form-full { grid-column: 1/-1; }

.profile-field { display: flex; flex-direction: column; gap: 5px; }
.profile-label {
    font-size: 0.72rem; font-weight: 700; color: var(--dk-t4);
    text-transform: uppercase; letter-spacing: .04em;
}
.profile-input, .profile-select {
    padding: 8px 12px; border: 1.5px solid var(--dk-b1);
    border-radius: var(--radius-md); font-size: 0.84rem;
    color: var(--dk-t2); background: var(--dk-surface2);
    font-family: var(--font-body); outline: none;
    transition: border-color .2s; width: 100%; box-sizing: border-box;
}
.profile-input:focus, .profile-select:focus { border-color: rgba(96,165,250,0.4); }
.profile-input:disabled, .profile-select:disabled {
    opacity: .5; cursor: not-allowed; background: var(--dk-surface);
}
.profile-input::placeholder { color: var(--dk-t4); }
.profile-select option { background: #111827; color: #e2e8f0; }

.profile-read-only {
    padding: 8px 12px; border: 1.5px solid var(--dk-b2);
    border-radius: var(--radius-md); font-size: 0.84rem;
    color: var(--dk-t3); background: var(--dk-surface);
    font-style: italic;
}

.profile-divider {
    font-size: 0.72rem; font-weight: 700; color: var(--dk-t4);
    text-transform: uppercase; letter-spacing: .06em;
    padding: 10px 0 6px; border-bottom: 1px solid var(--dk-b2);
    margin-bottom: 6px; grid-column: 1/-1;
}

.profile-footer {
    padding: 14px 20px; border-top: 1px solid var(--dk-b2);
    display: flex; justify-content: flex-end; gap: 10px;
}

/* Password strength bar */
.pw-strength { margin-top: 4px; height: 3px; border-radius: 2px; background: var(--dk-b1); overflow: hidden; }
.pw-strength-bar { height: 100%; border-radius: 2px; transition: width .3s, background .3s; width: 0; }
.pw-hint { font-size: 0.7rem; color: var(--dk-t4); margin-top: 3px; }

@media (max-width: 860px) {
    .profile-layout { grid-template-columns: 1fr; }
    .profile-form-grid { grid-template-columns: 1fr; }
    .profile-form-grid.three { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@section('content')

@php
    $initials = strtoupper(substr($user->name, 0, 1));
    $parts    = explode(' ', $user->name);
    if (count($parts) > 1) $initials .= strtoupper(substr($parts[1], 0, 1));
    $roleName = $role == 3 ? 'Admin' : ($role == 1 ? 'Teacher' : 'Student');
    $roleClass = $role == 3 ? 'role-admin' : ($role == 1 ? 'role-teacher' : 'role-student');
@endphp

<div class="profile-layout">

    {{-- ── Left: Avatar card ──────────────────────────────────────────────── --}}
    <div class="profile-avatar-card">
        <div class="profile-avatar-circle">{{ $initials }}</div>
        <div>
            <p class="profile-name">{{ $user->name }}</p>
            <p class="profile-email">{{ $user->email }}</p>
        </div>
        <span class="profile-role-badge {{ $roleClass }}">
            @if($role == 3)
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Administrator
            @elseif($role == 1)
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                Teacher
            @else
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                Student
            @endif
        </span>

        {{-- Role-specific stat chips --}}
        @if($role == 1 && $profile)
        <div class="profile-stat">
            <div class="profile-stat-label">Department</div>
            <div class="profile-stat-value">{{ $profile->department ?? '—' }}</div>
        </div>
        <div class="profile-stat">
            <div class="profile-stat-label">Position</div>
            <div class="profile-stat-value">{{ $profile->position ?? '—' }}</div>
        </div>
        <div class="profile-stat">
            <div class="profile-stat-label">Employee ID</div>
            <div class="profile-stat-value">{{ $profile->employee_id ?? '—' }}</div>
        </div>
        @elseif($role == 2 && $profile)
        <div class="profile-stat">
            <div class="profile-stat-label">Grade Level</div>
            <div class="profile-stat-value">{{ $profile->grade_level_name ?? '—' }}</div>
        </div>
        <div class="profile-stat">
            <div class="profile-stat-label">Section</div>
            <div class="profile-stat-value">{{ $profile->section_name ?? '—' }}</div>
        </div>
        <div class="profile-stat">
            <div class="profile-stat-label">LRN</div>
            <div class="profile-stat-value">{{ $profile->student_no ?? '—' }}</div>
        </div>
        @endif
    </div>

    {{-- ── Right: Forms ────────────────────────────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- ── Personal / Profile Info ──────────────────────────────────────── --}}
        <div class="profile-form-card">
            <div class="profile-section-header">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
                </svg>
                <span class="profile-section-title">
                    @if($role == 3) Account Information
                    @else Personal Information
                    @endif
                </span>
            </div>

            <form id="profileForm">
            <div class="profile-section-body">

                @if($role == 3)
                {{-- Admin: name + email only --}}
                <div class="profile-form-grid">
                    <div class="profile-field profile-form-full">
                        <label class="profile-label">Full Name *</label>
                        <input type="text" name="name" class="profile-input" value="{{ $user->name }}" required>
                    </div>
                    <div class="profile-field profile-form-full">
                        <label class="profile-label">Email Address *</label>
                        <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required>
                    </div>
                </div>

                @elseif($role == 1 && $profile)
                {{-- Teacher: full personal + faculty details --}}
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
                        <select name="sex" class="profile-select">
                            <option value="">Select…</option>
                            <option value="Male"   {{ $profile->sex === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $profile->sex === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Civil Status</label>
                        <select name="civil_status" class="profile-select">
                            <option value="Single"  {{ $profile->civil_status === 'Single'  ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $profile->civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>
                    <div class="profile-field profile-form-full">
                        <label class="profile-label">Address</label>
                        <input type="text" name="address" class="profile-input" value="{{ $profile->address }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Contact Number</label>
                        <input type="tel" name="contact_no" class="profile-input" value="{{ $profile->contact_no }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Email Address *</label>
                        <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required>
                    </div>

                    <div class="profile-divider">Faculty Details</div>
                    <div class="profile-field">
                        <label class="profile-label">Employee ID</label>
                        <div class="profile-read-only">{{ $profile->employee_id ?? '—' }}</div>
                        {{-- Read-only: managed by admin --}}
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Department</label>
                        <select name="department" class="profile-select">
                            <option value="">Select…</option>
                            @foreach(['Junior High School','Senior High School','Administration','Guidance'] as $dept)
                            <option value="{{ $dept }}" {{ $profile->department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Position / Designation</label>
                        <input type="text" name="position" class="profile-input" value="{{ $profile->position }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Specialization</label>
                        <input type="text" name="specialization" class="profile-input" value="{{ $profile->specialization }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Employment Status</label>
                        <select name="employment_status" class="profile-select">
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
                {{-- Student: personal info editable, academic info read-only --}}
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
                        <select name="sex" class="profile-select">
                            <option value="">Select…</option>
                            <option value="Male"   {{ $profile->sex === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $profile->sex === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Civil Status</label>
                        <select name="Civil_status" class="profile-select">
                            <option value="Single"  {{ $profile->Civil_status === 'Single'  ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $profile->Civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>
                    <div class="profile-field profile-form-full">
                        <label class="profile-label">Address</label>
                        <input type="text" name="address" class="profile-input" value="{{ $profile->address }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Contact Number</label>
                        <input type="tel" name="contact_no" class="profile-input" value="{{ $profile->contact_no }}">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Email Address *</label>
                        <input type="email" name="email" class="profile-input" value="{{ $user->email }}" required>
                    </div>

                    <div class="profile-divider">Academic Information <span style="font-weight:400; font-style:italic;">(managed by admin)</span></div>
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
                <button type="button" class="btn btn-outline" onclick="document.getElementById('profileForm').reset()">Reset</button>
                <button type="submit" class="btn btn-primary" id="btnSaveProfile">Save Changes</button>
            </div>
            </form>
        </div>

        {{-- ── Change Password ───────────────────────────────────────────────── --}}
        <div class="profile-form-card">
            <div class="profile-section-header">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span class="profile-section-title">Change Password</span>
            </div>
            <form id="passwordForm">
            <div class="profile-section-body">
                <div class="profile-form-grid">
                    <div class="profile-field profile-form-full">
                        <label class="profile-label">Current Password *</label>
                        <input type="password" name="current_password" id="currentPassword" class="profile-input" placeholder="Enter current password" autocomplete="current-password">
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">New Password *</label>
                        <input type="password" name="new_password" id="newPassword" class="profile-input" placeholder="Min. 8 characters" autocomplete="new-password">
                        <div class="pw-strength"><div class="pw-strength-bar" id="pwStrengthBar"></div></div>
                        <div class="pw-hint" id="pwHint">Enter a password</div>
                    </div>
                    <div class="profile-field">
                        <label class="profile-label">Confirm New Password *</label>
                        <input type="password" name="new_password_confirmation" id="confirmPassword" class="profile-input" placeholder="Repeat new password" autocomplete="new-password">
                    </div>
                </div>
            </div>
            <div class="profile-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('passwordForm').reset(); document.getElementById('pwStrengthBar').style.width='0'; document.getElementById('pwHint').textContent='Enter a password';">Reset</button>
                <button type="submit" class="btn btn-primary" id="btnSavePassword">Change Password</button>
            </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // ── Password strength ─────────────────────────────────────────────────────
    $('#newPassword').on('input', function () {
        const val = $(this).val();
        const bar  = document.getElementById('pwStrengthBar');
        const hint = document.getElementById('pwHint');
        let strength = 0;
        if (val.length >= 8)              strength++;
        if (/[A-Z]/.test(val))            strength++;
        if (/[0-9]/.test(val))            strength++;
        if (/[^A-Za-z0-9]/.test(val))     strength++;

        const colors = ['', '#ef4444', '#f59e0b', '#22c55e', '#16a34a'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        bar.style.width     = (strength * 25) + '%';
        bar.style.background = colors[strength] || '';
        hint.textContent    = val ? labels[strength] || '' : 'Enter a password';
        hint.style.color    = colors[strength] || 'var(--dk-t4)';
    });

    // ── Profile form submit ───────────────────────────────────────────────────
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();

        const data = {};
        $(this).serializeArray().forEach(f => { data[f.name] = f.value; });

        const $btn = $('#btnSaveProfile').prop('disabled', true).text('Saving…');
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
                    // Update the avatar initials and name in the sidebar/topbar
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
                $btn.prop('disabled', false).text('Save Changes');
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

        const $btn = $('#btnSavePassword').prop('disabled', true).text('Changing…');
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) loadingEl.style.display = 'flex';

        $.ajax({
            url:    '{{ route("profile.password") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                current_password:      $('#currentPassword').val(),
                new_password:          newPw,
                new_password_confirmation: confPw,
            }),
            success: function (res) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (res.status === 'success') {
                    showPopup('Success', res.message, 'success');
                    document.getElementById('passwordForm').reset();
                    document.getElementById('pwStrengthBar').style.width = '0';
                    document.getElementById('pwHint').textContent = 'Enter a password';
                } else {
                    showPopup('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                if (loadingEl) loadingEl.style.display = 'none';
                showPopup('Error', xhr.responseJSON?.message ?? 'Failed to change password.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Change Password');
            }
        });
    });
});
</script>
@endpush
