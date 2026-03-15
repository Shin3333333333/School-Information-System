<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function list()
    {
        try {
            $data = DB::select("CALL usp_get_data(?, ?)", [6, 0]); // MODE 6: all events
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Calendar Fetch Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_type' => 'required|string',
        ]);

        try {
            $data = [
                'title'       => $request->title,
                'description' => $request->description,
                'event_date'  => $request->event_date,
                'event_type'  => $request->event_type,
                'created_by'  => auth()->id(),
            ];

            DB::select("CALL usp_sql_actions(?, ?)", [5, json_encode($data)]); // MODE 5: insert event

            return response()->json(['status' => 'success', 'message' => 'Event added successfully.']);
        } catch (\Exception $e) {
            Log::error("Calendar Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|integer',
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_type' => 'required|string',
        ]);

        try {
            $data = [
                'id'          => (int) $request->id,
                'title'       => $request->title,
                'description' => $request->description,
                'event_date'  => $request->event_date,
                'event_type'  => $request->event_type,
            ];

            DB::select("CALL usp_sql_actions(?, ?)", [6, json_encode($data)]); // MODE 6: update event

            return response()->json(['status' => 'success', 'message' => 'Event updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Calendar Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            DB::table('events')->where('id', $request->id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Event deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Calendar Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}