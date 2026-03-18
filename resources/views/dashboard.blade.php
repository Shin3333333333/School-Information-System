@extends('layouts.app')

@section('title', 'Dashboard — School Information System')

@section('page-title')
    <h2><span style="text-transform:capitalize;">{{ $roleName ?? '' }}</span> Dashboard</h2>
@endsection

@section('content')

@if(auth()->user()->role->name === 'Admin')

    {{-- ── Admin Dashboard ── --}}
    {{-- Row 1: Stat Cards --}}
    <div class="db-grid-4">

        <div class="db-stat s-blue">
            <div class="db-stat-top">
                <span class="db-stat-label">Total Students</span>
                <div class="db-stat-icon ic-blue">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="db-stat-num">{{ number_format($totalStudents ?? 0) }}</div>
            <div class="db-stat-foot"><span class="chip">+{{ $newThisMonth ?? 0 }}</span> enrolled this month</div>
        </div>

        <div class="db-stat s-green">
            <div class="db-stat-top">
                <span class="db-stat-label">Teachers</span>
                <div class="db-stat-icon ic-green">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="db-stat-num">{{ number_format($totalTeachers ?? 0) }}</div>
            <div class="db-stat-foot"><span class="chip">+{{ $newTeachersThisMonth ?? 0 }}</span> joined this month</div>
        </div>

        <div class="db-stat s-amber">
            <div class="db-stat-top">
                <span class="db-stat-label">Sections</span>
                <div class="db-stat-icon ic-amber">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="db-stat-num">{{ number_format($totalSections ?? 0) }}</div>
            <div class="db-stat-foot">Across all grade levels</div>
        </div>

        <div class="db-stat s-red">
            <div class="db-stat-top">
                <span class="db-stat-label">Total Users</span>
                <div class="db-stat-icon ic-red">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="db-stat-num">{{ number_format(($totalStudents ?? 0) + ($totalTeachers ?? 0)) }}</div>
            <div class="db-stat-foot">Students &amp; teachers combined</div>
        </div>

    </div>

    {{-- Row 2: KPI + Enrollment Trend --}}
    <div class="db-row c2">

        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">School Overview</div>
                    <div class="db-card-sub">
                        Academic Year 
                        @if($activeAcademicYear)
                            <strong style="color:#f1f5f9;">{{ $activeAcademicYear->year_label }}</strong>
                        @else
                            <span style="color:#ef4444; font-weight:600;">Not Set</span>
                        @endif
                    </div>
                </div>
                @if(!$activeAcademicYear)
                <a href="{{ route('academic-years.index') }}" class="db-card-link" style="color:#f87171; font-weight:700;">
                    Set Now
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </a>
                @endif
            </div>
            <div class="db-divider"></div>
            <div class="db-card-body" style="padding-top:0;">
                <div class="kpi-row">
                    <div class="kpi-box">
                        <div class="kpi-ico ki-blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="kpi-label">Announcements</div>
                            <div class="kpi-val">{{ $totalAnnouncements ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-ico ki-green">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="kpi-label">Events</div>
                            <div class="kpi-val">{{ $totalEvents ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-ico ki-amber">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="kpi-label">Active Policies</div>
                            <div class="kpi-val">{{ $totalActivePolicies ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Enrollment Trend</div>
                    <div class="db-card-sub">New students — last 6 months</div>
                </div>
            </div>
            <div class="db-divider"></div>
            <div class="db-card-body" style="padding-top:0;">
                <div style="position:relative; height:130px;">
                    <canvas id="enrollmentTrendChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 3: Bar Chart + Recent Announcements --}}
    <div class="db-row c3-2">

        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Students per Grade Level</div>
                    <div class="db-card-sub">Active enrolled students</div>
                </div>
            </div>
            <div class="db-divider"></div>
            <div class="db-card-body" style="padding-top:0;">
                <div style="position:relative; height:200px;">
                    <canvas id="studentsPerGradeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <div class="db-card-title">Recent Announcements</div>
                    <div class="db-card-sub">Latest posted</div>
                </div>
                <a href="{{ route('admin.announcements') }}" class="db-card-link">
                    All
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>
            <div class="db-divider"></div>
            <div class="db-card-body" style="padding-top:0;">
                @php $abColors = ['ab-blue','ab-amber','ab-green','ab-red']; @endphp
                @forelse($upcomingEvents ?? [] as $i => $ev)
                <div class="ann-item">
                    <div class="ann-badge {{ $abColors[$i % count($abColors)] }}">
                        {{ \Carbon\Carbon::parse($ev->date_posted)->format('M') }}<br>
                        {{ \Carbon\Carbon::parse($ev->date_posted)->format('d') }}
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div class="ann-title">{{ $ev->title }}</div>
                        <div class="ann-sub">{{ $ev->subject_name ?? 'General' }} — {{ $ev->section_names ?? 'All' }}</div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:28px 0;">
                    <p>No announcements found.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Row 4: Recently Added Users --}}
    <div class="db-card" style="margin-bottom:20px;">
        <div class="db-card-head">
            <div>
                <div class="db-card-title">Recently Added Users</div>
                <div class="db-card-sub">Latest 5 accounts created</div>
            </div>
            <a href="{{ route('students.index') }}" class="db-card-link">
                View all
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
        <div class="db-divider"></div>
        <div style="padding:0 20px 12px;">
            @php
                $avColors = ['admin'=>'av-blue','teacher'=>'av-green','student'=>'av-amber'];
                $rtColors = ['admin'=>'rt-admin','teacher'=>'rt-teacher','student'=>'rt-student'];
            @endphp
            @forelse($recentUsers ?? [] as $ru)
            @php
                $role     = strtolower($ru->role_name);
                $initials = strtoupper(substr($ru->name,0,1)).strtoupper(substr(strstr($ru->name,' '),1,1));
            @endphp
            <div class="usr-row">
                <div class="usr-av {{ $avColors[$role] ?? 'av-blue' }}">{{ $initials }}</div>
                <div style="flex:1; min-width:0;">
                    <div class="usr-name">{{ $ru->name }}</div>
                    <div class="usr-mail">{{ $ru->email }}</div>
                </div>
                <div class="usr-time">{{ \Carbon\Carbon::parse($ru->created_at)->diffForHumans() }}</div>
                <span class="role-tag {{ $rtColors[$role] ?? 'rt-student' }}">{{ $ru->role_name }}</span>
            </div>
            @empty
            <div class="empty-state" style="padding:24px 0;"><p>No recent users found.</p></div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const gradeLabels = @json(($studentsPerGrade ?? collect())->pluck('grade_level_name'));
        const gradeCounts = @json(($studentsPerGrade ?? collect())->pluck('total'));
        const trendLabels = @json(($enrollmentTrend ?? collect())->pluck('month'));
        const trendCounts = @json(($enrollmentTrend ?? collect())->pluck('total'));

        const darkTooltip = {
            backgroundColor: '#1e293b', titleColor: '#475569', bodyColor: '#f1f5f9',
            borderColor: 'rgba(255,255,255,0.08)', borderWidth: 1, cornerRadius: 8, padding: 10,
        };
        const darkScales = {
            y: { beginAtZero:true, ticks:{ stepSize:1, color:'#334155', font:{size:11} }, grid:{ color:'rgba(255,255,255,0.04)', drawBorder:false } },
            x: { ticks:{ color:'#475569', font:{size:11} }, grid:{ display:false, drawBorder:false } }
        };

        if (gradeLabels.length > 0) {
            new Chart(document.getElementById('studentsPerGradeChart'), {
                type: 'bar',
                data: {
                    labels: gradeLabels,
                    datasets: [{ data: gradeCounts,
                        backgroundColor: ['rgba(96,165,250,0.12)','rgba(74,222,128,0.12)','rgba(251,191,36,0.12)','rgba(248,113,113,0.12)','rgba(167,139,250,0.12)','rgba(34,211,238,0.12)'],
                        borderColor:      ['#60a5fa','#4ade80','#fbbf24','#f87171','#a78bfa','#22d3ee'],
                        borderWidth: 2, borderRadius: 7, borderSkipped: false,
                    }]
                },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{...darkTooltip, callbacks:{label:ctx=>` ${ctx.parsed.y} students`}} }, scales: darkScales }
            });
        }

        if (trendLabels.length > 0) {
            new Chart(document.getElementById('enrollmentTrendChart'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{ data: trendCounts,
                        borderColor:'#60a5fa', backgroundColor:'rgba(96,165,250,0.06)',
                        borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#60a5fa',
                        pointBorderColor:'#111827', pointBorderWidth:2, fill:true, tension:0.42,
                    }]
                },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{...darkTooltip, callbacks:{label:ctx=>` ${ctx.parsed.y} new students`}} }, scales: darkScales }
            });
        }
    </script>

