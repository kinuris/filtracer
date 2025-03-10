<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\EducationRecord;
use App\Models\PersonalRecord;
use App\Models\Post;
use App\Models\ProfessionalRecord;
use App\Models\ProfessionalRecordAttachments;
use App\Models\ProfessionalRecordHardSkill;
use App\Models\ProfessionalRecordMethod;
use App\Models\ProfessionalRecordSoftSkill;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function dashboardView()
    {
        return view('alumni.dashboard');
    }

    public function alumniProfileView()
    {
        $user = Auth::user();

        return view('alumni.profile')
            ->with('user', $user);
    }

    public function settingsPasswordView()
    {
        return view('alumni.settings.password');
    }

    public function settingsPassword(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8'],
            'confirm_new_password' => ['required', 'same:new_password'],
        ]);

        if (!password_verify($validated['current_password'], $alumni->password)) {
            return back()->with('error', 'Old password is incorrect');
        }

        $alumni->update([
            'password' => bcrypt($validated['new_password']),
        ]);

        return back()->with('message', 'Password updated successfully');

    }

    public function settingsView()
    {
        return view('alumni.settings.index');
    }

    public function updateProfileView()
    {
        $user = Auth::user();

        return view('alumni.update-profile')
            ->with('user', $user);
    }

    public function addEducationRecord(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'school' => ['required'],
            'location' => ['required'],
            'type' => ['required'],
            'course' => ['required'],
            'major' => ['required'],
            'start' => ['required'],
            'end' => ['required'],
        ]);

        $validated['user_id'] = $alumni->id;
        $validated['school_location'] = $validated['location'];
        $validated['major_id'] = $validated['major'];
        $validated['course_id'] = $validated['course'];

        EducationRecord::query()->create($validated);

        return back()->with('message', 'Education record added successfully!');
    }

    public function updateEducationRecord(Request $request, EducationRecord $educ, User $alumni)
    {
        $validated = $request->validate([
            'school' => ['required'],
            'location' => ['required'],
            'degree_type' => ['required'],
            'course' => ['required'],
            'major' => ['required'],
            'start' => ['required'],
            'end' => ['nullable', 'after:start'],
        ]);

        $validated['user_id'] = $alumni->id;
        $validated['school_location'] = $validated['location'];
        $validated['major_id'] = $validated['major'];
        $validated['course_id'] = $validated['course'];

        $educ->update($validated);

        return back()->with('message', 'Education record updated successfully!');
    }

    public function uploadProfilePicture(Request $request, User $alumni)
    {
        $request->validate([
            'profile' => ['required', 'mimes:jpg,jpeg,png', 'max:10000']
        ]);

        $profile = $request->file('profile');

        Storage::delete('public/user/images/' . $alumni->personalBio->profile_picture);

        $filename = sha1(time() . $alumni->name) . '.' . $profile->getClientOriginalExtension();
        $profile->storePubliclyAs('public/user/images', $filename);

        $alumni->getPersonalBio()->update([
            'profile_picture' => $filename,
        ]);

        return redirect('/alumni/profile/update')->with('message', 'Profile picture uploaded successfully!');
    }

    public function createProfBio(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'employment_status' => ['required'],
            'employment_type1' => ['required'],
            'employment_type2' => ['required'],
            'industry' => ['required'],
            'job_title' => ['required'],
            'company' => ['required'],
            'monthly_salary' => ['required'],
            'work_location' => ['required'],
            'waiting_time' => ['required'],
            'hard_skills' => ['required', 'array', 'min:0'],
            'soft_skills' => ['required', 'array', 'min:0'],
            'methods' => ['required', 'array', 'min:0'],
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf']
        ]);

        $validated['company_name'] = $validated['company'];

        $prof = ProfessionalRecord::query()->create(array_merge([
            'user_id' => $alumni->id,
        ], $validated));

        foreach ($validated['hard_skills'] as $skill) {
            ProfessionalRecordHardSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        foreach ($validated['soft_skills'] as $skill) {
            ProfessionalRecordSoftSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        foreach ($validated['methods'] as $method) {
            ProfessionalRecordMethod::query()->create([
                'professional_record_id' => $prof->id,
                'method' => $method,
            ]);
        }

        return back()->with('message', 'Professional record created successfully');
    }

    public function updateProfBio(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'employment_status' => ['required'],
            'employment_type1' => ['required'],
            'employment_type2' => ['required'],
            'industry' => ['required'],
            'job_title' => ['required'],
            'company' => ['required'],
            'monthly_salary' => ['required'],
            'work_location' => ['required'],
            'waiting_time' => ['required'],
            'hard_skills' => ['nullable', 'array', 'min:1'],
            'soft_skills' => ['nullable', 'array', 'min:1'],
            'methods' => ['nullable', 'array', 'min:1'],
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf']
        ]);

        $validated['company_name'] = $validated['company'];

        $alumni->getProfessionalBio()->update($validated);
        $prof = $alumni->getProfessionalBio();

        if ($request->hasFile('certs')) {
            $prof->attachments()->delete();
            foreach ($request->file('certs') as $cert) {
                $ext = $cert->extension();
                $name = $cert->getClientOriginalName();
                $filename = sha1(time() . $cert->getClientOriginalName());

                $cert->storePubliclyAs('public/professional/attachments/', $filename . '.' . $ext);

                ProfessionalRecordAttachments::query()->create([
                    'professional_record_id' => $prof->id,
                    'type' => $cert->getClientMimeType(),
                    'name' => $name,
                    'link' => $filename . '.' . $ext,
                ]);
            }
        }

        $prof->hardSkills()->delete();
        foreach ($validated['hard_skills'] ?? [] as $skill) {
            ProfessionalRecordHardSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        $prof->softSkills()->delete();
        foreach ($validated['soft_skills'] ?? [] as $skill) {
            ProfessionalRecordSoftSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        $prof->methods()->delete();
        foreach ($validated['methods'] ?? [] as $method) {
            ProfessionalRecordMethod::query()->create([
                'professional_record_id' => $prof->id,
                'method' => $method,
            ]);
        }

        return back()->with('message', 'Professional record updated successfully');
    }

    public function setupPersonal(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'student_id' => ['required', 'unique:personal_records,student_id'],
            'gender' => ['required'],
            'civil_status' => ['required'],
            'birthdate' => ['required'],
            'permanent_address' => ['required'],
            'current_address' => ['required'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'unique:personal_records,phone_number'],
            'social_link' => ['nullable'],
        ]);

        $validated['email_address'] = $validated['email'];
        $validated['user_id'] = $alumni->id;


        PersonalRecord::query()->create($validated);

        return redirect('/alumni/setup/educational')->with('message', 'Personal record created successfully');
    }

    public function setupEducationalView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }

        if (Auth::user()->educationalBios()->exists()) {
            return redirect('/alumni/setup/professional');
        }

        return view('setup.educational-info');
    }

    public function setupEducational(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'school' => ['required'],
            'location' => ['required'],
            'degree_type' => ['required'],
            'course' => ['required'],
            'major' => ['required'],
            'start' => ['required'],
            'end' => ['nullable'],
        ]);

        $validated['school_location'] = $validated['location'];
        $validated['course_id'] = $validated['course'];
        $validated['major_id'] = $validated['major'];
        $validated['user_id'] = $alumni->id;

        EducationRecord::query()->create($validated);

        return redirect('/alumni/setup/professional')->with('message', 'Educational record created successfully');
    }

    public function setupProfessionalView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }

        if (!Auth::user()->educationalBios()->exists()) {
            return redirect('/alumni/setup/educational');
        }

        if (!is_null(Auth::user()->getProfessionalBio())) {
            return redirect('/alumni/setup/profilepic');
        }

        return view('setup.professional-info');
    }

    public function setupProfessional(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'employment_status' => ['required'],
            'employment_type1' => ['required'],
            'employment_type2' => ['required'],
            'industry' => ['required'],
            'job_title' => ['required'],
            'company' => ['required'],
            'monthly_salary' => ['required'],
            'work_location' => ['required'],
            'waiting_time' => ['required'],
            'hard_skills' => ['nullable', 'array', 'min:1'],
            'soft_skills' => ['nullable', 'array', 'min:1'],
            'methods' => ['nullable', 'array', 'min:1'],
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf']
        ]);

        $validated['company_name'] = $validated['company'];

        $prof = ProfessionalRecord::query()->create(array_merge([
            'user_id' => $alumni->id,
        ], $validated));

        foreach ($validated['hard_skills'] ?? [] as $skill) {
            ProfessionalRecordHardSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        foreach ($validated['soft_skills'] ?? [] as $skill) {
            ProfessionalRecordSoftSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => $skill,
            ]);
        }

        foreach ($validated['methods'] ?? [] as $method) {
            ProfessionalRecordMethod::query()->create([
                'professional_record_id' => $prof->id,
                'method' => $method,
            ]);
        }

        return redirect('/alumni/setup/profilepic')->with('message', 'Professional record created successfully');
    }

    public function setupProfilepicView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }

        if (!Auth::user()->educationalBios()->exists()) {
            return redirect('/alumni/setup/educational');
        }

        if (is_null(Auth::user()->getProfessionalBio())) {
            return redirect('/alumni/setup/professional');
        }

        return view('setup.profilepic');
    }

    public function setupProfilepic(Request $request, User $alumni)
    {
        $request->validate([
            'profile_picture' => ['nullable', 'mimes:jpg,jpeg,png'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = sha1(time() . $file->getClientOriginalName()) . '.' . $file->extension();
            $file->storePubliclyAs('public/user/images', $filename);

            $alumni->personalBio->update([
                'profile_picture' => $filename,
            ]);
        }

        $content = Auth::user()->name . ' alumni account has been completed';
        $action = '/user/view/' . $alumni->id;

        foreach (User::query()->where('role', '=', 'Admin')->get() as $admin) {
            UserAlert::query()->create([
                'title' => 'Alumni Account Completed',
                'content' => $content,
                'action' => $action,
                'user_id' => $admin->id,
            ]);
        }

        return redirect('/alumni')->with('message', 'Profile picture updated successfully');
    }

    public function updatePersonalProfile(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'student_id' => ['required'],
            'gender' => ['required'],
            'civil_status' => ['required'],
            'birthdate' => ['required'],
            'permanent_address' => ['required'],
            'current_address' => ['required'],
            'email_address' => ['required', 'email'],
            'username' => ['required'],
            'phone_number' => ['required'],
            'social_media' => ['required'],
        ]);

        $alumni->update($validated);
        $alumni->personalBio->update($validated);

        return back()->with('message', 'Personal profile updated successfully');
    }

    public function setupView()
    {
        return view('setup.index');
    }

    public function setupPersonalView()
    {
        if (!is_null(Auth::user()->personalBio)) {
            return redirect('/alumni/setup/educational');
        }

        return view('setup.personal-info');
    }

    public function chatView()
    {
        $user = User::query()->find(Auth::user()->id);
        $view = view('chat.alumni');

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

        $posts = $posts->latest()->get();

        return view('post.alumni')->with('posts', $posts);
    }
}
