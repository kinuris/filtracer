<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use App\Models\PartialPersonalRecord;
use App\Models\PersonalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            return redirect('/login')->with('message', 'Invalid Credentials');
        }

        $admin = User::query()->find(Auth::user()->id)->admin();
        if ($admin && !$admin->is_verified && !$admin->is_super) {
            Auth::logout();
            return redirect('/login')->with('message', 'Your officer account is not yet approved or has been disabled by an Admin');
        }

        if ($admin) {
            return redirect('/admin')->with('message', 'Welcome Superadmin');
        }

        if (is_null(Auth::user()->personalBio)) {
            return redirect('/alumni/setup')->with('message', 'Setup your account!');
        }

        if (Auth::user()->personalBio->status == 0) {
            Auth::logout();
            return redirect('/login')->with('message', 'Your account is not yet approved or has been disabled by an Admin.');
        }

        return redirect('/alumni')->with('message', 'Login Successful');
    }

    public function registerAdminView()
    {
        return view('admin-register');
    }

    public function registerAdmin(Request $request)
    {
        if ($request->post('step') == 0) {
            $validated = $request->validate([
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                // 'username' => ['required'],
                // 'password' => ['required'],
                // 'confirm_password' => ['required', 'same:password'],
                'office' => ['required'],
                'position_id' => ['required'],
                // 'email_address' => ['required'],
                // 'phone_number' => ['required'],
            ]);


            if (Admin::query()->where('position_id', '=', $validated['position_id'])->exists()) {
                session()->flashInput($request->input());

                return redirect()->back()
                    ->withErrors(['position_id' => 'Position ID already exists.']);
            }

            return view('admin-register', [
                'step' => 1,
                'validated' => $validated,
            ]);
        }

        if ($request->post('step') == 1) {
            $validated = $request->validate([
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                // 'username' => ['required'],
                // 'password' => ['required'],
                // 'confirm_password' => ['required', 'same:password'],
                'office' => ['required'],
                'position_id' => ['required'],
                // 'email_address' => ['required'],
                // 'phone_number' => ['required'],
            ]);

            $validator = Validator::make($request->all(), [
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                'username' => ['required', 'unique:users,username'],
                'password' => ['required'],
                'confirm_password' => ['required', 'same:password'],
                'office' => ['required'],
                'position_id' => ['required'],
                'email_address' => ['required'],
                'phone_number' => ['required'],
            ]);

            if ($validator->fails()) {
                session()->flashInput($request->input());
                return view('admin-register', [
                    'step' => 1,
                    'validated' => $validated
                ])
                    ->withErrors($validator);
            }

            $validated = $validator->validated();

            $errors = array();

            if (Admin::query()->where('email_address', '=', $validated['email_address'])->exists()) {
                session()->flashInput($request->input());

                $errors['email_address'] = 'Email already taken.';
            }

            if (Admin::query()->where('phone_number', '=', $validated['phone_number'])->exists()) {
                session()->flashInput($request->input());

                $errors['phone_number'] = 'Phone number already taken.';
            }

            if (!empty($errors)) {
                return view('admin-register', [
                    'step' => 1,
                    'validated' => $validated
                ])->withErrors($errors);
            }

            $validated['password'] = bcrypt($validated['password']);
            $validated['name'] = $validated['first_name'] . ' ' . ($validated['middle_name'] ? strtoupper(substr($validated['middle_name'], 0, 1)) . '. ' : '') . $validated['last_name'] . ' ' . $validated['suffix'];

            $user = User::query()->create(array_merge([
                'department_id' => Department::query()->where('name', 'Admins Assigned')->first()->id,
            ], $validated));

            Admin::query()->create(array_merge([
                'user_id' => $user->id,
            ], $validated));

            return redirect('/login')->with('message', 'Registration Successful');
        }
    }

    public function registerAlumniView()
    {
        return view('alumni-register');
    }

    public function registerAlumni(Request $request)
    {
        if ($request->post('step') == 0) {
            $validated = $request->validate([
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                'student_id' => ['required'],
                'department' => ['required'],
            ]);

            if (PartialPersonalRecord::query()->where('student_id', '=', $validated['student_id'])->exists() || PersonalRecord::query()->where('student_id', '=', $validated['student_id'])->exists()) {
                session()->flashInput($request->input());

                return view('alumni-register')
                    ->withErrors(['student_id' => 'Studnet ID already taken.']);
            }

            return view('alumni-register', [
                'step' => 1,
                'validated' => $validated,
            ]);
        }

        if ($request->post('step') == 1) {
            $validated = $request->validate([
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                'student_id' => ['required'],
                'department' => ['required'],
            ]);

            $validator = Validator::make($request->all(), [
                'first_name' => ['required'],
                'middle_name' => ['nullable'],
                'last_name' => ['required'],
                'suffix' => ['nullable'],
                'username' => ['required', 'unique:users,username'],
                'password' => ['required'],
                'password_confirmation' => ['required', 'same:password'],
                'department' => ['required'],
                'email' => ['required', 'email'],
                'contact_number' => ['required'],
                'student_id' => ['required'],
            ]);

            if ($validator->fails()) {
                session()->flashInput($request->input());

                return view('alumni-register', [
                    'step' => 1,
                    'validated' => $validated
                ])
                    ->withErrors($validator)
                    ->withInput($request->input());
            }

            $validated = $validator->validated();
        }

        $validated['name'] = 'autofill';
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_address'] = $validated['email'];
        $validated['department_id'] = $validated['department'];
        $validated['role'] = 'Alumni';

        $alumni = User::query()->create($validated);
        PartialPersonalRecord::query()->create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'suffix' => $validated['suffix'],
            'user_id' => $alumni->id,
            'email_address' => $validated['email'],
            'phone_number' => $validated['contact_number'],
            'student_id' => $validated['student_id'],
        ]);

        return redirect('/login')->with('message', 'Registration Successful');
    }
}