@elseif(auth()->user()->role->name === 'Teacher')

    {{-- ── Teacher Dashboard ── --}}
    {{-- Academic Year Display --}}
    <div class="role-banner banner-teacher">
        <div class="banner-content">
            <div>
                <div class="banner-title">Welcome back, {{ auth()->user()->name }}!</div>
                <div class="banner-subtitle">
                    Academic Year: 
                    @if($activeAcademicYear)
                        <strong>{{ $activeAcademicYear->year_label }}</strong>
                    @else
                        <span style="color:#fbbf24;">Year not set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom:20px; margin-top:16px;">
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
            <div class="stat-value">{{ count($teacherClasses ?? []) }}</div>
            <div class="stat-meta">Assigned to you</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Students</span>
                <div class="stat-icon blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
            <div class="stat-meta">Across all classes</div>
        </div>
    </div>

    <div class="dashboard-widgets">
        <div class="card dashboard-widget-card">
            <div class="card-header">
                <span class="section-title">Quick Actions</span>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                <a href="{{ route('teacher.class-list') }}" class="btn btn-primary">View Class List</a>
                <a href="{{ route('teacher.announcements') }}" class="btn btn-outline">Manage Announcements</a>
            </div>
        </div>

        <div class="card dashboard-widget-card">
            <div class="card-header">
                <span class="section-title">Your Classes</span>
            </div>
            <div class="card-body">
                @forelse($teacherClasses ?? [] as $class)
                    <div style="padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px;">
                        <div style="color:#f1f5f9; font-weight:600;">{{ $class->name }}</div>
                        <div style="color:#475569; font-size:12px; margin-top:2px;">Grade {{ $class->grade_level_id }}</div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:20px 0;">
                        <p>No classes assigned</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@elseif(auth()->user()->role->name === 'Student')

    {{-- ── Student Dashboard ── --}}
    {{-- Academic Year Display --}}
    <div class="role-banner banner-student">
        <div class="banner-content">
            <div>
                <div class="banner-title">Welcome, {{ auth()->user()->name }}!</div>
                <div class="banner-subtitle">
                    Academic Year: 
                    @if($activeAcademicYear)
                        <strong>{{ $activeAcademicYear->year_label }}</strong>
                    @else
                        <span style="color:#fbbf24;">Year not set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom:20px; margin-top:16px;">
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
            <div class="stat-value">{{ count($studentClasses ?? []) }}</div>
            <div class="stat-meta">Scheduled today</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">New Announcements</span>
                <div class="stat-icon blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $unreadAnnouncements ?? 0 }}</div>
            <div class="stat-meta">Unread</div>
        </div>
    </div>

    <div class="dashboard-widgets">
        <div class="card dashboard-widget-card">
            <div class="card-header">
                <span class="section-title">Quick Actions</span>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                <a href="{{ route('student.schedule') }}" class="btn btn-primary">View Schedule</a>
                <a href="{{ route('student.announcements') }}" class="btn btn-outline">View Announcements</a>
            </div>
        </div>

        <div class="card dashboard-widget-card">
            <div class="card-header">
                <span class="section-title">Your Schedule</span>
            </div>
            <div class="card-body">
                @forelse($studentClasses ?? [] as $class)
                    <div style="padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px;">
                        <div style="color:#f1f5f9; font-weight:600;">{{ $class->subject_name }}</div>
                        <div style="color:#475569; font-size:12px; margin-top:2px;">{{ $class->teacher_name ?? 'TBA' }} • {{ $class->start_time ?? 'TBA' }}</div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:20px 0;">
                        <p>No classes scheduled</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@else

    <div class="empty-state">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <h3>Dashboard unavailable</h3>
        <p>No dashboard content found for your role.</p>
    </div>

