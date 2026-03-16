<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // ── Max turns to keep in session memory ──────────────────────────────────
    private const MAX_HISTORY = 10;

    // ── Route map based on user role ─────────────────────────────────────────
   // ── Route map based on user role ─────────────────────────────────────────
private function getRoutesByRole(string $role): array
{
    if ($role === 'Admin') {
        return [
            'dashboard'       => ['url' => '/dashboard',           'label' => 'Dashboard'],
            'user management' => ['url' => '/students',            'label' => 'User Management'],
            'students'        => ['url' => '/students',            'label' => 'User Management'],
            'announcements'   => ['url' => '/admin/announcements', 'label' => 'Announcements'],
            'calendar'        => ['url' => '/admin/calendar',      'label' => 'Calendar'],
            'events'          => ['url' => '/admin/calendar',      'label' => 'Calendar'],
            'policies'        => ['url' => '/admin/policies',      'label' => 'Policies'],
            'school info'     => ['url' => '/admin/policies',      'label' => 'Policies & School Info'],
            'mission'         => ['url' => '/admin/policies',      'label' => 'Policies & School Info'],
            'vision'          => ['url' => '/admin/policies',      'label' => 'Policies & School Info'],
            'sections'        => ['url' => '/admin/sections',      'label' => 'Sections'],
            'subjects'        => ['url' => '/admin/subjects',      'label' => 'Subjects'],
            'schedule'        => ['url' => '/admin/schedule',      'label' => 'Schedule Management'],
            'academic years'  => ['url' => '/academic-years',      'label' => 'Academic Years'],
        ];
    } elseif ($role === 'Teacher') {
        return [
            'dashboard'       => ['url' => '/dashboard',           'label' => 'Dashboard'],
            'my schedule'     => ['url' => '/teacher/schedule',    'label' => 'My Schedule'],
            'schedule'        => ['url' => '/teacher/schedule',    'label' => 'My Schedule'],
            'announcements'   => ['url' => '/announcements',       'label' => 'Announcements'],
            'class list'      => ['url' => '/teacher/class-list',  'label' => 'Class List'],
            'my classes'      => ['url' => '/teacher/class-list',  'label' => 'Class List'],
            'grades'          => ['url' => '/grades',              'label' => 'Grades'],
            'calendar'        => ['url' => '/teacher/calendar',    'label' => 'Calendar'],
            'events'          => ['url' => '/teacher/calendar',    'label' => 'Calendar'],
            'policies'        => ['url' => '/teacher/policies',    'label' => 'Policies'],
            'school info'     => ['url' => '/teacher/policies',    'label' => 'Policies & School Info'],
        ];
    } else { // Student
        return [
            'dashboard'       => ['url' => '/dashboard',           'label' => 'Dashboard'],
            'my schedule'     => ['url' => '/student/schedule',    'label' => 'My Schedule'],
            'schedule'        => ['url' => '/student/schedule',    'label' => 'My Schedule'],
            'announcements'   => ['url' => '/announcements',       'label' => 'Announcements'],
            'my grades'       => ['url' => '/grades',              'label' => 'My Grades'],
            'grades'          => ['url' => '/grades',              'label' => 'My Grades'],
            'calendar'        => ['url' => '/student/calendar',    'label' => 'Calendar'],
            'events'          => ['url' => '/student/calendar',    'label' => 'Calendar'],
            'policies'        => ['url' => '/student/policies',    'label' => 'Policies'],
            'school info'     => ['url' => '/student/policies',    'label' => 'Policies & School Info'],
        ];
    }
}

    // ── Intent definitions: each intent has weighted keyword groups ───────────
    private array $intents = [
        'profile' => [
            'keywords' => [
                'my profile' => 3, 'my info' => 3, 'my details' => 3,
                'my credentials' => 3, 'my account' => 3, 'who am i' => 3,
                'show my' => 2, 'get my' => 2, 'my data' => 2,
                'about me' => 2, 'my record' => 2, 'my information' => 2,
            ],
            'min_score' => 2,
        ],
        'students' => [
            'keywords' => [
                'list students' => 3, 'show students' => 3, 'all students' => 3,
                'student list' => 3, 'how many students' => 3, 'student names' => 3,
                'enrolled students' => 3, 'students enrolled' => 3,
                'students name' => 3, 'name of student' => 3, 'names of student' => 3,
                'who are the students' => 3, 'our students' => 3,
                'student' => 1, 'students' => 1, 'how many' => 1, 'enrolled' => 1,
            ],
            'min_score' => 2,
        ],
        'teachers' => [
            'keywords' => [
                'list teachers' => 3, 'show teachers' => 3, 'all teachers' => 3,
                'teacher list' => 3, 'how many teachers' => 3, 'teacher names' => 3,
                'faculty list' => 3, 'faculty members' => 3,
                'teachers name' => 3, 'name of teacher' => 3, 'names of teacher' => 3,
                'who are the teachers' => 3, 'who teaches' => 3, 'our teachers' => 3,
                'teacher' => 1, 'teachers' => 1, 'faculty' => 1,
            ],
            'min_score' => 2,
        ],
        'announcements' => [
            'keywords' => [
                'all announcements' => 3, 'show announcements' => 3, 'list announcements' => 3,
                'recent announcements' => 3, 'my announcements' => 3, 'latest announcements' => 3,
                'announcements i posted' => 3, 'posted announcements' => 3,
                'announcement' => 1, 'announcements' => 1, 'notices' => 1, 'posts' => 1,
            ],
            'min_score' => 2,
        ],
        'events' => [
            'keywords' => [
                'upcoming events' => 3, 'next events' => 3, 'show events' => 3,
                'list events' => 3, 'all events' => 3, 'events this month' => 3,
                'school events' => 3, 'what events' => 3, 'any events' => 3,
                'schedule' => 2, 'activities' => 2, 'programs' => 2,
                'event' => 1, 'events' => 1, 'calendar' => 1, 'happening' => 1,
            ],
            'min_score' => 2,
        ],
        'policies' => [
            'keywords' => [
                'show policies' => 3, 'list policies' => 3, 'active policies' => 3,
                'school policies' => 3, 'all policies' => 3, 'current policies' => 3,
                'what are the rules' => 3, 'school rules' => 3,
                'policy' => 1, 'policies' => 1, 'rules' => 1, 'regulations' => 1,
            ],
            'min_score' => 2,
        ],
        'sections' => [
            'keywords' => [
                'show sections' => 3, 'list sections' => 3, 'all sections' => 3,
                'my sections' => 3, 'my class' => 3, 'my classes' => 3,
                'what sections' => 3, 'available sections' => 3,
                'section' => 1, 'sections' => 1, 'class' => 1, 'classes' => 1,
            ],
            'min_score' => 2,
        ],
        'subjects' => [
            'keywords' => [
                'show subjects' => 3, 'list subjects' => 3, 'all subjects' => 3,
                'my subjects' => 3, 'what subjects' => 3, 'available subjects' => 3,
                'subject' => 1, 'subjects' => 1,
            ],
            'min_score' => 2,
        ],
        'school_info' => [
            'keywords' => [
                'school info' => 3, 'school information' => 3, 'about the school' => 3,
                'mission vision' => 3, 'core values' => 3,
                'mission' => 2, 'vision' => 2, 'values' => 2,
                'school motto' => 2, 'school background' => 2,
            ],
            'min_score' => 2,
        ],
        'grades' => [
            'keywords' => [
                'my grades' => 3, 'my marks' => 3, 'my scores' => 3,
                'show grades' => 2, 'view grades' => 2, 'grades for' => 2,
                'what are my grades' => 3, 'grade' => 1, 'grades' => 1,
            ],
            'min_score' => 2,
        ],
        'summary' => [
            'keywords' => [
                'summary' => 3, 'overview' => 3, 'report' => 3,
                'statistics' => 3, 'stats' => 3, 'dashboard summary' => 3,
                'school summary' => 3, 'give me a summary' => 3,
                'show me everything' => 2, 'full report' => 2,
            ],
            'min_score' => 3,
        ],
        'navigation' => [
            'keywords' => [
                'where is' => 3, 'how do i find' => 3, 'go to' => 3,
                'navigate to' => 3, 'take me to' => 3, 'open' => 2,
                'find the page' => 3, 'where can i' => 3, 'link to' => 3,
                'how to access' => 3, 'where do i' => 2,
                'page' => 1, 'link' => 1, 'access' => 1,
            ],
            'min_score' => 2,
        ],
        'academic_years' => [
            'keywords' => [
                'academic year' => 3, 'school year' => 3, 'current academic year' => 3,
                'active academic year' => 3, 'what academic year' => 2,
                'years' => 1,
            ],
            'min_score' => 2,
        ],
    ];

    // ── Casual/greeting patterns — skip DB entirely ───────────────────────────
    private array $casualPatterns = [
        'hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening',
        'how are you', 'what can you do', 'thanks', 'thank you',
        'okay', 'ok', 'sure', 'alright', 'bye', 'goodbye', 'sup', 'yo',
        'what is sis', 'help', 'what can you help', 'what do you do',
    ];

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        try {
            $user     = auth()->user();
            $userName = $user->name;
            $userRole = $user->role->name;
            $message  = trim($request->message);
            $msgLower = strtolower($message);

            // ── Session key per user ──────────────────────────────────────────
            $sessionKey = 'chat_history_' . $user->id;

            // ── Load existing history ─────────────────────────────────────────
            $history = cache()->get($sessionKey, []);

            // ── Detect casual message ─────────────────────────────────────────
            $isCasual = $this->isCasualMessage($msgLower);

            // ── Detect intent via scoring ─────────────────────────────────────
            $detectedIntent = null;
            $navigationLink = null;
            $dynamicContext = '';

            if (!$isCasual) {
                $detectedIntent = $this->detectIntent($msgLower);

                Log::info("Chat Debug — message: '{$msgLower}' | intent: '{$detectedIntent}' | role: {$userRole}");

                // If navigation intent, also figure out which page ────────────
                if ($detectedIntent === 'navigation') {
                    $routes = $this->getRoutesByRole($userRole);
                    foreach ($routes as $keyword => $route) {
                        if (str_contains($msgLower, $keyword)) {
                            $navigationLink = $route;
                            break;
                        }
                    }
                }

                // ── Fetch data based on intent + role ─────────────────────────
                $dynamicContext = $this->fetchDataForIntent($detectedIntent, $user, $userRole);

                Log::info("Chat Debug — dynamicContext: " . ($dynamicContext ?: 'EMPTY — no DB data fetched'));
            }

            // ── Static school snapshot ────────────────────────────────────────
            $totalStudents  = DB::table('users')->where('role_id', 2)->where('status', 'Active')->count();
            $totalTeachers  = DB::table('users')->where('role_id', 1)->where('status', 'Active')->count();
            $newThisMonth   = DB::table('users')->where('role_id', 2)->where('status', 'Active')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();
            $announcements  = DB::table('announcements')->count();
            $events         = DB::table('events')->count();
            $activePolicies = DB::table('policies')->where('status', 'Active')->count();
            $sections       = DB::table('section')->count();
            $gradeLevels    = DB::table('grade_level')->pluck('grade_level_name')->implode(', ');

            // ── Role-specific instructions ────────────────────────────────────
            $roleInstructions = $this->getRoleInstructions($userRole);

            $navContext = $navigationLink
            ? "\nIMPORTANT NAVIGATION INSTRUCTION: The user is asking about '{$navigationLink['label']}'. "
            . "You MUST use this EXACT link when providing navigation: [{$navigationLink['label']}]({$navigationLink['url']}). "
            . "DO NOT create or suggest any other links. ONLY use the link provided above.\n"
            : '';

        // Also add this to the STRICT OUTPUT RULES section in the system prompt:
        $systemPrompt = "
            You are SIS Assistant, a smart and friendly AI built into a School Information System for a Philippine junior/senior high school.

            {$roleInstructions}

            PERSONALITY:
            - Warm, professional, and concise.
            - Address the user by their first name naturally when it fits.
            - For greetings — respond with a SHORT friendly greeting ONLY. Do NOT list data or stats.
            - For casual small talk, keep it to 1-2 sentences max.
            - Only provide data when the user explicitly asks for it.
            - Never volunteer information the user did not ask for.

            STRICT OUTPUT RULES — NEVER BREAK THESE:
            - Output ONLY the final response. Nothing else.
            - NEVER include thinking steps, reasoning, drafts, or self-checks.
            - NEVER start with 'Thinking:', 'Draft:', 'Final Output:', 'Step:', or any internal commentary.
            - NEVER explain what you are about to do. Just do it.
            - Keep answers under 150 words unless listing data that requires more.
            - **ABSOLUTELY DO NOT create or suggest any navigation links yourself.** 
            - **ONLY use navigation links that are explicitly provided in the NAVIGATION section below.**
            - If no navigation link is provided, DO NOT mention any links at all.

            LOGGED IN USER:
            - Name: {$userName}
            - Role: {$userRole}
            - Email: {$user->email}

            {$navContext}

            SCHOOL DATA SNAPSHOT (as of " . now()->format('F d, Y') . "):
            - Total active students: {$totalStudents}
            - Total active teachers: {$totalTeachers}
            - New students enrolled this month: {$newThisMonth}
            - Total sections: {$sections}
            - Grade levels offered: {$gradeLevels}
            - Total announcements posted: {$announcements}
            - Total school events: {$events}
            - Active policies: {$activePolicies}

            " . ($dynamicContext ? "ADDITIONAL DATA FOR THIS QUERY:\n{$dynamicContext}" : '') . "
        ";

            // ── Build messages array with history ─────────────────────────────
            $messages = [['role' => 'system', 'content' => $systemPrompt]];

            // Append past turns (already formatted as role/content pairs)
            foreach ($history as $turn) {
                $messages[] = $turn;
            }

            // Append current user message
            $messages[] = ['role' => 'user', 'content' => $message];

            // ── Call OpenRouter ───────────────────────────────────────────────
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => env('APP_URL', 'http://localhost'),
                'X-Title'       => 'School Information System',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'      => 'mistralai/mixtral-8x7b-instruct',
                'max_tokens' => 1024,
                'messages'   => $messages,
            ]);

            Log::info("OpenRouter Status: " . $response->status());

            $data = $response->json();

            if (isset($data['error'])) {
                Log::error("OpenRouter API Error: " . json_encode($data['error']));
                return response()->json([
                    'status' => 'success',
                    'reply'  => 'API Error: ' . $data['error']['message']
                ]);
            }

            // ── Parse reply ───────────────────────────────────────────────────
            $reply       = null;
            $message_obj = isset($data['choices'][0]['message']) ? $data['choices'][0]['message'] : null;

            if ($message_obj) {
                if (!empty($message_obj['content'])) {
                    $reply = $message_obj['content'];
                }
                if ($reply) {
                    $reply = preg_replace('/<think>.*?<\/think>/s', '', $reply);
                    $reply = trim($reply);
                }
                if (empty($reply) && !empty($message_obj['reasoning'])) {
                    $paragraphs = array_filter(explode("\n\n", $message_obj['reasoning']));
                    $reply      = trim(end($paragraphs));
                }
            }

            // ── Clean leaked internal thinking ────────────────────────────────
            $reply = $this->cleanLeaks($reply);

            // ── Convert markdown links to HTML ────────────────────────────────
            $reply = $this->renderLinks($reply);

            if (empty($reply)) {
                Log::warning("Empty reply. Full response: " . json_encode($data));
                $reply = 'I received your message but could not form a response. Please try rephrasing.';
            }

            // ── Save turn to session history ──────────────────────────────────
            $history[] = ['role' => 'user',      'content' => $message];
            $history[] = ['role' => 'assistant',  'content' => $reply];

            // Keep only last N turns
            if (count($history) > self::MAX_HISTORY * 2) {
                $history = array_slice($history, -self::MAX_HISTORY * 2);
            }

            cache()->put($sessionKey, $history, now()->addHours(2));

            return response()->json(['status' => 'success', 'reply' => $reply]);

        } catch (\Exception $e) {
            Log::error("ChatController Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'reply'  => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── Clear session history (called when user clears chat) ──────────────────
    public function clearHistory(Request $request)
    {
        $user = auth()->user();
        cache()->forget('chat_history_' . $user->id);
        return response()->json(['status' => 'success']);
    }

    // ── Casual message detector ───────────────────────────────────────────────
    private function isCasualMessage(string $msg): bool
    {
        // Very short messages are likely casual
        if (strlen($msg) < 15) {
            foreach ($this->casualPatterns as $pattern) {
                if (str_contains($msg, $pattern)) return true;
            }
        }
        // Exact casual matches regardless of length
        $exactCasual = ['hi', 'hello', 'hey', 'ok', 'okay', 'thanks', 'thank you', 'bye', 'goodbye'];
        return in_array(trim($msg), $exactCasual);
    }

    // ── Smarter intent detection via keyword scoring ──────────────────────────
    private function detectIntent(string $msg): ?string
    {
        $scores = [];

        foreach ($this->intents as $intent => $config) {
            $score = 0;
            foreach ($config['keywords'] as $keyword => $weight) {
                if (str_contains($msg, strtolower($keyword))) {
                    $score += $weight;
                }
            }
            if ($score >= $config['min_score']) {
                $scores[$intent] = $score;
            }
        }

        if (empty($scores)) return null;

        // Return the highest scoring intent
        arsort($scores);
        return array_key_first($scores);
    }

    // ── Fetch data based on detected intent + role ────────────────────────────
    private function fetchDataForIntent(?string $intent, $user, string $role): string
    {
        if (!$intent) return '';

        // Role-based access control per intent
        $adminOnly   = ['students', 'teachers', 'summary', 'sections', 'subjects', 'academic_years'];
        $teacherPlus = ['grades', 'announcements']; // teacher + admin
        $allRoles    = ['profile', 'events', 'policies', 'school_info', 'navigation'];

        if (in_array($intent, $adminOnly) && $role !== 'Admin') {
            return "Note: This information is only available to Admin users.";
        }

        if ($intent === 'grades' && $role === 'Student') {
            return $this->getStudentGrades($user);
        }

        if ($intent === 'grades' && $role === 'Teacher') {
            return $this->getTeacherGrades($user);
        }

        return match($intent) {
            'profile'        => $this->getUserCredentials($user, $role),
            'students'       => $this->getStudentList(),
            'teachers'       => $this->getTeacherList(),
            'announcements'  => $this->getAnnouncementsByRole($user, $role),
            'events'         => $this->getUpcomingEvents(),
            'policies'       => $this->getPolicies(),
            'sections'       => $this->getSections(),
            'subjects'       => $this->getSubjects(),
            'grades'         => $role === 'Admin' ? $this->getAllGrades() : 
                               ($role === 'Teacher' ? $this->getTeacherGrades($user) : $this->getStudentGrades($user)),
            'school_info'    => $this->getSchoolInfo(),
            'summary'        => $this->getAdminSummary(),
            'academic_years' => $this->getAcademicYears(),
            'navigation'     => '', // handled via $navigationLink separately
            default          => '',
        };
    }

    // ── Role instructions ─────────────────────────────────────────────────────
    private function getRoleInstructions(string $role): string
    {
        return match($role) {
            'Admin' => "
                You are assisting an ADMIN user who has FULL and UNRESTRICTED access to ALL school data.
                ADMIN ACCESS RULES:
                - NEVER mask, hide, or anonymize any data for admin users.
                - NEVER say 'for privacy reasons' or replace real names with 'Student 1', etc.
                - Provide complete, unfiltered data exactly as retrieved.
                - When providing page links, use ONLY relative paths like /dashboard.
                - Format links as markdown: [Label](/path)
                - NEVER list available pages unless the user explicitly asks for navigation help.
                - NEVER repeat the same data twice in one response.
                - NEVER add a navigation section at the end of a data response.
                - Only provide navigation links when explicitly asked.
            ",
            'Teacher' => "
                You are assisting a TEACHER user.
                ACCESS RULES FOR TEACHERS:
                - Can view: their own profile, their own announcements, upcoming school events,
                  active school policies, section information, their class list, their grades,
                  school mission/vision/values, and their teaching schedule.
                - Can view student names in their classes, but NOT other students' personal records.
                - Can view their own grades (for subjects they teach).
                - Do NOT share other teachers' personal details or admin-level data.
                - When providing page links, use ONLY relative paths like /teacher/schedule.
                - Format links as markdown: [Label](/path)
            ",
            default => "
                You are assisting a STUDENT user.
                ACCESS RULES FOR STUDENTS:
                - Can view: their own profile, their section, upcoming school events,
                  active school policies, school mission/vision/values, announcements for their section,
                  their own grades, and their class schedule.
                - Do NOT share other students' records, teacher details, or admin-level data.
                - When providing page links, use ONLY relative paths like /student/schedule.
                - Format links as markdown: [Label](/path)
            ",
        };
    }

    // ── Clean leaked thinking text ────────────────────────────────────────────
    private function cleanLeaks(?string $reply): string
    {
        if (!$reply) return '';

        $leakPatterns = [
            'thinking process', 'thinking:', 'my thinking',
            'step 1', 'step 2', 'step 3', 'step 4', 'step 5',
            'draft:', 'final draft', 'final output', 'final answer:',
            'final check', 'final response', 'constraint check',
            'refinement:', 'refine:', 'formulate', 'formulating',
            'analyze the request', 'analyzing', 'scan the data',
            'note to self', 'internal note', 'i will now', 'i am going to',
            'here is my response:', 'here is the response:',
            'my response:', 'the response is:',
        ];

        $lines   = explode("\n", $reply);
        $cleaned = array_filter($lines, function ($line) use ($leakPatterns) {
            $lower = strtolower(trim($line));
            if (empty($lower)) return true;
            foreach ($leakPatterns as $pattern) {
                if (str_contains($lower, $pattern)) return false;
            }
            return true;
        });

        $reply = trim(implode("\n", $cleaned));
        $reply = preg_replace('/\*\s*Constraint.*?\*/si', '', $reply);
        $reply = preg_replace('/^\s*\d+\.\s+\*\*(Analyze|Scan|Interpret|Formulate|Final|Draft|Refine|Check|Output).*$/mi', '', $reply);

        return trim($reply);
    }

    // ── Convert markdown links to clickable HTML ──────────────────────────────
    private function renderLinks(string $reply): string
    {
        // Markdown links: [Label](/path) or [Label](https://...)
        $reply = preg_replace(
            '/\[([^\]]+)\]\((https?:\/\/[^\)]+|\/[^\)]+)\)/',
            '<a href="$2" class="chat-link" onclick="window.location.href=\'$2\'; return false;">$1 →</a>',
            $reply
        );

        // Plain URLs not already wrapped
        $reply = preg_replace(
            '/(?<!\()(?<!")(https?:\/\/[^\s<]+(?:\/[^\s<]*)?)(?!\))/',
            '<a href="$1" class="chat-link" onclick="window.location.href=\'$1\'; return false;">$1 →</a>',
            $reply
        );

        return $reply;
    }

    // ── Dynamic Query Methods ─────────────────────────────────────────────────
    private function getUserCredentials($user, string $role): string
    {
        $out  = "**Your Profile Information**\n\n";
        $out .= "• **Name:** {$user->name}\n";
        $out .= "• **Email:** {$user->email}\n";
        $out .= "• **Role:** {$role}\n";
        $out .= "• **Status:** {$user->status}\n";
        $out .= "• **Account Created:** " . date('F d, Y', strtotime($user->created_at)) . "\n\n";

        if ($role === 'Teacher') {
            $d = DB::table('teacher_details')->where('id', $user->details_id)->first();
            if ($d) {
                $out .= "**Teacher Details:**\n";
                $out .= "• **Employee ID:** {$d->employee_id}\n";
                $out .= "• **Department:** {$d->department}\n";
                $out .= "• **Position:** {$d->position}\n";
                $out .= "• **Specialization:** {$d->specialization}\n";
                $out .= "• **Employment Status:** {$d->employment_status}\n";
                $out .= "• **Date Hired:** " . date('F d, Y', strtotime($d->date_hired)) . "\n";
                $out .= "• **Contact:** {$d->contact_no}\n";
            }
        }

        if ($role === 'Student') {
            $d = DB::table('user_details')->where('id', $user->details_id)->first();
            if ($d) {
                $gl  = DB::table('grade_level')->where('id', $d->grade_level_id)->value('grade_level_name');
                $sc  = DB::table('section')->where('id', $d->section_id)->value('section_name');
                $out .= "**Student Details:**\n";
                $out .= "• **Student No/LRN:** {$d->student_no}\n";
                $out .= "• **Grade Level:** {$gl}\n";
                $out .= "• **Section:** {$sc}\n";
                $out .= "• **Contact:** {$d->contact_no}\n";
                $out .= "• **Address:** {$d->address}\n";
                $out .= "• **Birthdate:** " . ($d->birthdate ? date('F d, Y', strtotime($d->birthdate)) : 'Not set') . "\n";
                $out .= "• **Gender:** {$d->sex}\n";
            }
        }

        return $out;
    }

    private function getAnnouncementsByRole($user, string $role): string
    {
        if ($role === 'Admin') {
            $rows = DB::table('announcements as a')
                        ->join('subject as s', 'a.subject_id', '=', 's.id')
                        ->join('users as u', 'a.user_id', '=', 'u.id')
                        ->orderBy('a.date_posted', 'desc')
                        ->limit(10)
                        ->get(['a.title', 'a.date_posted', 's.subject_name', 'u.name as posted_by']);

            if ($rows->isEmpty()) return "No announcements found.";

            $out = "**Recent Announcements (Admin View):**\n\n";
            foreach ($rows as $r) {
                $out .= "• **[{$r->date_posted}] {$r->title}**\n";
                $out .= "  Subject: {$r->subject_name} | Posted by: {$r->posted_by}\n\n";
            }
            return $out;
        }

        if ($role === 'Teacher') {
            $rows = DB::table('announcements as a')
                        ->join('subject as s', 'a.subject_id', '=', 's.id')
                        ->where('a.user_id', $user->id)
                        ->orderBy('a.date_posted', 'desc')
                        ->limit(10)
                        ->get(['a.title', 'a.date_posted', 's.subject_name']);

            if ($rows->isEmpty()) return "You haven't posted any announcements yet. You can create one at [Announcements](/announcements).";

            $out = "**Your Announcements:**\n\n";
            foreach ($rows as $r) {
                $out .= "• **[{$r->date_posted}] {$r->title}** (Subject: {$r->subject_name})\n";
            }
            return $out;
        }

        // Student
        $details = DB::table('user_details')->where('id', $user->details_id)->first();
        if (!$details || !$details->section_id) return "No announcements found for your section.";

        $rows = DB::table('announcements as a')
                    ->join('announcement_sections as ans', 'a.id', '=', 'ans.announcement_id')
                    ->join('subject as s', 'a.subject_id', '=', 's.id')
                    ->join('users as u', 'a.user_id', '=', 'u.id')
                    ->leftJoin('teacher_details as td', 'u.details_id', '=', 'td.id')
                    ->where('ans.section_id', $details->section_id)
                    ->orderBy('a.date_posted', 'desc')
                    ->limit(10)
                    ->select('a.title', 'a.date_posted', 's.subject_name', 'u.name as teacher_name')
                    ->get();

        if ($rows->isEmpty()) return "No announcements found for your section.";

        $out = "**Announcements for Your Section:**\n\n";
        foreach ($rows as $r) {
            $out .= "• **[{$r->date_posted}] {$r->title}**\n";
            $out .= "  Subject: {$r->subject_name} | Posted by: {$r->teacher_name}\n\n";
        }
        return $out;
    }

    private function getStudentList(): string
    {
        $total = DB::table('users')->where('role_id', 2)->where('status', 'Active')->count();
        
        $rows = DB::table('users as u')
                    ->join('user_details as d', 'u.details_id', '=', 'd.id')
                    ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                    ->join('section as sc', 'd.section_id', '=', 'sc.id')
                    ->where('u.role_id', 2)
                    ->where('u.status', 'Active')
                    ->orderBy('gl.id')
                    ->orderBy('sc.section_name')
                    ->orderBy('d.lname')
                    ->limit(5)
                    ->get(['u.name', 'd.student_no', 'gl.grade_level_name', 'sc.section_name', 'd.lname', 'd.fname']);

        if ($rows->isEmpty()) return "No students found.";

        $out = "**Active Students (Total: {$total})**\n\n";
        $out .= "Showing 5 of {$total} students. View the complete list at [User Management](/students):\n\n";
        
        foreach ($rows as $r) {
            $out .= "• **{$r->name}** (LRN: {$r->student_no})\n";
            $out .= "  {$r->grade_level_name} - {$r->section_name}\n\n";
        }
        
        return $out;
    }

    private function getTeacherList(): string
    {
        $total = DB::table('users')->where('role_id', 1)->where('status', 'Active')->count();
        
        $rows = DB::table('users as u')
                    ->join('teacher_details as d', 'u.details_id', '=', 'd.id')
                    ->where('u.role_id', 1)
                    ->where('u.status', 'Active')
                    ->orderBy('d.lname')
                    ->limit(5)
                    ->get(['u.name', 'd.employee_id', 'd.department', 'd.position']);

        if ($rows->isEmpty()) return "No teachers found.";

        $out = "**Active Teachers (Total: {$total})**\n\n";
        $out .= "Showing 5 of {$total} teachers. View the complete list at [User Management](/students):\n\n";
        
        foreach ($rows as $r) {
            $out .= "• **{$r->name}** (ID: {$r->employee_id})\n";
            $out .= "  {$r->department} - {$r->position}\n\n";
        }
        
        return $out;
    }

    private function getUpcomingEvents(): string
{
    $rows = DB::table('events')
                ->where('event_date', '>=', now()->toDateString())
                ->orderBy('event_date')
                ->limit(10)
                ->get(['title', 'event_date', 'event_type', 'description']);

    if ($rows->isEmpty()) return "No upcoming events.";

    $out = "**Upcoming Events:**\n\n";
    foreach ($rows as $r) {
        $date = date('M d, Y', strtotime($r->event_date));
        $type = ucfirst($r->event_type);
        $out .= "• **{$date} - {$r->title}** ({$type})\n";
        if ($r->description) {
            $out .= "  {$r->description}\n";
        }
        $out .= "\n";
    }
    
    $role = auth()->user()->role->name;
    $calendarRoute = $role === 'Admin' ? '/admin/calendar' : ($role === 'Teacher' ? '/teacher/calendar' : '/student/calendar');
    $out .= "View full calendar: [Calendar]({$calendarRoute})";
    
    return $out;
}

  private function getPolicies(): string
{
    $rows = DB::table('policies')
                ->where('status', 'Active')
                ->orderBy('effective_date', 'desc')
                ->limit(10)
                ->get(['title', 'category', 'effective_date', 'description']);

    if ($rows->isEmpty()) return "No active policies found.";

    $out = "**Active School Policies:**\n\n";
    foreach ($rows as $r) {
        $date = date('M d, Y', strtotime($r->effective_date));
        $out .= "• **{$r->title}** [{$r->category}]\n";
        $out .= "  Effective: {$date}\n";
        if ($r->description) {
            $out .= "  {$r->description}\n";
        }
        $out .= "\n";
    }
    
    $role = auth()->user()->role->name;
    $policiesRoute = $role === 'Admin' ? '/admin/policies' : ($role === 'Teacher' ? '/teacher/policies' : '/student/policies');
    $out .= "View all policies: [Policies]({$policiesRoute})";
    
    return $out;
}
    private function getSections(): string
    {
        $rows = DB::table('section as s')
                    ->join('grade_level as gl', 's.grade_level_id', '=', 'gl.id')
                    ->orderBy('gl.id')
                    ->orderBy('s.section_name')
                    ->get(['s.section_name', 'gl.grade_level_name', 's.student_enrolled']);

        if ($rows->isEmpty()) return "No sections found.";

        $out = "**All Sections:**\n\n";
        foreach ($rows as $r) {
            $out .= "• **{$r->grade_level_name} - {$r->section_name}**\n";
            $out .= "  Students enrolled: {$r->student_enrolled}\n\n";
        }
        
        if (auth()->user()->role->name === 'Admin') {
            $out .= "Manage sections: [Sections](/admin/sections)";
        }
        
        return $out;
    }

    private function getSubjects(): string
    {
        $rows = DB::table('subject')
                    ->orderBy('subject_name')
                    ->get();

        if ($rows->isEmpty()) return "No subjects found.";

        $out = "**All Subjects:**\n\n";
        foreach ($rows as $r) {
            $out .= "• {$r->subject_name}\n";
        }
        
        if (auth()->user()->role->name === 'Admin') {
            $out .= "\nManage subjects: [Subjects](/admin/subjects)";
        }
        
        return $out;
    }

    private function getStudentGrades($user): string
    {
        $grades = DB::table('grades as g')
                    ->join('subject as s', 'g.subject_id', '=', 's.id')
                    ->join('section as sec', 'g.section_id', '=', 'sec.id')
                    ->join('grade_level as gl', 'g.grade_level_id', '=', 'gl.id')
                    ->where('g.student_id', $user->id)
                    ->orderBy('g.quarter')
                    ->orderBy('s.subject_name')
                    ->get(['s.subject_name', 'g.quarter', 'g.grade', 'g.remarks', 'sec.section_name', 'gl.grade_level_name']);

        if ($grades->isEmpty()) return "No grades found for your account.";

        $out = "**Your Grades:**\n\n";
        $out .= "Section: {$grades[0]->grade_level_name} - {$grades[0]->section_name}\n\n";
        
        $currentQuarter = null;
        foreach ($grades as $g) {
            if ($currentQuarter !== $g->quarter) {
                $currentQuarter = $g->quarter;
                $out .= "\n**Quarter {$g->quarter}**\n";
            }
            $out .= "• {$g->subject_name}: **{$g->grade}**";
            if ($g->remarks) {
                $out .= " ({$g->remarks})";
            }
            $out .= "\n";
        }
        
        $out .= "\nView detailed grades: [My Grades](/grades)";
        
        return $out;
    }

    private function getTeacherGrades($user): string
    {
        // Get sections the teacher handles
        $sections = DB::table('schedule')
                    ->where('user_id', $user->id)
                    ->distinct()
                    ->pluck('section_id');

        if ($sections->isEmpty()) return "You don't have any sections assigned yet.";

        $grades = DB::table('grades as g')
                    ->join('users as u', 'g.student_id', '=', 'u.id')
                    ->join('user_details as ud', 'u.details_id', '=', 'ud.id')
                    ->join('subject as s', 'g.subject_id', '=', 's.id')
                    ->join('section as sec', 'g.section_id', '=', 'sec.id')
                    ->join('grade_level as gl', 'g.grade_level_id', '=', 'gl.id')
                    ->whereIn('g.section_id', $sections)
                    ->orderBy('gl.id')
                    ->orderBy('sec.section_name')
                    ->orderBy('ud.lname')
                    ->orderBy('g.quarter')
                    ->limit(20)
                    ->get(['u.name', 's.subject_name', 'g.quarter', 'g.grade', 'g.remarks', 'sec.section_name', 'gl.grade_level_name']);

        if ($grades->isEmpty()) return "No grades recorded for your sections yet.";

        $out = "**Recent Grades from Your Sections (showing 20 of many):**\n\n";
        
        foreach ($grades as $g) {
            $out .= "• **{$g->name}** - {$g->grade_level_name} {$g->section_name}\n";
            $out .= "  {$g->subject_name} (Q{$g->quarter}): {$g->grade}";
            if ($g->remarks) {
                $out .= " - {$g->remarks}";
            }
            $out .= "\n\n";
        }
        
        $out .= "Manage all grades: [Grades](/grades)";
        
        return $out;
    }

    private function getAllGrades(): string
    {
        $total = DB::table('grades')->count();
        
        $grades = DB::table('grades as g')
                    ->join('users as u', 'g.student_id', '=', 'u.id')
                    ->join('user_details as ud', 'u.details_id', '=', 'ud.id')
                    ->join('subject as s', 'g.subject_id', '=', 's.id')
                    ->join('section as sec', 'g.section_id', '=', 'sec.id')
                    ->join('grade_level as gl', 'g.grade_level_id', '=', 'gl.id')
                    ->orderBy('gl.id')
                    ->orderBy('sec.section_name')
                    ->orderBy('ud.lname')
                    ->orderBy('g.quarter')
                    ->limit(15)
                    ->get(['u.name', 's.subject_name', 'g.quarter', 'g.grade', 'sec.section_name', 'gl.grade_level_name']);

        if ($grades->isEmpty()) return "No grades found.";

        $out = "**Grade Records (Total: {$total})**\n\n";
        $out .= "Showing 15 of {$total} records:\n\n";
        
        foreach ($grades as $g) {
            $out .= "• **{$g->name}** - {$g->grade_level_name} {$g->section_name}\n";
            $out .= "  {$g->subject_name} (Q{$g->quarter}): {$g->grade}\n\n";
        }
        
        $out .= "Manage all grades: [Grades](/admin/grades)";
        
        return $out;
    }

private function getSchoolInfo(): string
{
    $info = DB::table('school_info')->first();
    if (!$info) return "School information has not been set up yet.";

    $values = array();
    if (!empty($info->core_values)) {
        try { 
            $decoded = json_decode($info->core_values, true);
            if (is_array($decoded)) {
                $values = $decoded;
            }
        } catch (\Exception $e) {
            // If json_decode fails, try to handle as comma-separated string
            $values = explode(',', $info->core_values);
        }
    }

    $mission = isset($info->mission) ? $info->mission : 'Not set';
    $vision = isset($info->vision) ? $info->vision : 'Not set';

    $out  = "**🏫 School Information**\n\n";
    $out .= "**Mission:**\n{$mission}\n\n";
    $out .= "**Vision:**\n{$vision}\n\n";
    $out .= "**Core Values:**\n";
    
    if (count($values) > 0) {
        foreach ($values as $val) {
            $val = trim($val);
            if (!empty($val)) {
                $out .= "• {$val}\n";
            }
        }
    } else {
        $out .= "Not set\n";
    }
    
    $role = auth()->user()->role->name;
    $policiesRoute = $role === 'Admin' ? '/admin/policies' : ($role === 'Teacher' ? '/teacher/policies' : '/student/policies');
    $out .= "\nView/Edit: [Policies & School Info]({$policiesRoute})";
    
    return $out;
}

    private function getAdminSummary(): string
    {
        $totalStudents  = DB::table('users')->where('role_id', 2)->where('status', 'Active')->count();
        $totalTeachers  = DB::table('users')->where('role_id', 1)->where('status', 'Active')->count();
        $totalInactive  = DB::table('users')->where('status', 'Inactive')->count();
        $newThisMonth   = DB::table('users')->where('role_id', 2)->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count();
        $announcements  = DB::table('announcements')->count();
        $events         = DB::table('events')->count();
        $activePolicies = DB::table('policies')->where('status', 'Active')->count();
        $sections       = DB::table('section')->count();
        $subjects       = DB::table('subject')->count();
        $gradeLevels    = DB::table('grade_level')->count();

        return "**📊 School System Summary**\n\n"
            . "**Users:**\n"
            . "• Active Students: {$totalStudents}\n"
            . "• Active Teachers: {$totalTeachers}\n"
            . "• Inactive Accounts: {$totalInactive}\n"
            . "• New Students This Month: {$newThisMonth}\n\n"
            . "**Academic:**\n"
            . "• Grade Levels: {$gradeLevels}\n"
            . "• Sections: {$sections}\n"
            . "• Subjects: {$subjects}\n\n"
            . "**Content:**\n"
            . "• Announcements: {$announcements}\n"
            . "• Events: {$events}\n"
            . "• Active Policies: {$activePolicies}\n\n"
            . "View dashboard: [Dashboard](/dashboard)";
    }

    private function getAcademicYears(): string
    {
        $years = DB::table('academic_year')
                    ->orderBy('year_start', 'desc')
                    ->get();

        if ($years->isEmpty()) return "No academic years found.";

        $current = DB::table('academic_year')->where('is_active', 1)->first();
        
        $out = "**📅 Academic Years**\n\n";
        
        if ($current) {
            $out .= "**Current Active Year:** {$current->year_start}-{$current->year_end}\n\n";
        }
        
        $out .= "**All Academic Years:**\n";
        foreach ($years as $y) {
            $active = $y->is_active ? " (Active)" : "";
            $out .= "• {$y->year_start}-{$y->year_end}{$active}\n";
        }
        
        if (auth()->user()->role->name === 'Admin') {
            $out .= "\nManage academic years: [Academic Years](/academic-years)";
        }
        
        return $out;
    }
}