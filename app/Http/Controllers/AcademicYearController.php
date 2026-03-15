<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = DB::table('academic_years')->orderBy('id', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $years]);
    }

    public function setActive(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        DB::table('academic_years')->update(['is_active' => 0]);
        DB::table('academic_years')->where('id', $request->id)->update(['is_active' => 1]);
        return response()->json(['status' => 'success', 'message' => 'Academic year updated.']);
    }

    public function store(Request $request)
    {
        $request->validate(['year_label' => 'required|string|max:20']);
        $exists = DB::table('academic_years')->where('year_label', $request->year_label)->exists();
        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Year already exists.'], 422);
        }
        DB::table('academic_years')->insert([
            'year_label' => $request->year_label,
            'is_active'  => 0,
            'created_at' => now(),
        ]);
        return response()->json(['status' => 'success', 'message' => 'Academic year added.']);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $year = DB::table('academic_years')->where('id', $request->id)->first();
        if ($year && $year->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete the active year.'], 422);
        }
        DB::table('academic_years')->where('id', $request->id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Academic year deleted.']);
    }
}