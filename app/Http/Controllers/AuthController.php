<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function logout() {
        Auth::logout();

        return redirect('/login')->with('success', 'You have been logged out.');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($validated)) {
            return redirect('/login')->with('error', 'Invalid Credentials');
        }

        $admin = Auth::user()->admin();
        if ($admin) {
            return redirect('/admin');
        }

        return redirect('/alumni');
    }

    public function registerAdminView()
    {
        return view('admin-register');
    }

    public function registerAdmin(Request $request) {}

    public function registerAlumniView()
    {
        return view('alumni-register');
    }
}
