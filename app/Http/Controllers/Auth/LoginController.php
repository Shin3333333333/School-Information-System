<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Make sure your User model is in App\Models

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function ajaxLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password'  => ['required', 'string'],
        ]);

        $user = User::with('role')->where('email', $credentials['email'])->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            // Store role in session and get the role name
            $roleName = $user->role ? $user->role->name : null;
            $request->session()->put('user_role', $roleName);

            return response()->json(['success' => true, 'role' => $roleName]);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid credentials']);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}