@endif

<style>
/* ── Dashboard-specific styles ───────────────────────────────── */
.db-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:16px; }
.db-row    { display:grid; gap:16px; margin-bottom:16px; }
.db-row.c2   { grid-template-columns:1fr 1fr; }
.db-row.c3-2 { grid-template-columns:1.55fr 1fr; }

.db-stat {
    background: #111827; border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px; padding: 18px 20px 16px;
    position: relative; overflow: hidden;
    transition: transform .18s, border-color .18s;
    animation: fadeUp .35s ease both;
}
.db-stat::before { content:''; position:absolute; inset:0; border-radius:14px; background:radial-gradient(ellipse at top left, rgba(255,255,255,0.03) 0%, transparent 60%); pointer-events:none; }
.db-stat:hover   { transform:translateY(-3px); border-color:rgba(255,255,255,0.14); }
.db-stat::after  { content:''; position:absolute; top:0; left:0; right:0; height:2px; border-radius:14px 14px 0 0; }
.db-stat.s-blue::after  { background:linear-gradient(90deg,#2563eb,#60a5fa); }
.db-stat.s-green::after { background:linear-gradient(90deg,#16a34a,#4ade80); }
.db-stat.s-amber::after { background:linear-gradient(90deg,#d97706,#fbbf24); }
.db-stat.s-red::after   { background:linear-gradient(90deg,#dc2626,#f87171); }
.db-stat:nth-child(1){animation-delay:0s} .db-stat:nth-child(2){animation-delay:.06s}
.db-stat:nth-child(3){animation-delay:.12s} .db-stat:nth-child(4){animation-delay:.18s}

.db-stat-top    { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.db-stat-label  { font-size:10.5px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; color:#475569; }
.db-stat-icon   { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.db-stat-icon.ic-blue  { background:rgba(37,99,235,0.15);  color:#60a5fa; }
.db-stat-icon.ic-green { background:rgba(22,163,74,0.15);   color:#4ade80; }
.db-stat-icon.ic-amber { background:rgba(217,119,6,0.15);   color:#fbbf24; }
.db-stat-icon.ic-red   { background:rgba(220,38,38,0.15);   color:#f87171; }
.db-stat-num   { font-size:34px; font-weight:800; color:#f8fafc; letter-spacing:-2px; line-height:1; margin-bottom:7px; font-family:var(--font-display); font-variant-numeric:tabular-nums; }
.db-stat-foot  { font-size:12px; color:#475569; display:flex; align-items:center; gap:5px; }
.db-stat-foot .chip { background:rgba(34,197,94,0.12); color:#4ade80; font-weight:700; padding:1px 7px; border-radius:20px; font-size:11px; }

.db-card       { background:#111827; border:1px solid rgba(255,255,255,0.07); border-radius:14px; overflow:hidden; animation:fadeUp .35s .22s ease both; }
.db-card-head  { display:flex; align-items:flex-start; justify-content:space-between; padding:18px 20px 0; margin-bottom:14px; }
.db-card-title { font-size:13.5px; font-weight:700; color:#e2e8f0; letter-spacing:-.15px; }
.db-card-sub   { font-size:11.5px; color:#334155; margin-top:2px; }
.db-card-link  { font-size:12px; color:#60a5fa; font-weight:600; text-decoration:none; display:flex; align-items:center; gap:3px; transition:gap .15s; white-space:nowrap; }
.db-card-link:hover { gap:6px; color:#93c5fd; }
.db-card-link[style*="f87171"]:hover { color:#fb7185; }
.db-card-body  { padding:0 20px 18px; }
.db-divider    { height:1px; background:rgba(255,255,255,0.05); margin:0 20px 14px; }

.kpi-row { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
.kpi-box { background:#0f172a; border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:13px 14px; display:flex; align-items:center; gap:12px; transition:border-color .15s, background .15s; }
.kpi-box:hover { background:#1a2540; border-color:rgba(255,255,255,0.12); }
.kpi-ico { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.kpi-ico.ki-blue  { background:rgba(37,99,235,0.18);  color:#60a5fa; }
.kpi-ico.ki-green { background:rgba(22,163,74,0.18);   color:#4ade80; }
.kpi-ico.ki-amber { background:rgba(217,119,6,0.18);   color:#fbbf24; }
.kpi-label { font-size:10.5px; color:#475569; font-weight:600; letter-spacing:.3px; text-transform:uppercase; margin-bottom:3px; }
.kpi-val   { font-size:22px; font-weight:800; color:#f1f5f9; letter-spacing:-.5px; font-family:var(--font-display); }

.ann-item  { display:flex; align-items:flex-start; gap:12px; padding:11px 0; border-bottom:1px solid rgba(255,255,255,0.05); }
.ann-item:last-child { border-bottom:none; }
.ann-badge { width:40px; text-align:center; border-radius:8px; padding:5px 4px; font-size:10.5px; font-weight:700; line-height:1.4; flex-shrink:0; }
.ann-badge.ab-blue  { background:rgba(37,99,235,0.18);  color:#60a5fa; }
.ann-badge.ab-amber { background:rgba(217,119,6,0.18);  color:#fbbf24; }
.ann-badge.ab-green { background:rgba(22,163,74,0.18);  color:#4ade80; }
.ann-badge.ab-red   { background:rgba(220,38,38,0.18);  color:#f87171; }
.ann-title { font-size:13px; font-weight:600; color:#e2e8f0; line-height:1.3; }
.ann-sub   { font-size:11.5px; color:#475569; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.usr-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.05); }
.usr-row:last-child { border-bottom:none; }
.usr-av  { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; font-family:var(--font-display); }
.av-blue  { background:rgba(37,99,235,0.2);  color:#60a5fa; }
.av-green { background:rgba(22,163,74,0.2);   color:#4ade80; }
.av-amber { background:rgba(217,119,6,0.2);   color:#fbbf24; }
.usr-name { font-size:13px; font-weight:600; color:#e2e8f0; line-height:1.3; }
.usr-mail { font-size:11.5px; color:#334155; margin-top:1px; }
.usr-time { font-size:11.5px; color:#1e293b; margin-right:10px; white-space:nowrap; }
.role-tag { font-size:10.5px; font-weight:700; padding:2px 9px; border-radius:20px; flex-shrink:0; letter-spacing:.15px; }
.rt-admin   { background:rgba(37,99,235,0.18);  color:#60a5fa; }
.rt-teacher { background:rgba(22,163,74,0.18);   color:#4ade80; }
.rt-student { background:rgba(217,119,6,0.18);   color:#fbbf24; }

/* ── Role-specific banners ──────────────────────────────────── */
.role-banner {
    background: linear-gradient(135deg, #111827 0%, #0f172a 100%);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    animation: slideDown .4s ease-out;
}

.role-banner.banner-teacher {
    border-color: rgba(74,222,128,0.2);
    background: linear-gradient(135deg, rgba(74,222,128,0.08) 0%, #111827 100%);
}

.role-banner.banner-student {
    border-color: rgba(251,191,36,0.2);
    background: linear-gradient(135deg, rgba(251,191,36,0.08) 0%, #111827 100%);
}

.banner-content { display: flex; align-items: center; justify-content: space-between; }
.banner-title { font-size: 16px; font-weight: 700; color: #f1f5f9; }
.banner-subtitle { font-size: 13px; color: #94a3b8; margin-top: 4px; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
}

.stat-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    padding: 16px;
    transition: all .2s;
}

.stat-card:hover {
    border-color: rgba(255,255,255,0.14);
    background: #0f172a;
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #475569;
    letter-spacing: 0.5px;
}

.stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon.amber {
    background: rgba(217, 119, 6, 0.15);
    color: #fbbf24;
}

.stat-icon.blue {
    background: rgba(37, 99, 235, 0.15);
    color: #60a5fa;
}

.stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #f1f5f9;
    line-height: 1;
    margin-bottom: 6px;
    font-family: var(--font-display);
}

.stat-meta {
    font-size: 11.5px;
    color: #475569;
}

.dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 14px;
}

.dashboard-widget-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: #e2e8f0;
}

.card-body {
    padding: 12px 16px;
}

.empty-state {
    text-align: center;
    color: #475569;
    font-size: 13px;
}

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

@media (max-width:1100px) { .db-grid-4{grid-template-columns:repeat(2,1fr)} }
@media (max-width:860px)  { .db-row.c2,.db-row.c3-2{grid-template-columns:1fr} .kpi-row{grid-template-columns:1fr} }
</style>

@endsection