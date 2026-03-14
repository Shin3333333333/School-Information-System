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

    // ── Route map for admin navigation links ─────────────────────────────────
    private array $adminRoutes = [
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
    ];

    // ── Intent definitions: each intent has weighted keyword groups ───────────
    // Score = sum of matched keyword weights. Highest score wins.
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
        'school_info' => [
            'keywords' => [
                'school info' => 3, 'school information' => 3, 'about the school' => 3,
                'mission vision' => 3, 'core values' => 3,
                'mission' => 2, 'vision' => 2, 'values' => 2,
                'school motto' => 2, 'school background' => 2,
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

                Log::info("Chat Debug — message: '{$msgLower}' | intent: '{$detectedIntent}'");

                // If navigation intent, also figure out which page ────────────
                if ($detectedIntent === 'navigation' && $userRole === 'Admin') {
                    foreach ($this->adminRoutes as $keyword => $route) {
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
                ? "\nNAVIGATION: The user is asking about '{$navigationLink['label']}'. "
                  . "Provide this EXACT link: [{$navigationLink['label']}]({$navigationLink['url']}). "
                  . "Use ONLY the path — do NOT use full URLs.\n"
                : '';

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
                - When providing navigation links, format them as markdown: [Label](/path)
                - Remember context from earlier in this conversation and refer back when relevant.

                STRICT OUTPUT RULES — NEVER BREAK THESE:
                - Output ONLY the final response. Nothing else.
                - NEVER include thinking steps, reasoning, drafts, or self-checks.
                - NEVER start with 'Thinking:', 'Draft:', 'Final Output:', 'Step:', or any internal commentary.
                - NEVER explain what you are about to do. Just do it.
                - Keep answers under 150 words unless listing data that requires more.

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
                'HTTP-Referer'  => 'http://localhost',
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
            $message_obj = $data['choices'][0]['message'] ?? null;

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
        $adminOnly   = ['students', 'teachers', 'summary'];
        $teacherPlus = ['sections', 'announcements']; // teacher + admin
        $allRoles    = ['profile', 'events', 'policies', 'school_info', 'navigation'];

        if (in_array($intent, $adminOnly) && $role !== 'Admin') {
            return "Note: This data is restricted to Admin users only.";
        }

        return match($intent) {
            'profile'      => $this->getUserCredentials($user, $role),
            'students'     => $this->getStudentList(),
            'teachers'     => $this->getTeacherList(),
            'announcements'=> $role === 'Admin'
                                ? $this->getAllAnnouncements()
                                : ($role === 'Teacher'
                                    ? $this->getUserAnnouncements($user->id)
                                    : $this->getStudentAnnouncements($user)),
            'events'       => $this->getUpcomingEvents(),
            'policies'     => $this->getPolicies(),
            'sections'     => $this->getSections(),
            'school_info'  => $this->getSchoolInfo(),
            'summary'      => $this->getAdminSummary(),
            'navigation'   => '', // handled via $navigationLink separately
            default        => '',
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
                - Only provide navigation links when explicitly asked. Example pages:
                  [Dashboard](/dashboard), [User Management](/students), [Policies](/admin/policies)
            ",
            'Teacher' => "
                You are assisting a TEACHER user.
                You can only share: their own profile, their own announcements, upcoming school events,
                active school policies, section information, and school mission/vision/values.
                Do NOT share other teachers' personal details, student personal records, or admin-level data.
            ",
            default => "
                You are assisting a STUDENT user.
                You can only share: their own profile and section, upcoming school events,
                active school policies, school mission/vision/values, and announcements for their section.
                Do NOT share other students' records, teacher details, or admin-level data.
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
        $out  = "User credentials for {$user->name}:\n";
        $out .= "- Email: {$user->email}\n";
        $out .= "- Role: {$role}\n";
        $out .= "- Status: {$user->status}\n";
        $out .= "- Account created: {$user->created_at}\n";

        if ($role === 'Teacher') {
            $d = DB::table('teacher_details')->where('id', $user->details_id)->first();
            if ($d) {
                $out .= "- Employee ID: {$d->employee_id}\n";
                $out .= "- Department: {$d->department}\n";
                $out .= "- Position: {$d->position}\n";
                $out .= "- Specialization: {$d->specialization}\n";
                $out .= "- Employment Status: {$d->employment_status}\n";
                $out .= "- Date Hired: {$d->date_hired}\n";
                $out .= "- Contact: {$d->contact_no}\n";
            }
        }

        if ($role === 'Student') {
            $d = DB::table('user_details')->where('id', $user->details_id)->first();
            if ($d) {
                $gl  = DB::table('grade_level')->where('id', $d->grade_level_id)->value('grade_level_name');
                $sc  = DB::table('section')->where('id', $d->section_id)->value('section_name');
                $out .= "- Student No: {$d->student_no}\n";
                $out .= "- Grade Level: {$gl}\n";
                $out .= "- Section: {$sc}\n";
                $out .= "- Contact: {$d->contact_no}\n";
                $out .= "- Address: {$d->address}\n";
            }
        }

        return $out;
    }

    private function getUserAnnouncements(int $userId): string
    {
        $rows = DB::table('announcements as a')
                    ->join('subject as s', 'a.subject_id', '=', 's.id')
                    ->where('a.user_id', $userId)
                    ->orderBy('a.date_posted', 'desc')
                    ->limit(10)
                    ->get(['a.title', 'a.date_posted', 's.subject_name']);

        if ($rows->isEmpty()) return "No announcements found for this user.";

        return "Your announcements:\n" . $rows->map(fn($r) =>
            "- [{$r->date_posted}] {$r->title} (Subject: {$r->subject_name})"
        )->implode("\n");
    }

    private function getStudentAnnouncements($user): string
    {
        $details = DB::table('user_details')->where('id', $user->details_id)->first();
        if (!$details) return "No announcements found.";

        $rows = DB::table('announcements as a')
                    ->join('announcement_sections as ans', 'a.id', '=', 'ans.announcement_id')
                    ->join('subject as s', 'a.subject_id', '=', 's.id')
                    ->where('ans.section_id', $details->section_id)
                    ->orderBy('a.date_posted', 'desc')
                    ->limit(10)
                    ->get(['a.title', 'a.date_posted', 's.subject_name']);

        if ($rows->isEmpty()) return "No announcements found for your section.";

        return "Announcements for your section:\n" . $rows->map(fn($r) =>
            "- [{$r->date_posted}] {$r->title} (Subject: {$r->subject_name})"
        )->implode("\n");
    }

    private function getAllAnnouncements(): string
    {
        $rows = DB::table('announcements as a')
                    ->join('subject as s', 'a.subject_id', '=', 's.id')
                    ->join('users as u', 'a.user_id', '=', 'u.id')
                    ->orderBy('a.date_posted', 'desc')
                    ->limit(10)
                    ->get(['a.title', 'a.date_posted', 's.subject_name', 'u.name as posted_by']);

        if ($rows->isEmpty()) return "No announcements found.";

        return "Recent announcements:\n" . $rows->map(fn($r) =>
            "- [{$r->date_posted}] {$r->title} by {$r->posted_by} (Subject: {$r->subject_name})"
        )->implode("\n");
    }

    private function getStudentList(): string
    {
        $rows = DB::table('users as u')
                    ->join('user_details as d', 'u.details_id', '=', 'd.id')
                    ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                    ->join('section as sc', 'd.section_id', '=', 'sc.id')
                    ->where('u.role_id', 2)
                    ->where('u.status', 'Active')
                    ->limit(3)
                    ->get(['u.name', 'd.student_no', 'gl.grade_level_name', 'sc.section_name']);

        if ($rows->isEmpty()) return "No students found.";

        return "Active students (showing 3 of many — see full list at [User Management](/students)):\n" . $rows->map(fn($r) =>
            "- {$r->name} | Student No: {$r->student_no} | {$r->grade_level_name} - {$r->section_name}"
        )->implode("\n");
    }

    private function getTeacherList(): string
    {
        $rows = DB::table('users as u')
                    ->join('teacher_details as d', 'u.details_id', '=', 'd.id')
                    ->where('u.role_id', 1)
                    ->where('u.status', 'Active')
                    ->limit(3)
                    ->get(['u.name', 'd.employee_id', 'd.department', 'd.position']);

        if ($rows->isEmpty()) return "No teachers found.";

        return "Active teachers (showing 3 of many — see full list at [User Management](/students)):\n" . $rows->map(fn($r) =>
            "- {$r->name} | ID: {$r->employee_id} | {$r->department} - {$r->position}"
        )->implode("\n");
    }

    private function getUpcomingEvents(): string
    {
        $rows = DB::table('events')
                    ->where('event_date', '>=', now()->toDateString())
                    ->orderBy('event_date')
                    ->limit(10)
                    ->get(['title', 'event_date', 'event_type', 'description']);

        if ($rows->isEmpty()) return "No upcoming events.";

        return "Upcoming events:\n" . $rows->map(fn($r) =>
            "- [{$r->event_date}] {$r->title} ({$r->event_type})" . ($r->description ? ": {$r->description}" : '')
        )->implode("\n");
    }

    private function getPolicies(): string
    {
        $rows = DB::table('policies')
                    ->where('status', 'Active')
                    ->orderBy('effective_date', 'desc')
                    ->limit(10)
                    ->get(['title', 'category', 'effective_date', 'description']);

        if ($rows->isEmpty()) return "No active policies found.";

        return "Active policies:\n" . $rows->map(fn($r) =>
            "- [{$r->category}] {$r->title} (Effective: {$r->effective_date})" . ($r->description ? ": {$r->description}" : '')
        )->implode("\n");
    }

    private function getSections(): string
    {
        $rows = DB::table('section as s')
                    ->join('grade_level as gl', 's.grade_level_id', '=', 'gl.id')
                    ->orderBy('gl.id')
                    ->get(['s.section_name', 'gl.grade_level_name']);

        if ($rows->isEmpty()) return "No sections found.";

        return "All sections:\n" . $rows->map(fn($r) =>
            "- {$r->grade_level_name}: {$r->section_name}"
        )->implode("\n");
    }

    private function getSchoolInfo(): string
    {
        $info = DB::table('school_info')->first();
        if (!$info) return "School info not set yet.";

        $values = [];
        try { $values = json_decode($info->core_values ?? '[]', true); } catch (\Exception $e) {}

        $out  = "School Information:\n";
        $out .= "- Mission: "     . ($info->mission ?? 'Not set') . "\n";
        $out .= "- Vision: "      . ($info->vision  ?? 'Not set') . "\n";
        $out .= "- Core Values: " . (count($values) > 0 ? implode(', ', $values) : 'Not set') . "\n";
        return $out;
    }

    private function getAdminSummary(): string
    {
        $totalStudents  = DB::table('users')->where('role_id', 2)->where('status', 'Active')->count();
        $totalTeachers  = DB::table('users')->where('role_id', 1)->where('status', 'Active')->count();
        $newThisMonth   = DB::table('users')->where('role_id', 2)->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count();
        $announcements  = DB::table('announcements')->count();
        $events         = DB::table('events')->count();
        $activePolicies = DB::table('policies')->where('status', 'Active')->count();
        $sections       = DB::table('section')->count();

        return "Full school summary:\n"
            . "- Active students: {$totalStudents}\n"
            . "- Active teachers: {$totalTeachers}\n"
            . "- New students this month: {$newThisMonth}\n"
            . "- Total sections: {$sections}\n"
            . "- Total announcements: {$announcements}\n"
            . "- Total events: {$events}\n"
            . "- Active policies: {$activePolicies}\n";
    }
}