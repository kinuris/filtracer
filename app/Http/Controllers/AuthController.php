<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSAsyncJob;
use App\Models\Admin;
use App\Models\BoundAccount;
use App\Models\Department;
use App\Models\PartialPersonalRecord;
use App\Models\PersonalRecord;
use App\Models\User;
use App\Models\UserPasswordReset;
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

    public function switchAccount(Request $request, User $user)
    {
        $isAdmin = Auth::user()->role === 'Admin';
        $exists = BoundAccount::query()
            ->where($isAdmin ? 'admin_id' : 'alumni_id', Auth::user()->id)
            ->where($isAdmin ? 'alumni_id' : 'admin_id', $user->id)
            ->exists();

        if (!$exists) {
            return redirect()->back()->with('message', 'Invalid account switch.');
        }

        Auth::logout();

        Auth::loginUsingId($user->id);

        return redirect('/');
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'exists:users,username']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('message', 'Invalid username');
        }

        $user = User::query()
            ->where('username', $request->post('username'))
            ->first();

        // Check if user has reset their password within the last week
        $oneWeekAgo = now()->subDays(7);
        $recentReset = UserPasswordReset::where('user_id', $user->id)
            ->where('created_at', '>=', $oneWeekAgo)
            ->first();

        if ($recentReset) {
            return redirect()->back()
                ->with('message', 'You have already requested a password reset recently.' . "\nYou still have " . $recentReset->created_at->addDays(7)->diffForHumans(now()) . ' before you can request another one.');
        }

        $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        if ($user->role === 'Admin') {
            $number = $user->admin()->philSMSNum();
        } else {
            $number = $user->personalBio->philSMSNum();
        }

        $user->password = bcrypt($random);
        $user->save();

        SendSMSAsyncJob::dispatch(
            $number,
            "🔑 Password Inquiry Request\nYou requested your FilTracer login credentials. Your password is: " . $random . ". Keep it secure and do not share it with anyone."
        );

        $maskedNumber = '+' . substr($number, 0, 4) . '*****' . substr($number, -3);

        $reset = new UserPasswordReset();

        $reset->user_id = $user->id;

        $reset->save();

        return back()
            ->with('reset_number', $maskedNumber);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect('/login')
                ->withErrors($validator)
                ->with('failed_message', 'Invalid Credentials')
                ->with('failed_subtitle', 'Incorrect username or password. Please try again.');
        }

        $validated = $validator->validated();

        if (!Auth::attempt($validated)) {
            return redirect('/login')
                ->with('failed_message', 'Invalid Credentials')
                ->with('failed_subtitle', 'Incorrent username or password. Please try again.');
        }

        $admin = User::query()->find(Auth::user()->id)->admin();
        if ($admin && !$admin->is_verified && !$admin->is_super) {
            Auth::logout();
            return redirect('/login')
                ->with('failed_message', 'Access Denied')
                ->with('failed_subtitle', 'Your officer account is not yet approved or has been disabled by an Admin.');
        }

        if ($admin) {
            return redirect('/admin')
                ->with('message', $admin->is_super ? 'Welcome Super Administrator!' : 'Welcome Administrator!')
                ->with('subtitle', 'You have successfully logged into your admin account.');
        }

        if (is_null(Auth::user()->personalBio)) {
            return redirect('/alumni/setup')
                ->with('message', 'Welcome! Let\'s Set Up Your Account')
                ->with('subtitle', 'Please complete your profile information to continue.');
        }

        if (Auth::user()->personalBio->status == 0) {
            Auth::logout();
            return redirect('/login')
                ->with('failed_message', 'Access Denied')
                ->with('failed_subtitle', 'Your account is not yet approved or has been disabled by an Admin.');
        }

        return redirect('/alumni')
            ->with('message', 'Congratulations!')
            ->with('subtitle', 'You logged in sucessfully.');
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

            return redirect('/login')
                ->with('message', 'Registration Successful')
                ->with('subtitle', 'Your admin account has been created. Please wait for verification before you can log in.');
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

        return redirect('/login')
            ->with('message', 'Registration Successful')
            ->with('subtitle', 'Your alumni account has been created. Please complete your profile after logging in.');
    }
}
