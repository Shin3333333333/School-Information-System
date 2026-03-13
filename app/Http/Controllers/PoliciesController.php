<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PoliciesController extends Controller
{
    public function index()
    {
        return view('admin.policies');
    }

    public function list()
    {
        try {
            $data = DB::select("CALL usp_get_data(?, ?)", [7, 0]); // MODE 7: all policies
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Policies Fetch Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string',
            'effective_date' => 'required|date',
            'status'         => 'required|string',
        ]);

        try {
            $data = [
                'title'          => $request->title,
                'description'    => $request->description,
                'category'       => $request->category,
                'effective_date' => $request->effective_date,
                'status'         => $request->status,
                'created_by'     => auth()->id(),
            ];

            DB::select("CALL usp_sql_actions(?, ?)", [7, json_encode($data)]); // MODE 7: insert policy

            return response()->json(['status' => 'success', 'message' => 'Policy added successfully.']);
        } catch (\Exception $e) {
            Log::error("Policies Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer',
            'title'          => 'required|string|max:255',
            'category'       => 'required|string',
            'effective_date' => 'required|date',
            'status'         => 'required|string',
        ]);

        try {
            $data = [
                'id'             => (int) $request->id,
                'title'          => $request->title,
                'description'    => $request->description,
                'category'       => $request->category,
                'effective_date' => $request->effective_date,
                'status'         => $request->status,
            ];

            DB::select("CALL usp_sql_actions(?, ?)", [8, json_encode($data)]); // MODE 8: update policy

            return response()->json(['status' => 'success', 'message' => 'Policy updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Policies Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            DB::table('policies')->where('id', $request->id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Policy deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Policies Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getInfo()
    {
        try {
            $info = DB::table('school_info')->first();
            return response()->json(['status' => 'success', 'data' => $info]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'field' => 'required|in:mission,vision,values',
            'value' => 'required|string',
        ]);

        try {
            // Map 'values' → actual DB column 'core_values'
            $column = $request->field === 'values' ? 'core_values' : $request->field;

            DB::table('school_info')->where('id', 1)->update([
                $column      => $request->value,
                'updated_at' => now(),
            ]);

            $label = match($request->field) {
                'mission' => 'Mission',
                'vision'  => 'Vision',
                'values'  => 'Core Values',
            };

            return response()->json(['status' => 'success', 'message' => $label . ' updated successfully.']);
        } catch (\Exception $e) {
            Log::error("School Info Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}