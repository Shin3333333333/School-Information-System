<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateFieldsController extends Controller
{
    public function getSubjects()
    {
        try {
            $data = DB::select("CALL usp_populate_fields(?, ?)", [1, null]);
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Populate Subjects Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getSections()
    {
        try {
            $data = DB::select("CALL usp_populate_fields(?, ?)", [2, null]);
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Populate Sections Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getSectionsByGrade($gradeLevel)
    {
        try {
            $data = DB::select("CALL usp_populate_fields(?, ?)", [3, (int) $gradeLevel]);
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Populate Sections By Grade Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getGradeLevels()
    {
        try {
            $data = DB::select("CALL usp_populate_fields(?, ?)", [4, null]);
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Populate Grade Levels Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}