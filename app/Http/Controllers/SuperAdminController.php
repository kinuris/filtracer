<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminGenerated;
use App\Models\Department;
use App\Models\ImportGenerated;
use App\Models\ImportHistory;
use App\Models\PartialPersonalRecord;
use App\Models\PersonalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function updateAccount(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'student_id' => ['required'],
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'username' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('openModal', 1);
        }

        $validated = $validator->validated();

        $user->update(['username' => $validated['username']]);
        $user->personalBio->update($validated);

        return back()->with('message', 'Account updated successfully.');
    }

    public function createAccountView()
    {
        return view('superadmin.create-account');
    }

    public function createAccount(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'suffix' => ['nullable'],
            'student_id' => ['required'],
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'username' => ['required', 'unique:users,username'],
            'password' => ['required'],
            'department' => ['required'],
        ]);

        $errors = array();

        if (PartialPersonalRecord::query()->where('student_id', '=', $validated['student_id'])->exists() || PersonalRecord::query()->where('student_id', '=', $validated['student_id'])->exists()) {
            session()->flashInput($request->input());

            $errors['student_id'] = 'Student ID already taken.';
        }

        if (PartialPersonalRecord::query()->where('email_address', '=', $validated['email'])->exists() || PersonalRecord::query()->where('email_address', '=', $validated['email'])->exists()) {
            session()->flashInput($request->input());

            $errors['email'] = 'Email already taken.';
        }

        if (PartialPersonalRecord::query()->where('phone_number', '=', $validated['contact_number'])->exists() || PersonalRecord::query()->where('phone_number', '=', $validated['contact_number'])->exists()) {
            session()->flashInput($request->input());

            $errors['contact_number'] = 'Contact number already taken.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        $defaultPassword = $validated['password'];

        $validated['name'] = 'autofill';
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_address'] = $validated['email'];
        $validated['department_id'] = $validated['department'];
        $validated['role'] = 'Alumni';

        $user = User::query()->create($validated);

        AdminGenerated::query()->create([
            'user_id' => $user->id,
            'default_password' => $defaultPassword,
        ]);

        PartialPersonalRecord::query()->create([
            'user_id' => $user->id,
            'student_id' => $validated['student_id'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'suffix' => $validated['suffix'],
            'email_address' => $validated['email'],
            'phone_number' => $validated['contact_number'],
            'department_id' => $validated['department'],
        ]);

        return back()->with('message', 'Account created successfully.');
    }

    public function createAdmin()
    {
        $validated = request()->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'suffix' => ['nullable'],
            'employee_id' => ['required'],
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'username' => ['required', 'unique:users,username'],
            'password' => ['required'],
        ]);

        $errors = array();
        if (Admin::query()->where('phone_number', '=', $validated['contact_number'])->exists()) {
            session()->flashInput(request()->input());

            $errors['contact_number'] = 'Contact number already taken.';
        }

        if (Admin::query()->where('email_address', '=', $validated['email'])->exists()) {
            session()->flashInput(request()->input());

            $errors['email'] = 'Email already taken.';
        }


        if (Admin::query()->where('position_id', '=', $validated['employee_id'])->exists()) {
            session()->flashInput(request()->input());

            $errors['employee_id'] = 'Position ID already taken.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        $defaultPassword = $validated['password'];

        $validated['name'] = 'autofill';
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_address'] = $validated['email'];
        $validated['role'] = 'Admin';
        $validated['department_id'] = 1;
        $validated['position_id'] = $validated['employee_id'];
        $validated['phone_number'] = $validated['contact_number'];

        $user = User::query()->create($validated);

        AdminGenerated::query()->create([
            'user_id' => $user->id,
            'default' => $defaultPassword,
        ]);

        Admin::query()->create(array_merge([
            'user_id' => $user->id,
        ], $validated));

        return back()->with('message', 'Admin account created successfully.');
    }

    public function createBulkView()
    {
        $users = User::has('importGenerated');

        $users = $users->paginate(6);

        return view('superadmin.bulk-create-account')->with('users', $users);
    }

    public function accountImport(Request $request)
    {
        $content = $request->json('content');

        $rows = array_map('str_getcsv', explode("\n", $content));
        $header = array_shift($rows);
        $data = array();

        usleep(500000);

        $type = count($header) === 14 ? 'student' : (count($header) === 8 ? 'admin' : null);
        if ($type === null) {
            return response()->json(['message' => 'Wrong csv file structure', 'status' => 'wrongfs'], 400);
        }

        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }

        $filename  = $request->json('filename');
        [$deptShorthand,, $batchEnd] = explode('-', $filename);
        // ImportHistory::query()->create([
        //     'filename' => $filename,
        //     'data' => json_encode($data),
        //     'user_id' => Auth::user()->id,
        // ]);

        $departments = [];
        foreach (Department::all() as $dept) {
            if ($dept->shortened() === $deptShorthand) {
                $departments[] = $dept;
            }
        }

        if (empty($departments)) {
            return response()->json(['message' => 'Department not found', 'status' => 'deptnf'], 400);
        }

        $department = $departments[0];

        foreach ($data as $row) {
            $username = strtolower($row['Student No.']);
            $password = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
            $role = 'Alumni';
            $firstname = $row['First Name'];
            $middlename = $row['Middle Name'];
            $lastname = $row['Last Name'];
            $suffix = $row['Suffix'];
            $phonenumber = $row['Contact No. - Parent/Guardian'];
            $email = $row['Email Address'];
            // $course = $row['Course'];
            // $homeaddress = $row['Home Address'];

            $user = User::query()->create([
                'name' => 'autofill',
                'username' => $username,
                'password' => bcrypt($password),
                'role' => $role,
                'department_id' => $department->id,
            ]);

            PartialPersonalRecord::query()->create([
                'student_id' => $username,
                'user_id' => $user->id,
                'first_name' => $firstname,
                'middle_name' => $middlename,
                'last_name' => $lastname,
                'suffix' => $suffix,
                'phone_number' => $phonenumber,
                'email_address' => $email,
            ]);

            ImportGenerated::query()->create([
                'user_id' => $user->id,
                'default_password' => $password,
            ]);
        }

        return response()->json(['message' => 'Accounts created successfully', 'status' => 'success']);
    }

    public function viewImports() {
        $imports = ImportHistory::paginate(6);

        return view('superadmin.view-imports')->with('imports', $imports);
    }
}