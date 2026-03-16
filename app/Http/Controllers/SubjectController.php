<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    // ── Display subjects page ─────────────────────────────────────────────────
    public function index()
    {
        return view('admin.subjects');
    }

    // ── Fetch all subjects — usp_populate_fields MODE 1 ──────────────────────
    // Reuses the existing populate SP so no new SP mode is needed.
    public function list(Request $request)
    {
        try {
            $subjects = collect(DB::select("CALL usp_populate_fields(?, ?)", [1, null]));

            // Optional keyword search
            if ($request->filled('search')) {
                $term     = strtolower($request->search);
                $subjects = $subjects->filter(fn($s) => str_contains(strtolower($s->subject_name), $term));
            }

            return response()->json(['status' => 'success', 'data' => $subjects->values()]);
        } catch (\Exception $e) {
            Log::error("Subject List Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Insert subject — usp_sql_actions MODE 18 ──────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:120',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [
                18,
                json_encode(['subject_name' => $request->subject_name]),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Subject added successfully.']);
        } catch (\Exception $e) {
            Log::error("Subject Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Update subject — usp_sql_actions MODE 19 ──────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'id'           => 'required|integer',
            'subject_name' => 'required|string|max:120',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [
                19,
                json_encode([
                    'id'           => (int) $request->id,
                    'subject_name' => $request->subject_name,
                ]),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Subject updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Subject Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Delete subject — usp_sql_actions MODE 20 ──────────────────────────────
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [
                20,
                json_encode(['id' => (int) $request->id]),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Subject deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Subject Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}
