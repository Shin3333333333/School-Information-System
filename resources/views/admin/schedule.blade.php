@extends('layouts.app')

@section('title', 'Schedule')

@section('page-title')
    <h2>Schedule Management</h2>
@endsection

@section('content')

{{-- ── Filters ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-toolbar" style="flex-wrap:wrap; gap:10px;">

        <select id="sch-filter-grade" class="form-select" style="width:170px;">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
            @endforeach
        </select>

        <select id="sch-filter-section" class="form-select" style="width:170px;">
            <option value="">All Sections</option>
            @foreach($sections as $sec)
                <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">
                    {{ $sec->grade_level_name }} — {{ $sec->section_name }}
                </option>
            @endforeach
        </select>

        <select id="sch-filter-teacher" class="form-select" style="width:200px;">
            <option value="">All Teachers</option>
            @foreach($teachers as $t)
                <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
            @endforeach
        </select>

        <button class="btn btn-outline" id="btn-sch-reset">Reset</button>

        <button class="btn btn-primary" id="btn-add-schedule" style="margin-left:auto;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            Add Schedule
        </button>
    </div>
</div>

{{-- ── Timetable Grid ── --}}
<div class="card" style="overflow-x:auto;">
    <div id="sch-grid-wrap" style="min-width:700px;">
        <div style="text-align:center; padding:48px; color:#94a3b8;">
            <span class="loading loading-dots loading-md"></span>
        </div>
    </div>
</div>

{{-- ── Modal ── --}}
<div id="sch-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:10000; align-items:center; justify-content:center;">
    <div id="sch-modal-box" style="background:#fff; border-radius:14px; width:100%; max-width:520px; box-shadow:0 10px 40px rgba(0,0,0,.15); padding:28px; margin:16px; max-height:90vh; overflow-y:auto; position:relative;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h3 id="sch-modal-title" style="font-size:15px; font-weight:600; color:#0f172a; margin:0;">Add Schedule</h3>
            <button id="btn-sch-modal-close" style="border:none; background:none; cursor:pointer; color:#94a3b8; padding:4px; font-size:18px; line-height:1;">✕</button>
        </div>

        <div id="sch-modal-error" style="display:none; background:#fee2e2; color:#dc2626; padding:10px 14px; border-radius:6px; font-size:13px; font-weight:600; margin-bottom:14px;"></div>

        <input type="hidden" id="sch-edit-id">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

            <div style="grid-column:1/-1;">
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">SUBJECT *</label>
                <select id="sch-subject" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select subject</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="grid-column:1/-1;">
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">TEACHER *</label>
                <select id="sch-teacher" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select teacher</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->teacher_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">GRADE LEVEL *</label>
                <select id="sch-grade" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select grade</option>
                    @foreach($gradeLevels as $gl)
                        <option value="{{ $gl->id }}">{{ $gl->grade_level_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">SECTION *</label>
                <select id="sch-section" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select section</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" data-grade="{{ $sec->grade_level_id }}">{{ $sec->section_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">DAY *</label>
                <select id="sch-day" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; background:#fff; box-sizing:border-box;">
                    <option value="">Select day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">ROOM *</label>
                <input type="text" id="sch-room" placeholder="e.g. Room 101" maxlength="100"
                    style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">START TIME *</label>
                <input type="time" id="sch-time-start"
                    style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="font-size:11px; font-weight:600; color:#64748b; letter-spacing:.3px; display:block; margin-bottom:4px;">END TIME *</label>
                <input type="time" id="sch-time-end"
                    style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; outline:none; box-sizing:border-box;">
            </div>

        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px; padding-top:16px; border-top:1px solid #f1f5f9;">
            <button id="btn-sch-cancel" style="padding:8px 16px; border:1.5px solid #e2e8f0; background:#fff; color:#475569; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Cancel</button>
            <button id="btn-sch-save"   style="padding:8px 16px; background:#2563eb; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Save Schedule</button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var CSRF        = '{{ csrf_token() }}';
    var URL_LIST    = '{{ route("admin.schedule.list") }}';
    var URL_STORE   = '{{ route("admin.schedule.store") }}';
    var URL_UPDATE  = '{{ route("admin.schedule.update") }}';
    var URL_DESTROY = '{{ route("admin.schedule.destroy") }}';

    var DAYS        = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
    var DAY_COLORS  = { Monday:'#eff6ff', Tuesday:'#f0fdf4', Wednesday:'#fefce8', Thursday:'#fff7ed', Friday:'#fdf4ff' };
    var DAY_BORDERS = { Monday:'#93c5fd', Tuesday:'#86efac', Wednesday:'#fde047', Thursday:'#fdba74', Friday:'#d8b4fe' };
    var DAY_TEXT    = { Monday:'#1d4ed8', Tuesday:'#15803d', Wednesday:'#a16207', Thursday:'#c2410c', Friday:'#7e22ce' };

    // Stored original HTML for each select (for loading restore)
    var selectOriginals = {};

    // ── Modal elements ────────────────────────────────────────────
    var backdrop  = document.getElementById('sch-modal-backdrop');
    var modalErr  = document.getElementById('sch-modal-error');
    var editId    = document.getElementById('sch-edit-id');
    var fSubject  = document.getElementById('sch-subject');
    var fTeacher  = document.getElementById('sch-teacher');
    var fGrade    = document.getElementById('sch-grade');
    var fSection  = document.getElementById('sch-section');
    var fDay      = document.getElementById('sch-day');
    var fRoom     = document.getElementById('sch-room');
    var fStart    = document.getElementById('sch-time-start');
    var fEnd      = document.getElementById('sch-time-end');
    var saveBtn   = document.getElementById('btn-sch-save');

    // Store originals on page load
    ['sch-subject','sch-teacher','sch-grade','sch-section','sch-day'].forEach(function (id) {
        selectOriginals[id] = document.getElementById(id).innerHTML;
    });

    // ── Grade → Section filter inside modal ───────────────────────
    fGrade.addEventListener('change', function () {
        var gid = this.value;

        // Show loading inside section select
        fSection.innerHTML = '<option value="">Loading...</option>';
        fSection.disabled      = true;
        fSection.style.opacity = '.5';

        setTimeout(function () {
            // Restore original options then filter
            fSection.innerHTML = selectOriginals['sch-section'];
            Array.from(fSection.options).forEach(function (opt) {
                if (!opt.value) return;
                opt.hidden = gid ? opt.dataset.grade !== gid : false;
            });
            fSection.value         = '';
            fSection.disabled      = false;
            fSection.style.opacity = '1';
        }, 250);
    });

    // ── Open / Close ──────────────────────────────────────────────
    function openModal(title, saveTxt, row) {
        document.getElementById('sch-modal-title').textContent = title;
        saveBtn.textContent    = saveTxt;
        modalErr.style.display = 'none';

        // Show loading inside all selects
        setModalLoading(true);

        editId.value   = row ? row.id            : '';
        var savedSubject  = row ? row.subject_id     : '';
        var savedTeacher  = row ? row.user_id        : '';
        var savedGrade    = row ? row.grade_level_id : '';
        var savedSection  = row ? row.section_id     : '';
        var savedDay      = row ? row.day            : '';
        fRoom.value    = row ? row.room          : '';
        fStart.value   = row ? row.time_start    : '';
        fEnd.value     = row ? row.time_end      : '';

        backdrop.style.display = 'flex';

        setTimeout(function () {
            setModalLoading(false);

            // Restore values after loading
            fSubject.value = savedSubject;
            fTeacher.value = savedTeacher;
            fGrade.value   = savedGrade;
            fDay.value     = savedDay;

            // Filter sections by grade then set value
            if (savedGrade) {
                Array.from(fSection.options).forEach(function (opt) {
                    if (!opt.value) return;
                    opt.hidden = opt.dataset.grade !== String(savedGrade);
                });
            }
            fSection.value = savedSection;

        }, 400);
    }

    function closeModal() { backdrop.style.display = 'none'; }

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.getElementById('btn-add-schedule').addEventListener('click',    function () { openModal('Add Schedule', 'Save Schedule', null); });
    document.getElementById('btn-sch-modal-close').addEventListener('click', closeModal);
    document.getElementById('btn-sch-cancel').addEventListener('click',      closeModal);

    // ── Save ──────────────────────────────────────────────────────
    saveBtn.addEventListener('click', function () {
        modalErr.style.display = 'none';

        var id      = editId.value;
        var subject = fSubject.value;
        var teacher = fTeacher.value;
        var grade   = fGrade.value;
        var section = fSection.value;
        var day     = fDay.value;
        var room    = fRoom.value.trim();
        var start   = fStart.value;
        var end     = fEnd.value;

        if (!subject) { showErr('Please select a subject.');      return; }
        if (!teacher) { showErr('Please select a teacher.');      return; }
        if (!grade)   { showErr('Please select a grade level.');  return; }
        if (!section) { showErr('Please select a section.');      return; }
        if (!day)     { showErr('Please select a day.');          return; }
        if (!room)    { showErr('Room is required.');             return; }
        if (!start)   { showErr('Start time is required.');       return; }
        if (!end)     { showErr('End time is required.');         return; }
        if (start >= end) { showErr('End time must be after start time.'); return; }

        var url  = id ? URL_UPDATE : URL_STORE;
        var data = {
            subject_id: subject, user_id: teacher, section_id: section,
            grade_level_id: grade, room: room, day: day,
            time_start: start, time_end: end, _token: CSRF
        };
        if (id) data.id = id;

        saveBtn.disabled    = true;
        saveBtn.textContent = 'Saving…';

        $.ajax({
            url: url, method: 'POST', data: data,
            success: function (res) {
                closeModal();
                showPopup('Success', res.message, 'success');
                loadSchedule();
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.';
                showErr(msg);
            },
            complete: function () {
                saveBtn.disabled    = false;
                saveBtn.textContent = id ? 'Update Schedule' : 'Save Schedule';
            }
        });
    });

    // ── Helpers ───────────────────────────────────────────────────
    function showErr(msg) {
        modalErr.textContent   = msg;
        modalErr.style.display = 'block';
    }

    function setModalLoading(loading) {
        var selectIds = ['sch-subject','sch-teacher','sch-grade','sch-section','sch-day'];
        var inputs    = [fRoom, fStart, fEnd];

        if (loading) {
            selectIds.forEach(function (id) {
                var el = document.getElementById(id);
                el.innerHTML     = '<option value="">Loading...</option>';
                el.disabled      = true;
                el.style.opacity = '.5';
            });
            inputs.forEach(function (el) { el.disabled = true; el.style.opacity = '.4'; });
            saveBtn.disabled = true;
        } else {
            selectIds.forEach(function (id) {
                var el = document.getElementById(id);
                el.innerHTML     = selectOriginals[id];
                el.disabled      = false;
                el.style.opacity = '1';
            });
            inputs.forEach(function (el) { el.disabled = false; el.style.opacity = '1'; });
            saveBtn.disabled = false;
        }
    }

    // ── Time helpers ──────────────────────────────────────────────
    function timeToMinutes(t) {
        if (!t) return 0;
        var parts = t.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1] || 0);
    }

    function formatTime(t) {
        if (!t) return '';
        var parts = t.split(':');
        var h     = parseInt(parts[0]);
        var m     = parts[1] || '00';
        var ampm  = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return h + ':' + m + ' ' + ampm;
    }

    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Load & Render Grid ────────────────────────────────────────
    function loadSchedule() {
        var wrap = document.getElementById('sch-grid-wrap');
        wrap.innerHTML = '<div style="text-align:center;padding:48px;color:#94a3b8;"><span class="loading loading-dots loading-md"></span></div>';

        var params = {
            section_id:     document.getElementById('sch-filter-section').value,
            grade_level_id: document.getElementById('sch-filter-grade').value,
            teacher_id:     document.getElementById('sch-filter-teacher').value,
        };

        $.ajax({
            url: URL_LIST, data: params,
            success: function (res) { renderGrid(res.data); },
            error:   function () {
                wrap.innerHTML = '<div style="text-align:center;padding:48px;color:#dc2626;">Failed to load schedule.</div>';
            }
        });
    }

    function renderGrid(rows) {
        var wrap = document.getElementById('sch-grid-wrap');

        if (!rows || !rows.length) {
            wrap.innerHTML = '<div class="empty-state" style="padding:48px; text-align:center; color:#94a3b8;"><h3 style="margin:0 0 6px; color:#64748b;">No schedules found</h3><p style="margin:0; font-size:13px;">Add a schedule or adjust your filters.</p></div>';
            return;
        }

        // Compute dynamic time range from data (round down start, round up end)
        var minMin = Infinity, maxMin = -Infinity;
        rows.forEach(function (r) {
            var s = timeToMinutes(r.time_start);
            var e = timeToMinutes(r.time_end);
            if (s < minMin) minMin = s;
            if (e > maxMin) maxMin = e;
        });

        // Build hourly slots covering the data range, padded by 1 hour each side
        var startHour = Math.max(0,  Math.floor(minMin / 60) - 1);
        var endHour   = Math.min(23, Math.ceil(maxMin  / 60) + 1);
        var HOURS = [];
        for (var hh = startHour; hh <= endHour; hh++) {
            HOURS.push((hh < 10 ? '0' : '') + hh + ':00');
        }

        // Build lookup: day → list of schedule items
        var byDay = {};
        DAYS.forEach(function (d) { byDay[d] = []; });
        rows.forEach(function (r) { if (byDay[r.day]) byDay[r.day].push(r); });

        // ── Table: TIME rows × DAY columns ────────────────────────
        var colWidth = 'calc((100% - 90px) / 5)';

        var html = '<table style="width:100%; border-collapse:collapse; font-size:13px; table-layout:fixed;">';

        // Header
        html += '<colgroup>';
        html += '<col style="width:90px;">';
        DAYS.forEach(function () { html += '<col style="width:' + colWidth + ';">'; });
        html += '</colgroup>';

        html += '<thead><tr>';
        html += '<th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:600; color:#94a3b8; letter-spacing:.4px; border-bottom:1.5px solid #f1f5f9; background:#fff;">TIME</th>';
        DAYS.forEach(function (d) {
            html += '<th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:' + DAY_TEXT[d] + '; border-bottom:1.5px solid #f1f5f9; background:' + DAY_COLORS[d] + '; border-left:1px solid #f1f5f9;">' + d + '</th>';
        });
        html += '</tr></thead><tbody>';

        // Time rows
        for (var h = 0; h < HOURS.length - 1; h++) {
            var slotStart    = HOURS[h];
            var slotEnd      = HOURS[h + 1];
            var slotStartMin = timeToMinutes(slotStart);
            var slotEndMin   = timeToMinutes(slotEnd);

            html += '<tr>';

            // Time label cell
            html += '<td style="padding:8px 16px; color:#94a3b8; font-size:12px; white-space:nowrap; vertical-align:top; border-bottom:1px solid #f8fafc; background:#fafafa;">';
            html += formatTime(slotStart) + '<br><span style="font-size:10px; color:#cbd5e1;">– ' + formatTime(slotEnd) + '</span>';
            html += '</td>';

            // Day cells
            DAYS.forEach(function (d) {
                html += '<td style="padding:6px 8px; vertical-align:top; border-bottom:1px solid #f8fafc; border-left:1px solid #f1f5f9; width:' + colWidth + ';">';

                var hasItem = false;
                byDay[d].forEach(function (item) {
                    var itemStart = timeToMinutes(item.time_start);
                    var itemEnd   = timeToMinutes(item.time_end);

                    // Show item in the slot where it starts
                    if (itemStart >= slotStartMin && itemStart < slotEndMin) {
                        hasItem = true;
                        html += '<div style="background:' + DAY_COLORS[d] + '; border:1.5px solid ' + DAY_BORDERS[d] + '; border-radius:8px; padding:8px 10px; margin-bottom:4px; overflow:hidden;">';
                        html += '<div style="font-weight:700; color:' + DAY_TEXT[d] + '; font-size:12px; margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + esc(item.subject_name) + '</div>';
                        html += '<div style="font-size:11px; color:#475569; margin-bottom:2px;">👤 ' + esc(item.teacher_name) + '</div>';
                        html += '<div style="font-size:11px; color:#475569; margin-bottom:2px;">🏫 ' + esc(item.section_name) + ' · ' + esc(item.grade_level_name) + '</div>';
                        html += '<div style="font-size:11px; color:#475569; margin-bottom:6px;">📍 ' + esc(item.room) + ' · ' + formatTime(item.time_start) + ' – ' + formatTime(item.time_end) + '</div>';
                        html += '<div style="display:flex; gap:4px; flex-wrap:wrap;">';
                        html += '<button class="btn btn-outline" style="padding:2px 8px; font-size:11px;" data-action="edit" data-id="' + item.id + '">Edit</button>';
                        html += '<button class="btn" style="padding:2px 8px; font-size:11px; background:#fee2e2; color:#dc2626; border:1px solid #fecaca;" data-action="delete" data-id="' + item.id + '" data-name="' + esc(item.subject_name) + '">Delete</button>';
                        html += '</div>';
                        html += '</div>';
                    }
                });

                if (!hasItem) {
                    html += '<div style="height:40px;"></div>';
                }

                html += '</td>';
            });

            html += '</tr>';
        }

        html += '</tbody></table>';
        wrap.innerHTML = html;

        // Wire edit/delete buttons
        var rowMap = {};
        rows.forEach(function (r) { rowMap[r.id] = r; });

        wrap.querySelectorAll('[data-action="edit"]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var row = rowMap[this.dataset.id];
                if (row) openModal('Edit Schedule', 'Update Schedule', row);
            });
        });

        wrap.querySelectorAll('[data-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var id   = this.dataset.id;
                var name = this.dataset.name;
                showConfirmationModal('Delete Schedule', 'Are you sure you want to delete "' + name + '"?', function () {
                    $.ajax({
                        url: URL_DESTROY, method: 'POST',
                        data: { id: id, _token: CSRF },
                        success: function (res) {
                            showPopup('Deleted', res.message, 'success');
                            loadSchedule();
                        },
                        error: function (xhr) {
                            showPopup('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed.', 'error');
                        }
                    });
                });
            });
        });
    }

    // ── Filter section dropdown by grade (toolbar) ────────────────
    document.getElementById('sch-filter-grade').addEventListener('change', function () {
        var gid    = this.value;
        var secSel = document.getElementById('sch-filter-section');
        Array.from(secSel.options).forEach(function (opt) {
            if (!opt.value) return;
            opt.hidden = gid ? opt.dataset.grade !== gid : false;
        });
        secSel.value = '';
        loadSchedule();
    });

    document.getElementById('sch-filter-section').addEventListener('change', loadSchedule);
    document.getElementById('sch-filter-teacher').addEventListener('change', loadSchedule);

    document.getElementById('btn-sch-reset').addEventListener('click', function () {
        document.getElementById('sch-filter-grade').value   = '';
        document.getElementById('sch-filter-section').value = '';
        document.getElementById('sch-filter-teacher').value = '';
        Array.from(document.getElementById('sch-filter-section').options).forEach(function (o) { o.hidden = false; });
        loadSchedule();
    });

    // ── Init ──────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        loadSchedule();
    });

})();
</script>
@endpush