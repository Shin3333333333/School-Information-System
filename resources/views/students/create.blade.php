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

        <form id="studentForm" method="POST" action="{{ route('students.store') }}" class="card-body form-section">
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

            {{-- User Type Selector (always visible) --}}
            <div>
                <div class="form-section-divider">Placement & Role</div>
                <div class="form-grid-3">
                    <div class="filter-group">
                        <span class="filter-label">Type *</span>
                        <select name="student_type" id="userTypeSelect" class="form-select">
                            <option value="2">Student</option>
                            <option value="1">Teacher / Faculty</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Contact Number *</span>
                        <input type="tel" name="contact" class="form-input" placeholder="09XX XXX XXXX" required>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Email Address</span>
                        <input type="email" name="email" class="form-input" placeholder="user@email.com">
                    </div>
                </div>
            </div>

            {{-- ── STUDENT-ONLY FIELDS ── --}}
            <div id="studentFields">
                <div class="form-section-divider">Student Details</div>
                <div class="form-grid-3">
                    <div class="filter-group">
                        <span class="filter-label">Academic Year *</span>
                        <select name="academic_year" class="form-select">
                            <option>2024–2025</option>
                            <option>2025–2026</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Grade Level *</span>
                        <select name="grade_level" class="form-select">
                            <option value="">Select…</option>
                            @for($g=7;$g<=12;$g++)
                                <option>Grade {{ $g }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Section *</span>
                        <select name="section" class="form-select">
                            <option value="">Select…</option>
                            <option>Section A</option>
                            <option>Section B</option>
                            <option>Section C</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">LRN (DepEd)</span>
                        <input type="text" name="lrn" class="form-input" placeholder="12-digit number" maxlength="12">
                    </div>
                </div>
            </div>

            {{-- ── TEACHER-ONLY FIELDS ── --}}
            <div id="teacherFields" style="display:none;">
                <div class="form-section-divider">Faculty Details</div>
                <div class="form-grid-3">
                    <div class="filter-group">
                        <span class="filter-label">Employee ID *</span>
                        <input type="text" name="employee_id" class="form-input" placeholder="EMP-00001">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Department *</span>
                        <select name="department" class="form-select">
                            <option value="">Select…</option>
                            <option>Junior High School</option>
                            <option>Senior High School</option>
                            <option>Administration</option>
                            <option>Guidance</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Position / Designation *</span>
                        <input type="text" name="position" class="form-input" placeholder="e.g. Subject Teacher, Adviser">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Specialization</span>
                        <input type="text" name="specialization" class="form-input" placeholder="e.g. Mathematics, Filipino">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Date Hired</span>
                        <input type="date" name="date_hired" class="form-input">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Employment Status</span>
                        <select name="employment_status" class="form-select">
                            <option>Permanent</option>
                            <option>Temporary</option>
                            <option>Contractual</option>
                            <option>Part-time</option>
                        </select>
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

<script>
$(document).ready(function () {
    loadingModal.hide(); // ensure it's hidden on page load

    // ── Toggle section visibility ─────────────────────────────────────────────
    function toggleUserTypeFields(type) {
        var isTeacher = (type === '1');
        $('#studentFields').toggle(!isTeacher);
        $('#teacherFields').toggle(isTeacher);
        $('#studentFields').find('[data-required]').prop('required', !isTeacher);
        $('#teacherFields').find('[data-required]').prop('required', isTeacher);
    }

    toggleUserTypeFields($('#userTypeSelect').val());

    $('#userTypeSelect').on('change', function () {
        toggleUserTypeFields($(this).val());
    });

    // ── AJAX setup ────────────────────────────────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── Form submit ───────────────────────────────────────────────────────────
    $('#studentForm').on('submit', function (e) {
        e.preventDefault();
        loadingModal.show(); // show ONLY on submit

        var userType  = $('#userTypeSelect').val();
        var isTeacher = (userType === '1');

        var formData = {
            last_name:    $('input[name="last_name"]').val()    || null,
            first_name:   $('input[name="first_name"]').val()   || null,
            middle_name:  $('input[name="middle_name"]').val()  || null,
            dob:          $('input[name="dob"]').val()          || null,
            sex:          $('select[name="sex"]').val()         || null,
            civil_status: $('select[name="civil_status"]').val()|| null,
            address:      $('input[name="address"]').val()      || null,
            student_type: userType,
            contact:      $('input[name="contact"]').val()      || null,
            email:        $('input[name="email"]').val()        || null,
        };

        if (isTeacher) {
            $.extend(formData, {
                employee_id:       $('input[name="employee_id"]').val()       || null,
                department:        $('select[name="department"]').val()        || null,
                position:          $('input[name="position"]').val()           || null,
                specialization:    $('input[name="specialization"]').val()     || null,
                date_hired:        $('input[name="date_hired"]').val()         || null,
                employment_status: $('select[name="employment_status"]').val() || null,
            });
        } else {
            $.extend(formData, {
                academic_year: $('select[name="academic_year"]').val() || null,
                grade_level:   $('select[name="grade_level"]').val()   || null,
                section:       $('select[name="section"]').val()       || null,
                lrn:           $('input[name="lrn"]').val()            || null,
            });
        }

        $.ajax({
            url:         '{{ route("students.store") }}',
            type:        'POST',
            contentType: 'application/json',
            data:        JSON.stringify(formData),

            success: function (response) {
                $('#studentForm')[0].reset();
                toggleUserTypeFields('2');
                var msg = response.message;
                if (response.generated_password) {
                    msg += '\n\nDefault password: ' + response.generated_password;
                }
                showPopup('Saved Successfully', msg, 'success');
            },

            error: function (xhr) {
                if (xhr.status === 422) {
                    var messages = Object.values(xhr.responseJSON.errors)
                                         .map(function(v){ return v[0]; })
                                         .join('\n');
                    showPopup('Validation Error', messages, 'warning');
                } else {
                    showPopup('Error', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                }
            },

            complete: function () {
                loadingModal.hide();
            }
        });
    });
});
</script>

@endsection