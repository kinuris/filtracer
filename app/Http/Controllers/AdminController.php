<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboardView()
    {
        return view('admin.dashbaord');
    }

    public function departmentView()
    {
        return view('department.index');
    }

    public function userView(User $user)
    {
        return view('alumni.view')
            ->with('dept', Department::query()->find($user->department_id))
            ->with('user', $user);
    }

    public function auditView()
    {
        return view('audit.index');
    }

    public function reportsGraphicalView()
    {
        return view('report.graphical');
    }

    public function reportsStatisticalView()
    {
        $users = User::query()->where('role', '=', 'Alumni')->orderBy('created_at', 'DESC');

        $users = $users->paginate(5);

        return view('report.statistical')->with('users', $users);
    }

    public function reportsStatisticalGenerateView()
    {
        return view('report.statistical-generate');
    }

    public function uploadProfilePicture(Request $request, User $admin)
    {
        $request->validate([
            'profile' => ['required', 'mimes:jpg,jpeg,png', 'max:10000']
        ]);

        $profile = $request->file('profile');
        Storage::delete('public/user/images/' . $admin->personalBio->profile_picture);

        $filename = sha1(time() . $admin->name) . '.' . $profile->getClientOriginalExtension();
        $profile->storePubliclyAs('public/user/images', $filename);

        $admin->getPersonalBio()->update([
            'profile_picture' => $filename,
        ]);

        return redirect('/settings/account')->with('message', 'Profile picture uploaded successfully!');
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
        $users = User::hasBio('personal');

        $search = request()->query('search');
        if ($search) {
            $users = $users->where('name', 'LIKE', '%' . $search . '%');
        }

        $status = (int) request()->query('user_status', -1);
        if ($status !== -1) {
            $users = $users->whereHas('personalBio', function ($query) {
                $query->where('status', '=', request()->query('user_status'));
            });
        }

        $users = $users->paginate(6);

        return view('account.index')->with('users', $users);
    }

    public function alumniListView(Request $request, Department $department)
    {
        $users = User::hasBio('educational');

        $course = (int) $request->query('course');
        if ($course && $course !== -1) {
            $users = User::isCourse($request->query('course'));
        }

        $search = $request->query('search');
        if ($search) {
            $users = $users->where('name', 'LIKE', '%' . $search . '%');
        }

        $users = $users
            ->where('department_id', '=', $department->id)
            ->paginate(5);
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

        return redirect('/settings/department')->with('message', 'Department created successfully');
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

        return redirect('/settings/major')->with('message', 'Major updated successfully');
    }

    public function editCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'department' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];

        $course->update($validated);

        return redirect('/settings/course')->with('message', 'Course updated successfully');
    }

    public function accountEdit(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'position' => ['required'],
            'department' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];
        $validated['email_address'] = $validated['email'];
        $validated['phone_number'] = $validated['phone'];

        $user->update($validated);
        $user->personalBio->update($validated);

        return redirect('/settings/account')->with('message', 'Account updated successfully');
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

        return redirect('/settings/department')->with('message', 'Department updated successfully');
    }

    public function delelteDepartment(Department $department)
    {
        Storage::delete('/public/departments/' . $department->logo);
        $department->delete();

        return redirect('/settings/department')->with('message', 'Department deleted successfully');
    }

    public function verifyUser(User $user)
    {
        $user->getPersonalBio()->update(['status' => 1]);

        return redirect('/account')->with('message', 'User verified successfully');
    }

    public function createCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:courses'],
            'department' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];

        Course::query()->create($validated);

        return redirect('/settings/course')->with('message', 'Course created successfully');
    }
}
