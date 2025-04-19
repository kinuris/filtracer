<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSAsyncJob;
use App\Models\BindingRequest;
use App\Models\ChatGroup;
use App\Models\Course;
use App\Models\Department;
use App\Models\EducationRecord;
use App\Models\Major;
use App\Models\PersonalRecord;
use App\Models\Post;
use App\Models\ProfessionalRecord;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Models\Audit;

class AdminController extends Controller
{
    public function dashboardView()
    {
        return view('admin.dashboard');
    }

    public function departmentView()
    {
        return view('department.index');
    }

    public function removeProfilePicture(User $user)
    {
        if (strpos($user->admin()->profile_picture, '-profile.png') !== false) {
            return back()->with([
                'failed_message' => 'Cannot remove default profile picture!',
                'failed_subtitle' => 'The default profile picture cannot be removed.'
            ]);
        }

        if ($user->role == 'Admin') {
            if (!in_array($user->admin()->profile_picture, ['admin-profile.png', 'superadmin-profile.png'])) {
                Storage::delete('public/user/images/' . $user->admin()->profile_picture);
            }

            $user->admin()->update(['profile_picture' => $user->admin()->is_super ? 'superadmin-profile.png' : 'admin-profile.png']);
        } else {
            if ($user->personalBio->profile_picture !== 'alumni-profile.png') {
                Storage::delete('public/user/images/' . $user->personalBio->profile_picture);
            }

            $user->getPersonalBio()->update(['profile_picture' => 'alumni-profile.png']);
        }

        return back()->with([
            'message' => 'Profile picture removed successfully!',
            'subtitle' => 'Your profile picture has been removed from your account'
        ]);
    }

    public function alumniCoursesView(Course $course)
    {
        $users = User::query();

        if (request('mode') === 'generated') {
            $users = User::has('adminGenerated');
        }

        $role = request()->query('user_role', 'Alumni');
        if ($role === 'Alumni' && request('mode') !== 'generated') {
            $users = User::query()->where('role', '=', 'Alumni');
        } elseif ($role === 'Alumni' && request('mode') === 'generated') {
            $users = User::has('adminGenerated')
                ->where('role', '=', 'Alumni')
                ->whereHas('adminGenerated');
        } else {
            $users = $users->where('id', '!=', Auth::user()->id)
                ->where('role', '=', 'Admin');
        }

        $search = request()->query('search');
        if ($search) {
            $relationName = $role === 'Alumni' ? 'personalBio' : 'adminRelation';
            $searchPattern = '%' . $search . '%';

            $users = $users->where(function ($query) use ($relationName, $searchPattern) {
                $query->whereRelation($relationName, 'first_name', 'LIKE', $searchPattern)
                    ->orWhereRelation($relationName, 'middle_name', 'LIKE', $searchPattern)
                    ->orWhereRelation($relationName, 'last_name', 'LIKE', $searchPattern);
            });
        }

        $currentUser = Auth::user();
        if (!User::query()->find($currentUser->id)->admin()->is_super) {
            $users = $users->where('department_id', '=', $currentUser->department_id);
        }

        $status = (int) request()->query('user_status', -1);
        if ($status !== -1) {
            if ($role === 'Alumni') {
                $users = $users->whereHas('personalBio', function ($query) {
                    $query->where('status', '=', request()->query('user_status'));
                });
            } else {
                $users = $users->whereHas('adminRelation', function ($query) {
                    $query->where('is_verified', '=', request()->query('user_status'));
                });
            }
        }

        $users = $users->whereRelation('educationalBios', 'course_id', '=', $course->id);
        $users = $users->paginate(6);

        return view('alumni.course')
            ->with('users', $users);
    }

    public function alumniProfilesView()
    {
        $courses = Course::query()
            ->where('department_id', '=', Auth::user()->admin()->office)
            ->get();

        return view('alumni.profiles')->with('courses', $courses);
    }

