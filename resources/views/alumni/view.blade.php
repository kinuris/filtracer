@extends('layouts.admin')

@section('title', 'Profile Details')

@section('content')
@if($user->isCompSet())
@include('components.manage-account-modal')
@endif
@php
$query = request()->query('type') ?? 'personal';
$isEditMode = request()->query('edit') === 'true';
@endphp
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Profile Details</h1>
    <p class="text-gray-400 text-xs mb-2">Department / {{ $dept->name }} / <span class="text-blue-500">{{ $user->name }}</span></p>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
            <div class="flex flex-col">
                <div class="flex place-items-center">
                    <p class="text-lg">{{ $user->name }}</p>
                </div>
                @if ($user->isCompSet())
                <p class="text-gray-400 text-sm">{{ $user->getEducationalBio()->getCourse()->name }}</p>
                @else
                <div class="flex gap-3">
                    <p class="text-gray-400 text-sm">Incomplete Setup (Cannot Verify/Unverify)</p>
                    <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50">
                        <div class="absolute inset-0 bg-black opacity-60 transition-opacity"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all">
                                <div class="border-b px-6 py-4 flex items-center">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                                </div>
                                <div class="px-6 py-4">
                                    <p class="text-gray-600">Are you sure you want to delete this alumni record? This action cannot be undone.</p>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                    <button onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                                        Cancel
                                    </button>
                                    <a href="/user/delete/{{ $user->id }}" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                                        Delete Record
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex-1"></div>

            {{-- Action Buttons --}}
            <div class="flex items-center space-x-2">
                @if($user->isCompSet())
                <a class="rounded-lg p-2 px-3 bg-blue-600 text-white hover:bg-blue-700 transition-colors" href="/profile/report/{{ $user->id }}">Generate Report</a>
                @if($isEditMode)
                <button type="submit" form="profileForm" class="rounded-lg p-2 px-3 bg-green-600 text-white hover:bg-green-700 transition-colors">Save Changes</button>
                <a class="rounded-lg p-2 px-3 bg-gray-500 text-white hover:bg-gray-600 transition-colors" href="{{ request()->url() }}?type={{ $query }}">Cancel</a>
                @else
                    {{-- Only show Edit Profile if type is 'personal' --}}
                    @if($query === 'personal')
                    <a class="rounded-lg p-2 px-3 bg-yellow-500 text-white hover:bg-yellow-600 transition-colors" href="{{ request()->fullUrlWithQuery(['edit' => 'true']) }}">Edit Profile</a>
                    @endif
                @endif
                @endif

                @if (Auth::user()->admin()->is_super && !$isEditMode) {{-- Hide Manage Account in edit mode for simplicity --}}
                @endif

                @if ($user->isCompSet() && !$isEditMode) {{-- Hide Message in edit mode --}}
                <a class="rounded-lg p-2 ml-2 px-3 bg-blue-600 text-white hover:bg-blue-700 transition-colors" href="/admin/chat?initiate={{ $user->id }}&override=1">Message</a>
                @endif
            </div>
            {{-- End Action Buttons --}}
        </div>
    </div>

    {{-- Form Wrapper --}}
    <form id="profileForm" method="POST" action="{{ route('admin.alumni.profile.update', [$dept->id, $user->id]) }}" class="shadow rounded-lg mt-4 box-border h-full max-h-full overflow-auto">
        @csrf
        <div class="bg-white py-5 flex flex-col px-7 border-b rounded-lg min-h-full shadow-sm">
            <div class="flex mb-5 border-b sticky top-0 bg-white z-10 py-2"> {{-- Made tabs sticky --}}
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'personal') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="{{ request()->fullUrlWithQuery(['type' => 'personal', 'edit' => $isEditMode ? 'true' : null]) }}">Basic Info</a>
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'educational') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="{{ request()->fullUrlWithQuery(['type' => 'educational', 'edit' => $isEditMode ? 'true' : null]) }}">Educational Info</a>
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'professional') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="{{ request()->fullUrlWithQuery(['type' => 'professional', 'edit' => $isEditMode ? 'true' : null]) }}">Professional Info</a>
            </div>
            @php
            function renderField($label, $name, $value, $isEditMode, $type = 'text', $options = [], $colspan = 1) {
            $isEmpty = empty($value);
            $colspanClass = match((int)$colspan) {
            2 => 'md:col-span-2',
            3 => 'md:col-span-3',
            4 => 'md:col-span-4',
            default => '',
            };
            echo '<div class="flex flex-col h-fit ' . $colspanClass . '">';
                echo '<label for="' . $name . '" class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">' . $label . '</label>';
                if ($isEditMode) {
                if ($type === 'text' || $type === 'email' || $type === 'tel' || $type === 'date' || $type === 'url') {
                echo '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . old($name, $value) . '" class="border p-2 rounded-md bg-white text-gray-800 shadow-inner focus:ring-blue-500 focus:border-blue-500">';
                } elseif ($type === 'select') {
                echo '<select id="' . $name . '" name="' . $name . '" class="border p-2 rounded-md bg-white text-gray-800 shadow-inner focus:ring-blue-500 focus:border-blue-500">';
                    echo '<option value="">Select...</option>';
                    foreach ($options as $optionValue => $optionLabel) {
                    $selected = old($name, $value) == $optionValue ? 'selected' : '';
                    echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionLabel . '</option>';
                    }
                    echo '</select>';
                }
                } else {
                if ($type === 'url' && !$isEmpty) {
                echo '<p class="border p-2 rounded-md bg-gray-50 text-gray-800 shadow-inner line-clamp-1 overflow-auto"><a class="underline text-blue-600 hover:text-blue-800" href="' . $value . '" target="_blank" rel="noopener noreferrer">' . $value . '</a></p>';
                } else {
                echo '<p class="border p-2 rounded-md ' . ($isEmpty ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800') . ' shadow-inner line-clamp-1 overflow-auto">' . (!$isEmpty ? $value : 'None') . '</p>';
                }
                }
                echo '</div>';
            }
            @endphp

            {{-- Personal Info Section --}}
            @if ($query === 'personal')
            @php $personal = $user->getPersonalBio() ?? $user->partialPersonal; @endphp
            @if ($personal !== null)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 h-full">
                {{-- Helper function for input/display --}}


                {{ renderField('First Name', 'first_name', $personal->first_name ?? null, $isEditMode) }}
                {{ renderField('Middle Name', 'middle_name', $personal->middle_name ?? null, $isEditMode) }}
                {{ renderField('Last Name', 'last_name', $personal->last_name ?? null, $isEditMode) }}
                {{ renderField('Suffix', 'suffix', $personal->suffix ?? null, $isEditMode) }}
                {{ renderField('Age', 'age_display', ($user->getPersonalBio() !== null && !empty($personal->getAge())) ? $personal->getAge() : null, false) }} {{-- Age is calculated, not editable directly --}}
                {{ renderField('Student ID', 'student_id', $personal->student_id ?? null, $isEditMode) }}
                {{ renderField('Username', 'username', $user->username ?? null, $isEditMode) }}
                {{ renderField('Gender', 'gender', $personal->gender ?? null, $isEditMode, 'select', ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']) }}
                {{ renderField('Date of Birth', 'birthdate', $personal->birthdate ? ($personal->birthdate instanceof \Carbon\Carbon ? $personal->birthdate->format('Y-m-d') : $personal->birthdate) : null, $isEditMode, 'date') }}
                {{ renderField('Civil Status', 'civil_status', $personal->civil_status ?? null, $isEditMode, 'select', ['Single' => 'Single', 'Married' => 'Married', 'Divorced' => 'Divorced', 'Widowed' => 'Widowed']) }}
                {{ renderField('Phone Number', 'phone_number', $personal->phone_number ?? null, $isEditMode, 'tel') }}
                {{ renderField('Email', 'email_address', $personal->email_address ?? null, $isEditMode, 'email') }}
                {{ renderField('Home Address', 'permanent_address', $personal->permanent_address ?? null, $isEditMode, 'text', [], 2) }}
                {{ renderField('Current Address', 'current_address', $personal->current_address ?? null, $isEditMode, 'text', [], 2) }}
                {{ renderField('Social Link', 'social_link', $personal->social_link ?? null, $isEditMode, 'url', [], 4) }}
            </div>
            @else
            <div class="flex flex-col justify-center items-center flex-1 h-full">
                <h1 class="text-center text-3xl text-gray-400">No Personal Info</h1>
                <p class="text-gray-500 mt-2">User has not completed this section.</p>
            </div>
            @endif
            {{-- End Personal Info Section --}}

            {{-- Educational Info Section --}}
            @elseif ($query === 'educational')
            @php $records = $user->educationalBios; @endphp
            @if ($records->isNotEmpty())
            <div class="flex flex-col h-full space-y-6">
                @foreach($records as $index => $educ)
                <div class="border-b pb-6 last:border-b-0 last:pb-0">
                    <div class="flex mb-3 justify-between items-center">
                        @if($isEditMode)
                        <input type="text" name="educ[{{ $educ->id }}][school]" value="{{ old('educ.'.$educ->id.'.school', $educ->school) }}" placeholder="School Name" class="text-sm font-medium text-gray-700 border rounded px-2 py-1 w-1/2">
                        @else
                        <h4 class="text-sm font-medium text-gray-700">{{ $educ->school }}</h4>
                        @endif
                        @if($isEditMode)
                        <input type="text" name="educ[{{ $educ->id }}][degree_type]" value="{{ old('educ.'.$educ->id.'.degree_type', $educ->degree_type) }}" placeholder="Degree Type" class="text-xs text-blue-600 bg-blue-50 rounded-full px-2 py-0.5 border border-blue-100 focus:ring-blue-500 focus:border-blue-500">
                        @else
                        <span class="text-xs text-blue-600 bg-blue-50 rounded-full px-2 py-0.5 border border-blue-100">{{ $educ->degree_type }}</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{ renderField('Course', "educ[{$educ->id}][course_id]", $educ->course_id, $isEditMode, 'select', App\Models\Course::pluck('name', 'id')->toArray()) }}
                        {{ renderField('Location', "educ[{$educ->id}][school_location]", $educ->school_location, $isEditMode) }}
                        {{ renderField('Start Year', "educ[{$educ->id}][start]", $educ->start, $isEditMode, 'text') }} {{-- Consider using number type or date --}}
                        {{ renderField('End Year', "educ[{$educ->id}][end]", $educ->end, $isEditMode, 'text') }} {{-- Consider using number type or date, handle 'Present' --}}
                    </div>
                </div>
                @endforeach
                {{-- Add button for new record? Maybe later --}}
            </div>
            @else
            <div class="flex flex-col justify-center items-center flex-1 h-full">
                <h1 class="text-center text-3xl text-gray-400">No Educational Info</h1>
                <p class="text-gray-500 mt-2">User has not completed this section.</p>
            </div>
            @endif
            {{-- End Educational Info Section --}}

            {{-- Professional Info Section --}}
            @elseif ($query === 'professional')
            @php $records = $user->professionalBios; @endphp
            @if ($records->isNotEmpty())
            <div class="flex flex-col h-full space-y-8"> {{-- Increased spacing --}}
                @foreach($records as $index => $prof)
                <div class="border-b pb-8 last:border-b-0 last:pb-0"> {{-- Increased padding --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"> {{-- Consistent gap --}}
                        @php
                        $statuses = ['Employed' => 'Employed', 'Unemployed' => 'Unemployed', 'Self-employed' => 'Self-employed', 'Student' => 'Student', 'Working Student' => 'Working Student', 'Retired' => 'Retired'];
                        $empType1 = ['Private' => 'Private', 'Government' => 'Government', 'NGO/INGO' => 'NGO/INGO', 'Not Applicable' => 'Not Applicable'];
                        $empType2 = ['Full-Time' => 'Full-Time', 'Part-Time' => 'Part-Time', 'Traineeship' => 'Traineeship', 'Internship' => 'Internship', 'Contract' => 'Contract', 'Not Applicable' => 'Not Applicable'];
                        $salaries = ['No Income' => 'No Income', 'Below 10,000' => 'Below 10,000', '10,000-20,000' => '10,000-20,000', '20,001-40,000' => '20,001-40,000', '40,001-60,000' => '40,001-60,000', '60,001-80,000' => '60,001-80,000', '80,001-100,000' => '80,001-100,000', 'Over 100,000' => 'Over 100,000'];
                        $industries = App\Models\ProfessionalRecord::getIndustries(); // Assuming a static method or config
                        $waitingTimes = ['Below 3 months' => 'Below 3 months', '3-5 months' => '3-5 months', '6 months-1 year' => '6 months-1 year', 'Over 1 year' => 'Over 1 year', 'Job not secured' => 'Job not secured'];
                        @endphp

                        {{ renderField('Employment Status', "prof[{$prof->id}][employment_status]", $prof->employment_status, $isEditMode, 'select', $statuses) }}
                        {{ renderField('Current Job Title', "prof[{$prof->id}][job_title]", $prof->job_title, $isEditMode) }}
                        {{ renderField('Employment Type 1', "prof[{$prof->id}][employment_type1]", $prof->employment_type1, $isEditMode, 'select', $empType1) }}
                        {{ renderField('Company / Employer', "prof[{$prof->id}][company_name]", $prof->company_name, $isEditMode) }}
                        {{ renderField('Employment Type 2', "prof[{$prof->id}][employment_type2]", $prof->employment_type2, $isEditMode, 'select', $empType2) }}
                        {{ renderField('Monthly Salary Range', "prof[{$prof->id}][monthly_salary]", $prof->monthly_salary, $isEditMode, 'select', $salaries) }}
                        {{ renderField('Industry', "prof[{$prof->id}][industry]", $prof->industry, $isEditMode, 'select', array_combine($industries, $industries)) }}
                        {{ renderField('Location', "prof[{$prof->id}][work_location]", $prof->work_location, $isEditMode) }}
                        {{ renderField('Waiting Time', "prof[{$prof->id}][waiting_time]", $prof->waiting_time, $isEditMode, 'select', $waitingTimes, 2) }} {{-- Span 2 cols --}}

                        {{-- Skills, Methods, Attachments - Display Only for now, editing these inline is complex --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Job Search Method(s)</h3>
                                @if($prof->methods->isNotEmpty())
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($prof->methods as $method) <li class="text-gray-700">{{ $method->method }}</li> @endforeach
                                </ul>
                                @else <p class="text-gray-500 italic">None</p> @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Attachment(s)</h3>
                                @if($prof->attachments->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach ($prof->attachments as $attachment)
                                    <a class="text-blue-600 hover:text-blue-800 underline block" href="{{ asset('storage/professional/attachments/' . $attachment->link) }}" target="_blank">
                                        {{ $attachment->name }}
                                    </a>
                                    @endforeach
                                </div>
                                @else <p class="text-gray-500 italic">None</p> @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Hard Skill(s)</h3>
                                @if($prof->hardSkills->isNotEmpty())
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($prof->hardSkills as $skill) <li class="text-gray-700">{{ $skill->skill }}</li> @endforeach
                                </ul>
                                @else <p class="text-gray-500 italic">None</p> @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Soft Skill(s)</h3>
                                @if($prof->softSkills->isNotEmpty())
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($prof->softSkills as $skill) <li class="text-gray-700">{{ $skill->skill }}</li> @endforeach
                                </ul>
                                @else <p class="text-gray-500 italic">None</p> @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                {{-- Add button for new record? Maybe later --}}
            </div>
            @else
            <div class="flex flex-col justify-center items-center flex-1 h-full">
                <h1 class="text-center text-3xl text-gray-400">No Professional Info</h1>
                <p class="text-gray-500 mt-2">User has not completed this section.</p>
            </div>
            @endif
            {{-- End Professional Info Section --}}
            @endif

        </div>
    </form> {{-- End Form Wrapper --}}
</div>
@endsection

@section('script')
{{-- Keep existing script for Manage Account Modal if needed --}}
@if($user->isCompSet() && Auth::user()->admin()->is_super)
<script>
    const manageAccountsModal = document.getElementById('manageAccountModal');
    const openManageAccountModal = document.getElementById('openManageAccountModal');
    const closeManageAccountModal = document.getElementById('closeManageAccountModal');

    // Check if modal should be opened based on session flash (if applicable)
    <?php if (session('openModal') && session('openModal') === 1): ?>
        if (manageAccountsModal) manageAccountsModal.classList.remove('hidden');
    <?php endif ?>

    if (openManageAccountModal && manageAccountsModal) {
        openManageAccountModal.addEventListener('click', () => {
            manageAccountsModal.classList.remove('hidden');
        });
    }

    if (closeManageAccountModal && manageAccountsModal) {
        closeManageAccountModal.addEventListener('click', () => {
            manageAccountsModal.classList.add('hidden');
        });
        // Optional: Close modal on outside click
        window.addEventListener('click', (event) => {
            if (event.target === manageAccountsModal) {
                manageAccountsModal.classList.add('hidden');
            }
        });
    }
</script>
@endif
@endsection