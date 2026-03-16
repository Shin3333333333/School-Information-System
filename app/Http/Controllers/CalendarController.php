<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    // ── Display calendar page (all roles) ─────────────────────────────────────
    // Admin  → full CRUD for standalone events + sees everything
    // Teacher/Student → read-only, sees events relevant to them
    public function index()
    {
        $role = auth()->user()->role_id ?? null;
        return view('calendar', compact('role'));
    }

    // ── Role-aware calendar feed — usp_get_data MODE 16 ──────────────────────
    // The SP returns TWO result sets (standalone events + announcement events).
    // PDO only returns the first result set via DB::select(), so we use
    // DB::statement + cursor, or simply run two separate SP calls.
    // Simplest approach: merge in PHP using a raw PDO multi-query.
    public function list()
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare("CALL usp_get_calendar(?)");
            $stmt->execute([(int) auth()->id()]);

            // First result set: standalone calendar events
            $events = $stmt->fetchAll(\PDO::FETCH_OBJ);

            // Second result set: announcement-linked calendar events
            $announcements = [];
            if ($stmt->nextRowset()) {
                $announcements = $stmt->fetchAll(\PDO::FETCH_OBJ);
            }

            $all = array_merge($events, $announcements);

            // Sort combined results by event_date ascending
            usort($all, fn($a, $b) => strcmp($a->event_date ?? '', $b->event_date ?? ''));

            return response()->json(['status' => 'success', 'data' => $all]);
        } catch (\Exception $e) {
            Log::error("Calendar List Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Admin: store standalone event — usp_sql_actions MODE 21 ──────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'description'=> 'nullable|string',
            'event_date' => 'required|date',
            'event_type' => 'required|in:academic,admin,holiday,activity',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [21, json_encode([
                'title'       => $request->title,
                'description' => $request->description ?? null,
                'event_date'  => $request->event_date,
                'event_type'  => $request->event_type,
                'created_by'  => (int) auth()->id(),
            ])]);
            return response()->json(['status' => 'success', 'message' => 'Event added to calendar.']);
        } catch (\Exception $e) {
            Log::error("Calendar Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Admin: update standalone event — usp_sql_actions MODE 22 ─────────────
    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:events,id',
            'title'      => 'required|string|max:200',
            'description'=> 'nullable|string',
            'event_date' => 'required|date',
            'event_type' => 'required|in:academic,admin,holiday,activity',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [22, json_encode([
                'id'          => (int) $request->id,
                'title'       => $request->title,
                'description' => $request->description ?? null,
                'event_date'  => $request->event_date,
                'event_type'  => $request->event_type,
            ])]);
            return response()->json(['status' => 'success', 'message' => 'Event updated.']);
        } catch (\Exception $e) {
            Log::error("Calendar Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Admin: delete standalone event — usp_sql_actions MODE 23 ─────────────
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:events,id']);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [23, json_encode([
                'id' => (int) $request->id,
            ])]);
            return response()->json(['status' => 'success', 'message' => 'Event deleted.']);
        } catch (\Exception $e) {
            Log::error("Calendar Destroy Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}