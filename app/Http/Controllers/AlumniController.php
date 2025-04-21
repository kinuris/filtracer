<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSAsyncJob;
use App\Models\BindingRequest;
use App\Models\BoundAccount;
use App\Models\ChatGroup;
use App\Models\EducationRecord;
use App\Models\PersonalRecord;
use App\Models\Post;
use App\Models\PrimarySecondaryEducation;
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
use Illuminate\Support\Facades\Validator;

class AlumniController extends Controller
{
    public function dashboardView()
    {
        return view('alumni.dashboard');
    }

    public function settingsDisplayView()
    {
        return view('alumni.settings.display');
    }

    public function deleteAttachment(Request $request, ProfessionalRecordAttachments $attachment)
    {
        $attachment->delete();

        return back()
            ->with('message', 'Attachment deleted successfully')
            ->with('subtitle', 'The attachment has been removed from your profile.');
    }

    public function resetAlumniPassword(Request $request, User $alumni)
    {
        $validator = Validator::make($request->all(), [
            'current' => ['required'],
            'new' => ['required', 'min:8'],
            'confirm' => ['required', 'same:new'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('failed_message', 'Failed to reset password. Please check your inputs.')
                ->with('failed_subtitle', 'Please ensure that all fields are filled correctly.');
        }

        $validated = $validator->validated();

        if (!password_verify($validated['current'], $alumni->password)) {
            return back()->with('error', 'Old password is incorrect');
        }

        $alumni->update([
            'password' => bcrypt($validated['new']),
        ]);

        return back()
            ->with('message', 'Password updated successfully')
            ->with('subtitle', 'Your account is now secured with your new password.');
    }

    public function denyBinding(BindingRequest $binding)
    {
        $binding->is_denied = true;
        $binding->save();

        $adminName = $binding->admin->admin()->fullname;

        return back()
            ->with('message', 'Binding request from ' . $adminName . ' denied.')
            ->with('subtitle', 'The admin will be notified that you have rejected their request to connect.');
    }

    public function acceptBinding(BindingRequest $binding)
    {
        $bound = $binding->toBoundAccount();

        return back()
            ->with('message', 'Successfully connected with ' . $bound->admin->admin()->fullname)
            ->with('subtitle', 'You can now communicate and share information with this admin.');
    }

    public function alumniProfileView()
    {
        $user = Auth::user();

        return view('alumni.profile')
            ->with('user', $user);
    }

    public function updatePrimarySecondary(Request $request, PrimarySecondaryEducation $primsec, User $alumni)
    {
        // Basic validation for all records
        $validated = $request->validate([
            'type' => ['required'],
            'school_name' => ['required'],
            'location' => ['required'],
            'start' => ['required'],
            'end' => ['required'],
        ]);

        // If secondary education, validate strand
        if ($validated['type'] === 'secondary') {
            $strandValidation = $request->validate([
                'strand' => ['required'],
            ]);

            $validated['strand'] = $strandValidation['strand'];
        }

        foreach ($validated as $key => $value) {
            $primsec->$key = $value;
        }
        $primsec->save();

        return back()
            ->with('message', 'Education record updated successfully')
            ->with('subtitle', 'Your educational information has been saved to your profile.');
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

        return back()
            ->with('message', 'Password updated successfully')
            ->with('subtitle', 'Your account is now secured with your new password.');
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
            'level' => ['required'],
        ]);

        if ($validated['level'] === 'Primary') {
            $validated = $request->validate([
                'school' => ['required'],
                'location' => ['required'],
                'start' => ['required'],
                'end' => ['required']
            ]);

            $record = new PrimarySecondaryEducation();

            $record->user_id = $alumni->id;
            $record->type = 'primary';
            $record->school_name = $validated['school'];
            $record->location = $validated['location'];
            $record->start = $validated['start'];
            $record->end = $validated['end'];

            $record->save();

            return back()
                ->with('message', 'Education record added successfully!')
                ->with('subtitle', 'Your primary education information has been saved to your profile.');
        } else if ($validated['level'] === 'Secondary') {
            $validated = $request->validate([
                'track' => ['required'],
                'school' => ['required'],
                'location' => ['required'],
                'start' => ['required'],
                'end' => ['required']
            ]);

            $record = new PrimarySecondaryEducation();

            $record->user_id = $alumni->id;
            $record->type = 'secondary';
            $record->strand = $validated['track'];
            $record->school_name = $validated['school'];
            $record->location = $validated['location'];
            $record->start = $validated['start'];
            $record->end = $validated['end'];

            $record->save();

            return back()
                ->with('message', 'Education record added successfully!')
                ->with('subtitle', 'Your secondary education information has been saved to your profile.');
        }

        $validated = $request->validate([
            'school' => ['required'],
            'location' => ['required'],
            'type' => ['required'],
            'course' => ['required'],
            'major' => ['nullable'],
            'start' => ['required'],
            'end' => ['required'],
        ]);

        $validated['user_id'] = $alumni->id;
        $validated['school_location'] = $validated['location'];
        $validated['major_id'] = isset($validated['major']) ? $validated['major'] : null;
        $validated['course_id'] = $validated['course'];

        EducationRecord::query()->create($validated);

        return back()
            ->with('message', 'Education record added successfully!')
            ->with('subtitle', 'Your tertiary education information has been saved to your profile.');
    }

