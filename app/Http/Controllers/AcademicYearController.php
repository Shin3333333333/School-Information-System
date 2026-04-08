<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years
     */
    public function index()
    {
        // Fetch all academic years ordered by ID descending (newest first)
        $years = DB::table('academic_years')->orderBy('id', 'desc')->get();
        
        // Get the currently active academic year
        $currentYear = DB::table('academic_years')->where('is_active', 1)->first();
        
        return view('admin.academic-years', compact('years', 'currentYear'));
    }

    /**
     * Store a newly created academic year in database
     * 
     * Validates:
     * - year_label is required, string, max 20 characters
     * - year_label is unique (no duplicates)
     * 
     * Returns JSON response
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'year_label' => 'required|string|max:20',
            ], [
                'year_label.required' => 'Year label is required.',
                'year_label.max' => 'Year label cannot exceed 20 characters.',
            ]);

            // Check if year already exists (prevent duplicates)
            $exists = DB::table('academic_years')
                ->where('year_label', $validated['year_label'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This academic year already exists.'
                ], 422);
            }

            // Insert new academic year (inactive by default)
            DB::table('academic_years')->insert([
                'year_label' => $validated['year_label'],
                'is_active'  => 0,
                'created_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Academic year added successfully.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()['year_label'][0] ?? 'Validation failed.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add academic year. Please try again.'
            ], 500);
        }
    }

    /**
     * Set an academic year as active
     * 
     * Deactivates all other years (ensures only one is active)
     * Uses database transaction for consistency
     * 
     * Returns JSON response
     */
    public function setActive(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'id' => 'required|integer|exists:academic_years,id',
            ], [
                'id.required' => 'Academic year ID is required.',
                'id.exists' => 'Academic year not found.',
            ]);

            // Get the year to be activated
            $year = DB::table('academic_years')->where('id', $validated['id'])->first();

            if (!$year) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Academic year not found.'
                ], 404);
            }

            // Check if already active
            if ($year->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This academic year is already active.'
                ], 422);
            }

            // Use transaction to ensure data consistency
            // If anything fails, all changes rollback
            DB::transaction(function () use ($year) {
                // Deactivate all other years
                DB::table('academic_years')->update(['is_active' => 0]);

                // Activate this year
                DB::table('academic_years')
                    ->where('id', $year->id)
                    ->update([
                        'is_active' => 1,
                    ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => "Academic year '{$year->year_label}' is now active."
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid academic year ID.'
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update academic year: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update academic year.'
            ], 500);
        }
    }

    /**
     * Delete an academic year
     * 
     * Prevents deletion of:
     * - Active years
     * 
     * Returns JSON response
     */
    public function destroy(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'id' => 'required|integer|exists:academic_years,id',
            ], [
                'id.required' => 'Academic year ID is required.',
                'id.exists' => 'Academic year not found.',
            ]);

            // Get the year to delete
            $year = DB::table('academic_years')->where('id', $validated['id'])->first();

            if (!$year) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Academic year not found.'
                ], 404);
            }

            // Prevent deletion of active year
            if ($year->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete the active academic year. Please set another year as active first.'
                ], 422);
            }

            // Store the year label before deletion (for response message)
            $yearLabel = $year->year_label;

            // Delete the year
            DB::table('academic_years')->where('id', $validated['id'])->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Academic year '{$yearLabel}' has been deleted."
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid academic year ID.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete academic year.'
            ], 500);
        }
    }

    /**
     * Get the currently active academic year (API endpoint)
     * 
     * Accessible to all authenticated users
     * Returns JSON response
     */
    public function getCurrentYear()
    {
        try {
            $year = DB::table('academic_years')->where('is_active', 1)->first();

            if (!$year) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active academic year set.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $year
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve current academic year.'
            ], 500);
        }
    }
}