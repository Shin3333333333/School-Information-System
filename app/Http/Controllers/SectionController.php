<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    // ── Display the section management page ───────────────────────────────────
    public function index()
    {
        $gradeLevels = DB::table('grade_level')->orderBy('id')->get();
        return view('admin.sections', compact('gradeLevels'));
    }

    // ── Return paginated + filtered section list (AJAX) ───────────────────────
    public function list(Request $request)
    {
        $search  = $request->input('search', '');
        $gradeId = $request->input('grade_level_id', '');
        $perPage = 10;
        $page    = max(1, (int) $request->input('page', 1));
        $offset  = ($page - 1) * $perPage;

        try {
            $all = collect(DB::select("CALL usp_get_data(?, ?)", [8, 0]));
        } catch (\Exception $e) {
            Log::error("Section Fetch Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        // ── Apply filters in PHP after SP call ────────────────────────────────
        if ($search) {
            $searchLower = strtolower($search);
            $all = $all->filter(fn($r) =>
                str_contains(strtolower($r->section_name), $searchLower) ||
                str_contains(strtolower($r->grade_level_name), $searchLower)
            );
        }

        if ($gradeId) {
            $all = $all->filter(fn($r) => $r->grade_level_id == $gradeId);
        }

        $total = $all->count();
        $rows  = $all->values()->slice($offset, $perPage)->values();

        return response()->json([
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    // ── Create a new section ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'section_name'   => 'required|string|max:50',
            'grade_level_id' => 'required|exists:grade_level,id',
        ]);

        try {
            $data = [
                'section_name'   => $request->section_name,
                'grade_level_id' => (int) $request->grade_level_id,
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [9, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Section created successfully.']);
        } catch (\Exception $e) {
            Log::error("Section Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Update an existing section ────────────────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|exists:section,id',
            'section_name'   => 'required|string|max:50',
            'grade_level_id' => 'required|exists:grade_level,id',
        ]);

        try {
            $data = [
                'id'             => (int) $request->id,
                'section_name'   => $request->section_name,
                'grade_level_id' => (int) $request->grade_level_id,
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [10, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Section updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Section Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Delete a section ──────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:section,id']);

        try {
            $data = ['id' => (int) $request->id];
            DB::select("CALL usp_sql_actions(?, ?)", [11, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Section deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Section Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}