    public function updateEducationRecord(Request $request, EducationRecord $educ, User $alumni)
    {
        $validated = $request->validate([
            'school' => ['required'],
            'location' => ['required'],
            'degree_type' => ['required'],
            'course' => ['required'],
            'major' => ['nullable'],
            'start' => ['required'],
            'end' => ['nullable', 'after:start'],
        ]);

        $validated['user_id'] = $alumni->id;
        $validated['school_location'] = $validated['location'];
        $validated['major_id'] = isset($validated['major']) ? $validated['major'] : null;
        $validated['course_id'] = $validated['course'];

        $educ->update($validated);

        // Get previous URL and parse it
        $previousUrl = url()->previous();
        $urlComponents = parse_url($previousUrl);
        $queryParams = [];
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParams);
        }

        // Define prefixes to remove
        $unwantedPrefixes = ['school_', 'course_', 'degree_type_'];

        // Filter out parameters starting with the unwanted prefixes
        $filteredParams = [];
        foreach ($queryParams as $key => $value) {
            $remove = false;
            foreach ($unwantedPrefixes as $prefix) {
                if (\Illuminate\Support\Str::startsWith($key, $prefix)) {
                    $remove = true;
                    break;
                }
            }
            if (!$remove) {
                $filteredParams[$key] = $value;
            }
        }

        // Rebuild the URL
        $newQueryString = http_build_query($filteredParams);
        $newUrl = ($urlComponents['scheme'] ?? 'http') . '://' . ($urlComponents['host'] ?? '');
        if (isset($urlComponents['port'])) {
            $newUrl .= ':' . $urlComponents['port'];
        }
        $newUrl .= ($urlComponents['path'] ?? '/');
        if (!empty($newQueryString)) {
            $newUrl .= '?' . $newQueryString;
        }

        // Redirect to the cleaned URL with flash messages
        return redirect($newUrl)
            ->with('message', 'Education record updated successfully!')
            ->with('subtitle', 'Your tertiary education information has been updated in your profile.');
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

        return redirect('/alumni/profile/update')
            ->with('message', 'Profile picture uploaded successfully!')
            ->with('subtitle', 'Your new profile picture has been saved and is now visible to others.');
    }

    public function createProfBio(Request $request, User $alumni)
    {
        // Validation rules adjusted based on the view and JS logic
        $validated = $request->validate([
            'employment_status' => ['required', 'string'],
            'employment_type1' => ['required', 'string'], // JS sets 'Not Applicable' if needed
            'employment_type2' => ['required', 'string'], // JS sets 'Not Applicable' if needed
            'industry' => ['required', 'string'],         // JS sets 'Not Applicable' if needed
            'job_title' => ['required', 'string'],        // JS sets 'N/A' if needed
            'company_name' => ['required', 'string'],     // Field name corrected from 'company', JS sets 'N/A' if needed
            'monthly_salary' => ['required', 'string'],   // JS sets 'No Income'/'Not Applicable' if needed
            'work_location' => ['required', 'string'],    // JS sets 'N/A' if needed
            'waiting_time' => ['required', 'string'],
            'hard_skills' => ['nullable', 'array'], // Changed from required
            'soft_skills' => ['nullable', 'array'], // Changed from required
            'methods' => ['nullable', 'array'],     // Changed from required
            'certs' => ['nullable', 'array'],       // Added for file uploads
            'certs.*' => ['nullable', 'mimes:pdf', 'max:5120'] // Added PDF validation with size limit (5MB)
        ]);

        // Create the main professional record
        // Note: 'company_name' is already validated with the correct key
        $prof = ProfessionalRecord::query()->create(array_merge(
            ['user_id' => $alumni->id],
            $validated // Pass all validated data directly
        ));

        // Handle skills and methods using validated arrays, checking for null and trimming
        foreach ($validated['hard_skills'] ?? [] as $skill) {
            if ($skill) { // Ensure skill is not empty/null
                ProfessionalRecordHardSkill::query()->create([
                    'professional_record_id' => $prof->id,
                    'skill' => trim($skill),
                ]);
            }
        }

        foreach ($validated['soft_skills'] ?? [] as $skill) {
            if ($skill) { // Ensure skill is not empty/null
                ProfessionalRecordSoftSkill::query()->create([
                    'professional_record_id' => $prof->id,
                    'skill' => trim($skill),
                ]);
            }
        }

        foreach ($validated['methods'] ?? [] as $method) {
            if ($method) { // Ensure method is not empty/null
                ProfessionalRecordMethod::query()->create([
                    'professional_record_id' => $prof->id,
                    'method' => trim($method),
                ]);
            }
        }

        // Handle certifications file uploads
        if ($request->hasFile('certs')) {
            foreach ($request->file('certs') as $cert) {
                if ($cert->isValid()) { // Check if file is valid
                    $ext = $cert->extension();
                    $name = $cert->getClientOriginalName();
                    // Generate a more unique filename
                    $filename = sha1(time() . '_' . $cert->getClientOriginalName()) . '.' . $ext;
                    // Store the file
                    $path = $cert->storePubliclyAs('public/professional/attachments', $filename);

                    if ($path) { // Check if storage was successful
                        ProfessionalRecordAttachments::query()->create([
                            'professional_record_id' => $prof->id,
                            'type' => $cert->getClientMimeType(),
                            'name' => $name,
                            'link' => $filename, // Store only the filename, path is known
                        ]);
                    } else {
                        // Optional: Log error or return with specific file upload error
                    }
                }
            }
        }

        // Redirect back to the previous page (likely the form page)
        return back()
            ->with('message', 'Professional record created successfully')
            ->with('subtitle', 'Your professional information has been saved to your profile.');
    }

    public function updateProfBio(Request $request, ProfessionalRecord $record, User $alumni)
    {
        $validated = $request->validate([
            'employment_status' => ['required'],
            'employment_type1' => ['required'],
            'employment_type2' => ['required'],
            'industry' => ['required'],
            'job_title' => ['required'],
            'company_name' => ['required'],
            'monthly_salary' => ['required'],
            'work_location' => ['required'],
            'waiting_time' => ['required'],
            'hard_skills' => ['nullable', 'array', 'min:1'],
            'soft_skills' => ['nullable', 'array', 'min:1'],
            'methods' => ['nullable', 'array', 'min:1'],
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf']
        ]);

        $prof = $record;
        $prof->update($validated);

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

        return back()
            ->with('message', 'Professional record updated successfully')
            ->with('subtitle', 'Your professional information has been updated in your profile.');
    }

    public function setupPersonal(Request $request, User $alumni)
    {
        $existingRecord = $alumni->personalBio;

        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            // Ignore current record's student_id if updating
            'student_id' => ['nullable', 'unique:personal_records,student_id' . ($existingRecord ? ',' . $existingRecord->id : '')],
            'gender' => ['required'],
            'civil_status' => ['required'],
            'birthdate' => ['required', 'date'],
            'permanent_address' => ['required'],
            'current_address' => ['required'],
            'email' => ['required', 'email'],
            'phone_number' => ['required'],
            'social_link' => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['email_address'] = $validated['email'];
        $validated['user_id'] = $alumni->id;

        if ($existingRecord) {
            $existingRecord->update($validated);
            $message = 'Personal record updated successfully';
            $subtitle = 'Your personal information has been updated. Proceeding to education details.';
        } else {
            PersonalRecord::query()->create($validated);
            $message = 'Personal record created successfully';
            $subtitle = 'Your personal information has been saved. Now let\'s set up your education details.';
        }

        return redirect('/alumni/setup/educational')
            ->with('message', $message)
            ->with('subtitle', $subtitle);
    }

    public function setupEducationalView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }
        // Fetch the first educational record for setup purposes
        $educationalRecord = Auth::user()->educationalBios()->first();
        return view('setup.educational-info')->with('educationalRecord', $educationalRecord);
    }

    public function setupEducational(Request $request, User $alumni)
    {
        $existingRecord = $alumni->educationalBios()->first(); // Assuming one record during setup

        $validator = Validator::make($request->all(), [
            'school' => ['required'],
            'location' => ['required'],
            'degree_type' => ['required'],
            'course' => ['required'],
            'major' => ['nullable'], // Major might not exist for all courses
            'start' => ['required', 'digits:4', 'integer', 'min:1900'],
            'end' => ['nullable', 'digits:4', 'integer', 'min:1900', 'gte:start'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['school_location'] = $validated['location'];
        $validated['course_id'] = $validated['course'];
        $validated['major_id'] = $validated['major'] ?? null; // Use null if major is not provided
        $validated['user_id'] = $alumni->id;

        if ($existingRecord) {
            $existingRecord->update($validated);
            $message = 'Educational record updated successfully';
            $subtitle = 'Your educational information has been updated. Proceeding to professional details.';
        } else {
            EducationRecord::query()->create($validated);
            $message = 'Educational record created successfully';
            $subtitle = 'Your educational information has been saved. Now let\'s set up your professional details.';
        }

        return redirect('/alumni/setup/professional')
            ->with('message', $message)
            ->with('subtitle', $subtitle);
    }

    public function setupProfessionalView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }

        if (!Auth::user()->educationalBios()->exists()) {
            return redirect('/alumni/setup/educational');
        }

        $professionalRecord = Auth::user()->getProfessionalBio(); // Fetches the single professional record
        return view('setup.professional-info')->with('professionalRecord', $professionalRecord);
    }

    public function setupProfessional(Request $request, User $alumni)
    {
        $existingRecord = $alumni->getProfessionalBio();

        $validator = Validator::make($request->all(), [
            'employment_status' => ['required', 'string'],
            'employment_type1' => ['required', 'string'],
            'employment_type2' => ['required', 'string'],
            'industry' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'company_name' => ['required', 'string'], // Assuming 'company' is the input name
            'monthly_salary' => ['required', 'string'],
            'work_location' => ['required', 'string'],
            'waiting_time' => ['required', 'string'],
            'hard_skills' => ['nullable', 'array'],
            'soft_skills' => ['nullable', 'array'],
            'methods' => ['nullable', 'array'],
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf', 'max:5120'] // 5MB limit per file
        ]);

         if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        unset($validated['company']); // Remove original key if different

        if ($existingRecord) {
            // Update existing record
            $prof = $existingRecord;
            $prof->update($validated);

            // Clear existing related data before adding new ones
            $prof->hardSkills()->delete();
            $prof->softSkills()->delete();
            $prof->methods()->delete();
            // Optionally handle attachments: delete old ones if new ones are uploaded?
            // If new certs are uploaded, delete old ones first
            if ($request->hasFile('certs')) {
                 // Delete old attachments from storage and DB
                foreach ($prof->attachments as $attachment) {
                    Storage::delete('public/professional/attachments/' . $attachment->link);
                    $attachment->delete();
                }
            }

            $message = 'Professional record updated successfully';
            $subtitle = 'Your professional information has been updated. Finally, let\'s add your profile picture.';

        } else {
            // Create new record
            $prof = ProfessionalRecord::query()->create(array_merge(
                ['user_id' => $alumni->id],
                $validated
            ));
            $message = 'Professional record created successfully';
            $subtitle = 'Your professional information has been saved. Finally, let\'s add your profile picture.';
        }

        // Add/Re-add skills and methods
        foreach ($validated['hard_skills'] ?? [] as $skill) {
            if ($skill) ProfessionalRecordHardSkill::query()->create(['professional_record_id' => $prof->id, 'skill' => trim($skill)]);
        }
        foreach ($validated['soft_skills'] ?? [] as $skill) {
            if ($skill) ProfessionalRecordSoftSkill::query()->create(['professional_record_id' => $prof->id, 'skill' => trim($skill)]);
        }
        foreach ($validated['methods'] ?? [] as $method) {
            if ($method) ProfessionalRecordMethod::query()->create(['professional_record_id' => $prof->id, 'method' => trim($method)]);
        }

        // Handle file uploads (for both create and update if new files are present)
        if ($request->hasFile('certs')) {
            foreach ($request->file('certs') as $cert) {
                if ($cert->isValid()) {
                    $ext = $cert->extension();
                    $name = $cert->getClientOriginalName();
                    $filename = sha1(time() . '_' . $cert->getClientOriginalName()) . '.' . $ext;
                    $path = $cert->storePubliclyAs('public/professional/attachments', $filename);

                    if ($path) {
                        ProfessionalRecordAttachments::query()->create([
                            'professional_record_id' => $prof->id,
                            'type' => $cert->getClientMimeType(),
                            'name' => $name,
                            'link' => $filename,
                        ]);
                    }
                }
            }
        }

        return redirect('/alumni/setup/profilepic')
            ->with('message', $message)
            ->with('subtitle', $subtitle);
    }

    public function deleteProfbio(Request $request, ProfessionalRecord $record)
    {
        $record->delete();

        return redirect('/alumni/profile/update?type=professional')
            ->with('message', 'Professional record deleted successfully')
            ->with('subtitle', 'Your professional information has been removed from your profile.');
    }

    public function setupProfilepicView()
    {
        if (Auth::user()->personalBio === null) {
            return redirect('/alumni/setup/personal');
        }

        if (!User::query()->find(Auth::user()->id)->educationalBios()->exists()) {
            return redirect('/alumni/setup/educational');
        }

        if (is_null(User::query()->find(Auth::user()->id)->getProfessionalBio())) {
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
        } else {
            $alumni->personalBio->update([
                'profile_picture' => 'alumni-profile.png',
            ]);
        }

        $content = Auth::user()->name . ' alumni account has been completed';
        $action = '/user/view/' . $alumni->id;

        $content = <<<TEXT
            🎉 Account Complete!
            Your FilTracer alumni account setup is complete! An admin will verify your details soon.
            TEXT;

        SendSMSAsyncJob::dispatch(
            $alumni->personalBio->philSMSNum(),
            $content
        );

        foreach (User::query()->where('role', '=', 'Admin')->get() as $admin) {
            UserAlert::query()->create([
                'title' => 'Alumni Account Completed',
                'content' => $content,
                'action' => $action,
                'user_id' => $admin->id,
            ]);
        }

        Auth::logout();

        return redirect('/login')
            ->with('message', 'Profile setup completed successfully')
            ->with('subtitle', 'Your alumni profile is now complete. You can login to access your account.');
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

        return back()
            ->with('message', 'Personal profile updated successfully')
            ->with('subtitle', 'Your personal information has been updated in your profile.');
    }

    public function setupView()
    {
        return view('setup.index');
    }

    public function setupPersonalView()
    {
        $personalRecord = Auth::user()->personalBio;
        return view('setup.personal-info')->with('personalRecord', $personalRecord);
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

    public function createProfessionalRecordView(User $alumni)
    {
        return view('alumni.professional-record-create')->with('user', $alumni);
    }

    public function storeProfessionalRecord(Request $request, User $alumni)
    {
        $validated = $request->validate([
            'employment_status' => ['required'],
            'employment_type1' => ['required'],
            'employment_type2' => ['required'],
            'industry' => ['required'],
            'job_title' => ['required'],
            'company_name' => ['required'],
            'monthly_salary' => ['required'],
            'work_location' => ['required'],
            'waiting_time' => ['required'],
            'hard_skills' => ['nullable', 'array'], // Validate as array
            'soft_skills' => ['nullable', 'array'], // Validate as array
            'methods' => ['nullable', 'array'],     // Validate as array
            'certs' => ['nullable', 'array'],
            'certs.*' => ['nullable', 'mimes:pdf']
        ]);

        $prof =
            \App\Models\ProfessionalRecord::query()->create(array_merge([
                'user_id' => $alumni->id,
            ], $validated));

        // Handle skills and methods using validated arrays
        foreach ($validated['hard_skills'] ?? [] as $skill) {
            if ($skill) \App\Models\ProfessionalRecordHardSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => trim($skill), // Trim potential whitespace
            ]);
        }
        foreach ($validated['soft_skills'] ?? [] as $skill) {
            if ($skill) \App\Models\ProfessionalRecordSoftSkill::query()->create([
                'professional_record_id' => $prof->id,
                'skill' => trim($skill), // Trim potential whitespace
            ]);
        }
        foreach ($validated['methods'] ?? [] as $method) {
            if ($method) \App\Models\ProfessionalRecordMethod::query()->create([
                'professional_record_id' => $prof->id,
                'method' => trim($method), // Trim potential whitespace
            ]);
        }
        // Handle certifications
        if ($request->hasFile('certs')) {
            foreach ($request->file('certs') as $cert) {
                $ext = $cert->extension();
                $name = $cert->getClientOriginalName();
                $filename = sha1(time() . $cert->getClientOriginalName());
                $cert->storePubliclyAs('public/professional/attachments/', $filename . '.' . $ext);
                \App\Models\ProfessionalRecordAttachments::query()->create([
                    'professional_record_id' => $prof->id,
                    'type' => $cert->getClientMimeType(),
                    'name' => $name,
                    'link' => $filename . '.' . $ext,
                ]);
            }
        }
        return redirect('/alumni/profile/update?type=professional')
            ->with('message', 'Professional record added successfully!')
            ->with('subtitle', 'Your professional information has been saved to your profile.');
    }
}
