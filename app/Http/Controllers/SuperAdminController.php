<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSAsyncJob;
use App\Models\Admin;
use App\Models\AdminGenerated;
use App\Models\Department;
use App\Models\ImportGenerated;
use App\Models\ImportHistory;
use App\Models\PartialPersonalRecord;
use App\Models\PersonalRecord;
use App\Models\Post;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Exception;

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

        return back()->with('message', 'Account updated successfully.')
            ->with('subtitle', 'The account details have been updated.');
    }

    public function createAccountView()
    {
        return view('superadmin.create-account');
    }

    public function sendSmsCredentials(Request $request)
    {
        $request->validate([
            'import_history_id' => ['required', 'exists:import_histories,id'],
        ]);

        $users = User::query()
            ->whereRelation('importGenerated', 'import_history_id', '=', $request->import_history_id)
            ->get();

        foreach ($users as $user) {
            $username = $user->username;
            $password = $user->importGenerated->default_password;
            $content = <<<TEXT
            🔑 Login Credentials
            Welcome! Your FilTracer admin account has been created. Use the following credentials to log in.
            Username: $username
            Password: $password
            Change your password after logging in for security. Log in now: https://filtracer.com/login
            TEXT;

            SendSMSAsyncJob::dispatch($user->partialPersonal->philSMSNum(), $content);
        }

        return back()->with('message', 'SMS credentials sent successfully.')
            ->with('subtitle', 'SMS credentials have been sent to the imported users.');
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

        return back()->with('message', 'Account created successfully.')
            ->with('subtitle', 'A new alumni account has been created.');
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
            'office' => ['required'],
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
            'default_password' => $defaultPassword,
        ]);

        Admin::query()->create(array_merge([
            'user_id' => $user->id,
        ], $validated));

        return back()->with('message', 'Admin account created successfully.')
            ->with('subtitle', 'A new admin account has been created.');
    }

    public function createBulkView()
    {
        $users = User::has('importGenerated');


        $importHistoryId = request()->query('send_sms_history');
        if ($importHistoryId && $importHistoryId != -1) {
            $userIds = ImportHistory::query()->find($importHistoryId)->importGenerateds()->pluck('user_id')->unique();

            $users = User::whereIn('id', $userIds);
        }

        if ($search = request()->query('search')) {
            $users = $users->where(function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhereHas('partialPersonal', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('personalBio', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%");
                    });
            });
        }

        $users = $users
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('superadmin.bulk-create-account')->with('users', $users);
    }

    public function accountImport(Request $request)
    {
        $content = $request->json('content');

        $rows = array_map('str_getcsv', explode("\n", $content));
        $header = array_shift($rows);
        $data = array();

        usleep(500000);

        $type = count($header) === 15 ? 'student' : (count($header) === 8 ? 'admin' : null);
        if ($type === null) {
            return response()->json(['message' => 'Wrong csv file structure', 'status' => 'wrongfs'], 400);
        }

        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }

        $filename  = $request->json('filename');
        [$deptShorthand,, $batchEnd] = explode('-', $filename);

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

        if (ImportHistory::query()->where('filename', $filename)->exists()) {
            return response()->json(['status' => 'fexist'], 400);
        }

        DB::beginTransaction();

        try {
            $history = ImportHistory::query()->create([
                'filename' => $filename,
                'data' => json_encode($data),
                'user_id' => Auth::user()->id,
            ]);

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
                    'import_history_id' => $history->id,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage(), 'status' => 'insertf'], 400);
        }

        DB::commit();

        return response()->json(['message' => 'Accounts created successfully', 'status' => 'success']);
    }

    public function viewImports()
    {
        $imports = ImportHistory::paginate(6);

        return view('superadmin.view-imports')->with('imports', $imports);
    }

    public function deletePost(Post $post)
    {
        try {
            UserAlert::query()->create([
                'title' => 'Your post has been removed by Superadmin',
                'action' => '/',
                'content' => $post->title . ' has been deleted',
                'user_id' => $post->creator->id,
            ]);

            $post->delete();

            return back()->with('message', 'Post deleted successfully.')
                ->with('subtitle', 'The selected post has been successfully deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete post: ' . $e->getMessage()]);
        }
    }

    public function postRequestView()
    {
        $statusSelect = request()->query('status', 'Pending');

        $posts = Post::query()
            ->where('status', '=', $statusSelect);

        if (User::query()->find(Auth::user()->id)->admin()->is_super) {
            $posts = $posts->where('user_id', '!=', Auth::user()->id);
        }

        $posts = $posts->latest()
            ->paginate(6);

        return view('superadmin.post-request')->with('posts', $posts);
    }

    public function postChangeStat(Request $request, Post $post)
    {
        $status = $request->query('status');

        $validator = Validator::make(['status' => $status], [
            'status' => ['required', 'in:Approved,Denied'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            $post->update(['status' => $validated['status']]);

            UserAlert::query()->create([
                'title' => 'Your post status has been changed by Superadmin',
                'action' => '/',
                'content' => 'The status of your post "' . $post->title . '" has been changed to ' . $validated['status'],
                'user_id' => $post->creator->id,
            ]);

            return back()->with('message', 'Post status updated successfully.')
                ->with('subtitle', 'The post status has been updated.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Failed to update post status: ' . $e->getMessage()]);
        }
    }
}