    public function sendSMSIndividual(User $user)
    {
        $username = $user->username;
        $password = $user->adminGenerated->default_password;

        $content = <<<TEXT
            🔑 Login Credentials
            Welcome! Your FilTracer admin account has been created. Use the following credentials to log in.
            Username: $username
            Password: $password
            Change your password after logging in for security. Log in now: https://filtracer.com/login
            TEXT;

        SendSMSAsyncJob::dispatch(
            $user->role === 'Admin' ? $user->admin()->philSMSNum() : $user->partialPersonal->philSMSNum(),
            $content,
        );

        return back()->with([
            'message' => 'SMS sent successfully!',
            'subtitle' => 'Login credentials have been sent to ' . $user->name . '.'
        ]);
    }

    public function createLink(Request $request)
    {
        $validated = $request->validate([
            'alumni_id' => ['required'],
        ]);

        $bindReq = new BindingRequest();

        $bindReq->alumni_id = $validated['alumni_id'];
        $bindReq->admin_id = Auth::user()->id;
        $bindReq->is_denied = false;

        $bindReq->save();

        return back()->with([
            'message' => 'Binding request created successfully',
            'subtitle' => 'A linking request has been sent to the alumni.'
        ]);
    }

    public function deleteLink(Request $request, User $alumni)
    {
        $bindingReq = $alumni->hasActiveBindingRequestWith(Auth::user())->first();
        BindingRequest::query()->where('id', '=', $bindingReq->id)->delete();

        return back()->with([
            'message' => 'Successfully removed linking request',
            'subtitle' => 'The connection request with this alumni has been deleted.'
        ]);
    }

    public function linkAccount()
    {
        $users = User::query();

        if (request('mode') === 'generated') {
            $users = User::has('adminGenerated');
        }

        $role = request()->query('user_role', 'Alumni');
        if ($role === 'Alumni' && request('mode') !== 'generated') {
            $users = User::query()->where('role', '=', 'Alumni');
        } elseif ($role === 'Alumni' && request('mode') === 'generated') {
            $users = User::has('adminGenerated')
                ->where('role', '=', 'Alumni')
                ->whereHas('adminGenerated');
        } else {
            $users = $users->where('id', '!=', Auth::user()->id)
                ->where('role', '=', 'Admin');
        }

        $search = request()->query('search');
        if ($search) {
            if ($role === 'Alumni') {
                $users = $users->whereRelation('personalBio', 'first_name', 'LIKE', '%' . $search . '%')
                    ->orWhereRelation('personalBio', 'middle_name', 'LIKE', '%' . $search . '%')
                    ->orWhereRelation('personalBio', 'last_name', 'LIKE', '%' . $search . '%');
            } else {
                $users = $users->whereRelation('adminRelation', 'first_name', 'LIKE', '%' . $search . '%')
                    ->orWhereRelation('adminRelation', 'middle_name', 'LIKE', '%' . $search . '%')
                    ->orWhereRelation('adminRelation', 'last_name', 'LIKE', '%' . $search . '%');
            }
        }

        $status = (int) request()->query('user_status', -1);
        if ($status !== -1) {
            if ($role === 'Alumni') {
                $users = $users->whereHas('personalBio', function ($query) {
                    $query->where('status', '=', request()->query('user_status'));
                });
            } else {
                $users = $users->whereHas('adminRelation', function ($query) {
                    $query->where('is_verified', '=', request()->query('user_status'));
                });
            }
        }

        $users = $users->paginate(6);

        return view('admin.link-account')
            ->with('users', $users)
            ->with('status', $status);
    }

    public function updateAccountFromVerify(Request $request, User $user)
    {
        if ($user->role === 'Admin') {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'position_id' => ['required'],
                'email_address' => ['required', 'string', 'email', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20'],
                'office' => ['required', 'exists:departments,id'],
            ]);

            $user->admin()->update([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'phone_number' => $validated['phone_number'],
                'position_id' => $validated['position_id'],
                'department_id' => $validated['office'],
            ]);

            $user->update([
                'username' => $validated['username'],
                'email' => $validated['email_address'],
            ]);

            return back()->with([
                'message' => 'Account updated successfully from verification',
                'subtitle' => 'The user profile has been updated with the verified information'
            ]);
        }

