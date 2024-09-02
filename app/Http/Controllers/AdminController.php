<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function auditView() {
        return view('audit.index');
    }

    public function reportsGraphicalView()
    {
        return view('report.graphical');
    }

    public function reportsStatisticalView()
    {
        $users = User::query();

        $users = $users->paginate(5);

        return view('report.statistical')->with('users', $users);
    }

    public function myProfileView() {
        return view('admin.profile');
    }

    public function settingsView() {
        return view('settings.index');
    }

    public function departmentSettingsView() {
        $depts = Department::query();

        $depts = $depts->paginate(5);

        return view('settings.department')->with('departments', $depts);
    }

    public function accountsSettingsView() {
        return view('settings.account');
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
}
