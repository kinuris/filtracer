<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use App\Models\PartialPersonalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function logout()
    {
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

        $admin = User::query()->find(Auth::user()->id)->admin();
        if ($admin) {
            return redirect('/admin');
        }

        if (is_null(Auth::user()->personalBio)) {
            return redirect('/alumni/setup');
        }

        return redirect('/alumni');
    }

    public function registerAdminView()
    {
        return view('admin-register');
    }

    public function registerAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'username' => ['required'],
            'password' => ['required'],
            'confirm_password' => ['required', 'same:password'],
            'office' => ['required'],
            'position_id' => ['required'],
            'email_address' => ['required'],
            'phone_number' => ['required'],
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::query()->create(array_merge([
            'department_id' => Department::query()->where('name', 'Admins Assigned')->first()->id,
        ], $validated));

        Admin::query()->create(array_merge([
            'user_id' => $user->id,
            'fullname' => $validated['name'],
        ], $validated));

        return redirect('/login')->with('message', 'Registration Successful');
    }

    public function registerAlumniView()
    {
        return view('alumni-register');
    }

    public function registerAlumni(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'username' => ['required', 'unique:users,username'],
            'password' => ['required'],
            'password_confirmation' => ['required', 'same:password'],
            'department' => ['required'],
            'email' => ['required'], // NOTE: Personal
            'contact_number' => ['required'], // NOTE: Personal
            'student_id' => ['required'], // NOTE: Personal
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['department_id'] = $validated['department'];
        $validated['role'] = 'Alumni';

        $alumni = User::query()->create($validated);
        PartialPersonalRecord::query()->create([
            'user_id' => $alumni->id,
            'email_address' => $validated['email'],
            'phone_number' => $validated['contact_number'],
            'student_id' => $validated['student_id'],
        ]);

        return redirect('/login')->with('message', 'Registration Successful');
    }
}