        // Validate the request data
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email_address' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'student_id' => ['required', 'string', 'max:50'],
            'course_id' => ['required', 'exists:courses,id'],
            'start' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'end' => ['required', 'integer', 'min:1900', 'max:' . date('Y'), 'gte:start'],
        ]);

        // Update user basic information
        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email_address'],
        ]);

        // Update additional information for Alumni users
        if ($user->role == 'Alumni') {
            $user->getPersonalBio()->update([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'],
                'phone_number' => $validated['phone_number'],
                'student_id' => $validated['student_id'],
            ]);

            // Update educational information
            $user->getEducationalBio()->update([
                'course_id' => $validated['course_id'],
                'year_start' => $validated['start'],
                'year_end' => $validated['end'],
            ]);
        }

        return back()->with([
            'message' => 'Account updated successfully from verification',
            'subtitle' => 'The user profile has been updated with the verified information'
        ]);
    }

    public function rejectPost(Post $post)
    {
        $post->update(['status' => 'Denied']);

        return redirect('/admin')->with([
            'message' => 'Post rejected successfully',
            'subtitle' => 'The post has been marked as denied and will not be visible to users'
        ]);
    }

    public function approvePost(Post $post)
    {
        $post->update(['status' => 'Approved']);

        return redirect('/admin')->with([
            'message' => 'Post approved successfully',
            'subtitle' => 'The post is now visible to all users'
        ]);
    }

    public function userView(User $user)
    {
        // Ensure the admin belongs to the same department unless superadmin
        $admin = Auth::user();
        if (!$admin->admin()->is_super && $admin->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access to this user profile.');
        }

        return view('alumni.view')
            ->with('dept', Department::query()->find($user->department_id))
            ->with('user', $user);
    }

    public function updateAlumniProfile(Request $request, Department $dept, User $user)
    {
        // Authorization check (redundant if route middleware is used, but good practice)
        $admin = Auth::user();
        if (!$admin->admin()->is_super && $admin->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        // --- Validation --- 
        $personalData = $request->only([
            'first_name', 'middle_name', 'last_name', 'suffix', 'student_id', 
            'gender', 'birthdate', 'civil_status', 'phone_number', 'email_address',
            'permanent_address', 'current_address', 'social_link'
        ]);
        $userData = $request->only(['username']);
        $educationData = $request->input('educ', []);
        $professionalData = $request->input('prof', []);

        // Basic User Validation
        $userValidator = Validator::make($userData, [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        // Personal Info Validation
        $personalValidator = Validator::make($personalData, [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'student_id' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'birthdate' => ['required', 'date'],
            'civil_status' => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'phone_number' => ['required', 'string', 'max:20'],
            'email_address' => ['required', 'string', 'email', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'current_address' => ['nullable', 'string', 'max:500'],
            'social_link' => ['nullable', 'url', 'max:255'],
        ]);

        // Education Info Validation (Iterate through each record)
        $educationValidators = [];
        foreach ($educationData as $id => $data) {
            $educationValidators[$id] = Validator::make($data, [
                'school' => ['required', 'string', 'max:255'],
                'degree_type' => ['required', 'string', 'max:100'],
                'course_id' => ['required', 'exists:courses,id'],
                'school_location' => ['nullable', 'string', 'max:255'],
                'start' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
                'end' => ['required', 'string', 'max:10'], // Allow 'Present' or year
            ]);
        }

        // Professional Info Validation (Iterate through each record)
        $professionalValidators = [];
        foreach ($professionalData as $id => $data) {
            $professionalValidators[$id] = Validator::make($data, [
                'employment_status' => ['required'], // Assuming a static method
                'job_title' => ['required', 'string', 'max:255'],
                'employment_type1' => ['required'], // Assuming a static method
                'company_name' => ['required', 'string', 'max:255'],
                'employment_type2' => ['required'], // Assuming a static method
                'monthly_salary' => ['required'], // Assuming a static method
                'industry' => ['required'],
                'work_location' => ['nullable', 'string', 'max:255'],
                'waiting_time' => ['required'], // Assuming a static method
            ]);
        }

        // Combine validation errors
        $errors = $userValidator->errors()->merge($personalValidator->errors());
        foreach ($educationValidators as $id => $validator) {
            $errors = $errors->merge($validator->errors()->messages(), "educ.{$id}.");
        }
        foreach ($professionalValidators as $id => $validator) {
            $errors = $errors->merge($validator->errors()->messages(), "prof.{$id}.");
        }

        if ($errors->isNotEmpty()) {
            return back()->withErrors($errors)->withInput()->with('edit', 'true'); // Redirect back with errors and input
        }

        // --- Update Logic --- 
        try {
            DB::beginTransaction();

            // Update User model
            $user->update($userData);

            // Update PersonalRecord
            $personalRecord = $user->getPersonalBio() ?? $user->partialPersonal;
            if ($personalRecord) {
                // Ensure birthdate is formatted correctly if needed (depends on model casting)
                // $personalData['birthdate'] = \Carbon\Carbon::parse($personalData['birthdate'])->format('Y-m-d');
                $personalRecord->update($personalData);
            }

            // Update EducationRecords
            foreach ($educationData as $id => $data) {
                $educRecord = EducationRecord::find($id);
                if ($educRecord && $educRecord->user_id === $user->id) { // Ensure record belongs to user
                    $educRecord->update($data);
                }
            }

            // Update ProfessionalRecords
            foreach ($professionalData as $id => $data) {
                $profRecord = ProfessionalRecord::find($id);
                if ($profRecord && $profRecord->user_id === $user->id) { // Ensure record belongs to user
                    $profRecord->update($data);
                }
            }

            DB::commit();

            return redirect()->route('admin.alumni.profile.view', [$dept->id, $user->id])->with([
                'message' => 'Profile updated successfully!',
                'subtitle' => $user->name . '\'s profile has been updated.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error("Error updating profile for user {$user->id}: " . $e->getMessage());
            return back()->withInput()->with('edit', 'true')->with([
                'failed_message' => 'Profile update failed!',
                'failed_subtitle' => 'An unexpected error occurred. Please try again.'
            ]);
        }
    }

    public function auditView()
    {
        $audits = Audit::query()
            ->latest();

        $search = request()->query('search');
        if ($search) {
            $audits = $audits->where('old_values', 'LIKE', '%' . $search . '%')
                ->orWhere('new_values', 'LIKE', '%' . $search . '%');
        }

        $audits = $audits->paginate(5);

        return view('audit.index')->with('audits', $audits);
    }

    public function reportsGraphicalView()
    {
        return view('report.graphical');
    }

    public function reportsStatisticalView()
    {
        $users = User::compSet()
            ->where('role', '=', 'Alumni')
            ->orderBy('created_at', 'DESC');

        $search = request()->query('search');
        if ($search) {
            $users = $users->whereRelation('partialPersonal', 'first_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('partialPersonal', 'middle_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('partialPersonal', 'last_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'first_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'middle_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'last_name', 'LIKE', '%' . $search . '%');
        }

        if (!User::query()->find(Auth::user()->id)->admin()->is_super) {
            $users = $users->where('department_id', '=', User::query()->find(Auth::user()->id)->admin()->office);
        }

        $users = $users->paginate(7);
        $view = view('report.statistical');

        // $user = Auth::user();
        // if ($user->role === 'Admin' && !User::query()->find($user->id)->admin()->is_super) {
        //     $view->with('adminDept', Department::query()->find(User::query()->find($user->id)->admin()->office));
        // }

        return $view->with('users', $users);
    }

    public function reportsStatisticalGenerateView()
    {
        return view('report.statistical-generate');
    }

    public function uploadProfilePicture(Request $request, User $user)
    {
        $request->validate([
            'profile' => ['required', 'mimes:jpg,jpeg,png', 'max:10000']
        ]);

        $profile = $request->file('profile');

        if ($user->role == 'Admin') {
            if (!in_array($user->admin()->profile_picture, ['admin-profile.png', 'superadmin-profile.png'])) {
                Storage::delete('public/user/images/' . $user->admin()->profile_picture);
            }
        } else {
            if ($user->personalBio->profile_picture !== 'alumni-profile.png') {
                Storage::delete('public/user/images/' . $user->personalBio->profile_picture);
            }
        }

        $filename = sha1(time() . $user->name) . '.' . $profile->getClientOriginalExtension();
        $profile->storePubliclyAs('public/user/images', $filename);

        if ($user->role == 'Admin') {
            $user->admin()->update([
                'profile_picture' => $filename
            ]);
        } else {
            $user->getPersonalBio()->update([
                'profile_picture' => $filename,
            ]);
        }


        return redirect('/settings/account')->with([
            'message' => 'Profile picture uploaded successfully!',
            'subtitle' => 'Your new profile picture has been applied to your account'
        ]);
    }

    public function myProfileView()
    {
        return view('admin.profile');
    }

    public function settingsView()
    {
        return view('settings.index');
    }

    public function profileReportView(User $alumni)
    {
        return view('alumni.profile-report')->with('alumni', $alumni);
    }

    public function departmentSettingsView()
    {
        $depts = Department::query()->where('name', '!=', 'Admins Assigned');

        $search = request('search');
        if ($search) {
            $depts = $depts->where('name', 'LIKE', '%' . $search . '%');
        }

        $depts = $depts->paginate(5);

        return view('settings.department')->with('departments', $depts);
    }

    public function accountsSettingsView()
    {
        return view('settings.account');
    }

    public function coursesSettingsView()
    {
        $courses = Course::query();
        $user = User::query()->find(Auth::user()->id);

        if (!$user->admin()->is_super) {
            $courses = $courses->where('department_id', '=', $user->department_id);
        }

        $search = request()->query('search');
        if ($search) {
            $courses = $courses->where('name', 'LIKE', '%' . $search . '%');
        }

        $courses = $courses->paginate(6);

        return view('settings.course')->with('courses', $courses);
    }

    public function editCourseView(Course $course)
    {
        return view('settings.course-update')->with('course', $course);
    }

    public function majorsSettingsView()
    {
        $majors = Major::query();

        $search = request()->query('search');
        if ($search) {
            $majors = $majors->where('name', 'LIKE', '%' . $search . '%');
        }

        $majors = $majors->paginate(6);

        return view('settings.majors')->with('majors', $majors);
    }

    public function editMajorView(Major $major)
    {
        return view('settings.major-update')->with('major', $major);
    }

    public function accountsView()
    {
        $users = User::query();

        if (request('mode') === 'generated') {
            $users = User::has('adminGenerated');
        }

        $role = request()->query('user_role', 'Alumni');
        if ($role === 'Alumni' && request('mode') !== 'generated') {
            $users = User::query()->where('role', '=', 'Alumni');
        } elseif ($role === 'Alumni' && request('mode') === 'generated') {
            $users = User::has('adminGenerated')
                ->where('role', '=', 'Alumni')
                ->whereHas('adminGenerated');
        } else {
            $users = $users->where('id', '!=', Auth::user()->id)
                ->where('role', '=', 'Admin');
        }

        $search = request()->query('search');
        if ($search) {
            $relationName = $role === 'Alumni' ? 'personalBio' : 'adminRelation';
            $searchPattern = '%' . $search . '%';

            $users = $users->where(function ($query) use ($relationName, $searchPattern) {
                $query->whereRelation($relationName, 'first_name', 'LIKE', $searchPattern)
                    ->orWhereRelation($relationName, 'middle_name', 'LIKE', $searchPattern)
                    ->orWhereRelation($relationName, 'last_name', 'LIKE', $searchPattern);
            });
        }

        $currentUser = Auth::user();
        if (!User::query()->find($currentUser->id)->admin()->is_super) {
            $users = $users->where('department_id', '=', $currentUser->department_id);
        }

        $status = (int) request()->query('user_status', -1);
        if ($status !== -1) {
            if ($role === 'Alumni') {
                $users = $users->whereHas('personalBio', function ($query) {
                    $query->where('status', '=', request()->query('user_status'));
                });
            } else {
                $users = $users->whereHas('adminRelation', function ($query) {
                    $query->where('is_verified', '=', request()->query('user_status'));
                });
            }
        }

        $users = $users
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return view('account.index')->with('users', $users);
    }

    public function alumniListView(Request $request, Department $department)
    {
        // $users = User::hasBio('educational');
        $users = User::query()->where('role', '=', 'Alumni');

        $course = (int) $request->query('course');
        if ($course && $course !== -1) {
            $users = $users->whereHas('educationalBios', function ($query) use ($course) {
                $query->where('course_id', '=', $course);
            });
        }

        $search = $request->query('search');
        if ($search) {
            $users = $users
                ->whereRelation('partialPersonal', 'first_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('partialPersonal', 'middle_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('partialPersonal', 'last_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'first_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'middle_name', 'LIKE', '%' . $search . '%')
                ->orWhereRelation('personalBio', 'last_name', 'LIKE', '%' . $search . '%');
        }

        $users = $users
            ->where('department_id', '=', $department->id)
            ->where('role', '=', 'Alumni')
            ->paginate(7);


        $courses = $department->getCourses();

        return view('alumni.index')
            ->with('dept', $department)
            ->with('courses', $courses)
            ->with('users', $users);
    }

    public function editDepartmentView(Department $department)
    {
        return view('department.update')->with('department', $department);
    }

    public function createDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:departments'],
            'logo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
        ]);

        $logo = $request->file('logo');
        $logoName = sha1(time()) . '.' . $logo->getClientOriginalExtension();

        $logo->storePubliclyAs('/public/departments', $logoName);
        $validated['logo'] = $logoName;

        Department::query()->create($validated);

        return redirect('/settings/department')->with([
            'message' => 'Department created successfully',
            'subtitle' => 'The new department has been added to the system'
        ]);
    }

    public function editMajor(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => [
                'required',
            ],
            'course' => [
                'required',
            ]
        ]);

        $validated['course_id'] = $validated['course'];

        $major->update($validated);

        return redirect('/settings/major')->with([
            'message' => 'Major updated successfully',
            'subtitle' => 'The changes to the major have been saved'
        ]);
    }

    public function editCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'department' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];

        $course->update($validated);

        return redirect('/settings/course')->with([
            'message' => 'Course updated successfully',
            'subtitle' => 'The changes to the course have been saved'
        ]);
    }

    public function accountEdit(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'suffix' => ['nullable'],
            'username' => ['required', Rule::unique('users')->ignore($user->id)],
            'position' => ['required'],
            'department' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
        ]);

        $validated['office'] = $validated['department'];
        $validated['email_address'] = $validated['email'];
        $validated['phone_number'] = $validated['phone'];
        $validated['position_id'] = $validated['position'];

        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email_address'],
        ]);

        $user->admin()->update($validated);

        return redirect('/settings/account')->with([
            'message' => 'Account updated successfully',
            'subtitle' => 'Your account information has been updated'
        ]);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('departments')
                    ->ignore($department->id)
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
        ]);

        if ($request->hasFile('logo')) {
            Storage::delete('/public/departments/' . $department->logo);

            $logo = $request->file('logo');
            $logoName = sha1(time()) . '.' . $logo->getClientOriginalExtension();

            $logo->storePubliclyAs('/public/departments', $logoName);

            $validated['logo'] = $logoName;
        }

        $department->update($validated);

        return redirect('/settings/department')->with([
            'message' => 'Department updated successfully',
            'subtitle' => 'The department information has been updated'
        ]);
    }

    public function delelteDepartment(Department $department)
    {
        Storage::delete('/public/departments/' . $department->logo);
        $department->delete();

        return redirect('/settings/department')->with([
            'message' => 'Department deleted successfully',
            'subtitle' => 'The department and all its associated data have been removed'
        ]);
    }

    public function verifyUser(User $user)
    {
        if ($user->admin() != null) {
            $user->admin()->update(['is_verified' => true]);
        } else {
            $user->getPersonalBio()->update(['status' => 1]);
        }

        $content = 'User: ' . $user->name . ' has been VERIFIED by ' . Auth::user()->name;
        $action = $user->role === 'Admin' ? ('/account?unverify_modal=' . $user->id) : '/user/view/' . $user->id;

        foreach (User::query()->where('role', '=', 'Admin')->get() as $admin) {
            UserAlert::query()->create([
                'title' => 'User VERIFIED',
                'content' => $content,
                'action' => $action,
                'user_id' => $admin->id
            ]);
        }
        SendSMSAsyncJob::dispatch(
            $user->role === 'Admin' ? $user->admin()->philSMSNum() : $user->personalBio->philSMSNum(),
            "✅ Account Verified\nCongratulations, " . ($user->role === 'Admin' ? $user->admin()->first_name : $user->personalBio->first_name) . "! Your FilTracer account has been verified. Log in now to connect with other users and explore opportunities. Visit: https://filtracer.com/login"
        );

        $previousUrl = url()->previous();
        $parsedUrl = parse_url($previousUrl);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queries);
            unset($queries['verify_modal']);
            $previousUrl = $parsedUrl['path'] . '?' . http_build_query($queries);
        }

        return redirect($previousUrl)->with([
            'message' => 'Account verified!',
            'subtitle' => 'The user can now access all system features'
        ]);
    }

    public function unverifyUser(User $user)
    {
        if ($user->admin() != null) {
            $user->admin()->update(['is_verified' => false]);
        } else {
            $user->getPersonalBio()->update(['status' => 0]);
        }

        $content = 'User: ' . $user->name . ' has been UNVERIFIED by ' . Auth::user()->name;
        $action = $user->role === 'Admin' ? ('/account?verify_modal=' . $user->id) : '/user/view/' . $user->id;

        foreach (User::query()->where('role', '=', 'Admin')->get() as $admin) {
            UserAlert::query()->create([
                'title' => 'User UNVERIFIED',
                'content' => $content,
                'action' => $action,
                'user_id' => $admin->id
            ]);
        }

        SendSMSAsyncJob::dispatch(
            $user->role == 'Admin' ? $user->admin()->philSMSNum() : $user->personalBio->philSMSNum(),
            "❌ Account Unverified\nWe regret to inform you that your FilTracer account has been unverified. Please contact the admin for more information."
        );

        $previousUrl = url()->previous();
        $parsedUrl = parse_url($previousUrl);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queries);
            unset($queries['unverify_modal']);
            $previousUrl = $parsedUrl['path'] . '?' . http_build_query($queries);
        }

        return redirect($previousUrl)->with([
            'message' => 'Account unverified!',
            'subtitle' => 'The user will have limited access to system features'
        ]);
    }

    public function createCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:courses'],
            'department' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];

        Course::query()->create($validated);

        return redirect('/settings/course')->with([
            'message' => 'Course created successfully',
            'subtitle' => 'The new course has been added to the system'
        ]);
    }

    public function chatView()
    {
        $user = User::query()->find(Auth::user()->id);
        $view = view('chat.admin');

        $selected = request('initiate');

        if ($selected && is_numeric($selected)) {
            $selected = User::query()->find($selected);

            if ($selected->chatGroupWith($user) == null && request('override') == null) {
                return redirect($user->role === 'Admin' ? '/admin/chat' : '/alumni/chat')->with([
                    'failed_message' => 'Chat group not found',
                    'failed_subtitle' => 'The chat group you are trying to access does not exist'
                ]);
            }
        } elseif ($selected && !is_numeric($selected)) {
            $selected = ChatGroup::query()->where('internal_id', '=', urldecode(request('initiate')))->first();

            if ($selected == null) {
                return redirect($user->role === 'Admin' ? '/admin/chat' : '/alumni/chat')->with([
                    'failed_message' => 'Chat group not found',
                    'failed_subtitle' => 'The chat group you are trying to access does not exist'
                ]);
            }
        }

        if ($selected) {
            if ($selected instanceof ChatGroup) {
                $view->with('association', $selected->associations()->where('user_id', '=', $user->id)->first());
                $view->with('group', $selected);
            } elseif ($selected instanceof User && request('override') == null) {
                $view->with('association', $selected->chatGroupWith($user)->associations()->where('user_id', '=', $user->id)->first());
                $view->with('group', $selected->chatGroupWith($user));
            }
        }

        return $view
            ->with('selected', $selected)
            ->with('chatGroups', $user->chatGroups());
    }

    public function createMajor(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:majors'],
            'course_id' => ['required'],
        ]);

        $validated['description'] = '';

        Major::query()->create($validated);

        return redirect('/settings/major')->with([
            'message' => 'Major created successfully',
            'subtitle' => 'The new major has been added to the system'
        ]);
    }

    public function postView()
    {
        $category = request('category', 'All Posts');

        $posts = Post::query()->where('status', '=', 'Approved');

        if ($category === 'Events') {
            $posts = $posts->where('post_category', 'Event');
        } else if ($category === 'Job Openings') {
            $posts = $posts->where('post_category', 'Job Opening');
        } else if ($category === 'Announcements') {
            $posts = $posts->where('post_category', 'Announcement');
        } else if ($category === 'Your Posts') {
            $posts = Post::query();
            $posts = $posts->where('user_id', Auth::user()->id);
        } else if ($category === 'Saved Posts') {
            $posts = User::query()
                ->find(Auth::user()->id)
                ->savedPostsAsPosts();
        } else if ($category === 'Pinned Posts') {
            $posts = User::query()
                ->find(Auth::user()->id)
                ->pinnedPostsAsPosts();
        }

        $posts = $posts->latest()
            ->get();

        return view('post.admin')->with('posts', $posts);
    }

    public function deleteMajorView(Major $major)
    {
        $major->delete();

        return redirect('/settings/major')->with([
            'message' => 'Major deleted successfully',
            'subtitle' => 'The major and its associated data have been removed'
        ]);
    }

    public function deleteCourseView(Course $course)
    {
        $course->delete();

        return redirect('/settings/course')->with([
            'message' => 'Course deleted successfully',
            'subtitle' => 'The course and its associated data have been removed'
        ]);
    }

    public function deleteDepartmentView(Department $department)
    {
        Storage::delete('/public/departments/' . $department->logo);
        $department->delete();

        return redirect('/settings/department')->with([
            'message' => 'Department deleted successfully',
            'subtitle' => 'The department and all its related data have been removed from the system'
        ]);
    }

    public function userDelete(User $user)
    {
        $user->delete();

        return redirect('/account')->with([
            'message' => 'Account deleted!',
            'subtitle' => 'The user account and all associated data have been removed'
        ]);
    }

    public function userDeleteDepartment(User $user)
    {
        $user->delete();

        return back()->with([
            'message' => 'User deleted successfully',
            'subtitle' => 'The user account and all associated data have been removed'
        ]);
    }
}
