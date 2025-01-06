<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\Post;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function userView(User $user)
    {
        return view('alumni.view')
            ->with('dept', Department::query()->find($user->department_id))
            ->with('user', $user);
    }

    public function auditView()
    {
        $audits = Audit::query();

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
        $users = User::compSet()->where('role', '=', 'Alumni')->orderBy('created_at', 'DESC');

        $users = $users->paginate(5);

        return view('report.statistical')->with('users', $users);
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
            Storage::delete('public/user/images/' . $user->admin()->profile_picture);
        } else {
            Storage::delete('public/user/images/' . $user->personalBio->profile_picture);
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
        $users = User::query();

        if (request('mode') === 'generated') {
            $users = User::has('adminGenerated');
        }

        $role = request()->query('user_role', 'Alumni');
        if ($role === 'Alumni' && request('mode') !== 'generated') {
            $users = User::compSet()->where('role', '=', 'Alumni');
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

        return view('account.index')->with('users', $users);
    }

    public function alumniListView(Request $request, Department $department)
    {
        $users = User::hasBio('educational');

        $course = (int) $request->query('course');
        if ($course && $course !== -1) {
            $users = User::isCourse($request->query('course'));

            dd($users->get());
        }

        $search = $request->query('search');
        if ($search) {
            $users = $users->where('name', 'LIKE', '%' . $search . '%');
        }

        $users = $users
            ->where('department_id', '=', $department->id)
            ->where('role', '=', 'Alumni')
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
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'suffix' => ['nullable'],
            'position' => ['required'],
            'department' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
        ]);

        $validated['department_id'] = $validated['department'];
        $validated['email_address'] = $validated['email'];
        $validated['phone_number'] = $validated['phone'];
        $validated['position_id'] = $validated['position'];

        $user->update($validated);
        $user->admin()->update($validated);

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

        return redirect('/account')->with('message', 'User VERIFIED successfully');
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

        return redirect('/account')->with('message', 'User UNVERIFIED successfully');
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

    public function chatView()
    {
        $user = User::query()->find(Auth::user()->id);
        $view = view('chat.admin');

        $selected = request('initiate');

        if ($selected && is_numeric($selected)) {
            $selected = User::query()->find($selected);
        } else {
            $selected = ChatGroup::query()->where('internal_id', '=', urldecode(request('initiate')))->first();
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

        return redirect('/settings/major')->with('message', 'Major created successfully');
    }

    public function postView()
    {
        $category = request('category', 'All Posts');

        $posts = Post::query();

        if ($category === 'Events') {
            $posts = $posts->where('post_category', 'Event');
        } else if ($category === 'Job Openings') {
            $posts = $posts->where('post_category', 'Job Opening');
        } else if ($category === 'Announcements') {
            $posts = $posts->where('post_category', 'Announcement');
        } else if ($category === 'Your Posts') {
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

        return redirect('/settings/major')->with('message', 'Major deleted successfully');
    }

    public function deleteCourseView(Course $course)
    {
        $course->delete();

        return redirect('/settings/course')->with('message', 'Course deleted successfully');
    }

    public function deleteDepartmentView(Department $department)
    {
        $department->delete();

        return redirect('/settings/department')->with('message', 'Department deleted successfully');
    }

    public function userDelete(User $user)
    {
        $user->delete();

        return redirect('/account')->with('message', 'User deleted successfully');
    }
}
