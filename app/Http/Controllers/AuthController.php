<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function logout() {
        auth()->logout();

        return redirect('/login')->with('success', 'You have been logged out.');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!auth()->attempt($validated)) {
            return redirect('/login')->with('error', 'Invalid Credentials');
        }

        $admin = auth()->user()->admin();
        if ($admin) {
            return redirect('/admin');
        }
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
