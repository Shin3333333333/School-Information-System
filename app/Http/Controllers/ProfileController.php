<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // ── Display profile page ──────────────────────────────────────────────────
    public function index()
    {
        $user    = auth()->user();
        $role    = $user->role_id;
        $profile = null;

        if ($role == 1) {
            // Teacher
            $profile = DB::table('teacher_details')
                ->where('id', $user->details_id)
                ->first();
        } elseif ($role == 2) {
            // Student
            $profile = DB::table('user_details as ud')
                ->join('section as s',     's.id',  '=', 'ud.section_id')
                ->join('grade_level as gl', 'gl.id', '=', 's.grade_level_id')
                ->where('ud.id', $user->details_id)
                ->select('ud.*', 's.section_name', 'gl.grade_level_name')
                ->first();
        }
        // Admin (role 3) — no details table, only users row

        return view('profile', compact('user', 'role', 'profile'));
    }

    // ── Update personal info ──────────────────────────────────────────────────
    public function update(Request $request)
    {
        $user = auth()->user();
        $role = $user->role_id;

        try {
            if ($role == 3) {
                // Admin — only name and email
                $request->validate([
                    'name'  => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                ]);

                DB::table('users')->where('id', $user->id)->update([
                    'name'       => $request->name,
                    'email'      => $request->email,
                    'updated_at' => now(),
                ]);

            } elseif ($role == 1) {
                // Teacher
                $request->validate([
                    'fname'             => 'required|string|max:100',
                    'lname'             => 'required|string|max:100',
                    'mname'             => 'nullable|string|max:100',
                    'birthdate'         => 'nullable|date',
                    'sex'               => 'nullable|in:Male,Female',
                    'civil_status'      => 'nullable|string',
                    'address'           => 'nullable|string|max:500',
                    'contact_no'        => 'nullable|string|max:20',
                    'email'             => 'required|email|unique:users,email,' . $user->id,
                    'department'        => 'nullable|string',
                    'position'          => 'nullable|string|max:200',
                    'specialization'    => 'nullable|string|max:200',
                    'employment_status' => 'nullable|string',
                    'date_hired'        => 'nullable|date',
                ]);

                DB::table('teacher_details')->where('id', $user->details_id)->update([
                    'fname'             => $request->fname,
                    'lname'             => $request->lname,
                    'mname'             => $request->mname,
                    'birthdate'         => $request->birthdate,
                    'sex'               => $request->sex,
                    'civil_status'      => $request->civil_status,
                    'address'           => $request->address,
                    'contact_no'        => $request->contact_no,
                    'department'        => $request->department,
                    'position'          => $request->position,
                    'specialization'    => $request->specialization,
                    'employment_status' => $request->employment_status,
                    'date_hired'        => $request->date_hired ?: null,
                ]);

                // Sync display name on users table
                DB::table('users')->where('id', $user->id)->update([
                    'name'       => $request->fname . ' ' . $request->lname,
                    'email'      => $request->email,
                    'updated_at' => now(),
                ]);

            } elseif ($role == 2) {
                // Student — read-only academic fields (grade/section assigned by admin)
                $request->validate([
                    'fname'        => 'required|string|max:100',
                    'lname'        => 'required|string|max:100',
                    'mname'        => 'nullable|string|max:100',
                    'birthdate'    => 'nullable|date',
                    'sex'          => 'nullable|in:Male,Female',
                    'Civil_status' => 'nullable|string',
                    'address'      => 'nullable|string|max:500',
                    'contact_no'   => 'nullable|string|max:20',
                    'email'        => 'required|email|unique:users,email,' . $user->id,
                ]);

                DB::table('user_details')->where('id', $user->details_id)->update([
                    'fname'        => $request->fname,
                    'lname'        => $request->lname,
                    'mname'        => $request->mname,
                    'birthdate'    => $request->birthdate,
                    'sex'          => $request->sex,
                    'Civil_status' => $request->Civil_status,
                    'address'      => $request->address,
                    'contact_no'   => $request->contact_no,
                ]);

                DB::table('users')->where('id', $user->id)->update([
                    'name'       => $request->fname . ' ' . $request->lname,
                    'email'      => $request->email,
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Profile updated successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Exception $e) {
            Log::error("Profile Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ── Change password ───────────────────────────────────────────────────────
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect.'], 422);
        }

        try {
            DB::table('users')->where('id', $user->id)->update([
                'password'   => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Password changed successfully.']);
        } catch (\Exception $e) {
            Log::error("Password Change Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
