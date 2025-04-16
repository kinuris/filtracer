@extends('layouts.alumni')

@section('header')
<style>
    input::-webkit-file-upload-button {
        display: none;
        /* Keep this if you prefer custom styled upload buttons */
    }

    /* Style Choices.js elements to better match the form */
    .choices__inner {
        background-color: white;
        border: 1px solid #d1d5db;
        /* Equivalent to border-gray-300 */
        border-radius: 0.375rem;
        /* Equivalent to rounded-lg */
        padding: 0.5rem;
        /* Equivalent to p-2 */
        font-size: 0.875rem;
        /* Equivalent to sm:text-sm */
        color: #6b7280;
        /* Equivalent to text-gray-500 */
        min-height: auto;
        /* Adjust default min-height */
    }

    .choices__list--multiple .choices__item {
        background-color: #4f46e5;
        /* Equivalent to bg-indigo-600 */
        border-color: #4f46e5;
        color: white;
        border-radius: 0.25rem;
        /* rounded-md */
        margin-bottom: 0.25rem;
        /* Add some spacing */
    }

    .choices[data-type*="select-multiple"] .choices__button,
    .choices[data-type*="text"] .choices__button {
        border-left: 1px solid #4338ca;
        /* Slightly darker indigo */
        margin-left: 5px;
    }

    .choices__input {
        background-color: transparent;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0;
        /* Reset margin */
        padding: 0;
        /* Reset padding */
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #e0e7ff;
        /* bg-indigo-100 */
        color: #3730a3;
        /* text-indigo-800 */
    }

    /* Style file input to match others */
    input[type="file"].form-input {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        /* shadow-sm */
        font-size: 0.875rem;
        color: #6b7280;
    }

    input[type="file"].form-input::file-selector-button {
        display: none;
        /* Hide default button if using custom styling/label */
    }

    /* Custom file upload button style */
    .file-upload-label {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #e0e7ff;
        /* bg-indigo-100 */
        color: #4338ca;
        /* text-indigo-700 */
        border: 1px solid transparent;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    .file-upload-label:hover {
        background-color: #c7d2fe;
        /* Slightly darker indigo */
    }
</style>
@endsection

@section('title', 'Add Professional Info') {{-- Changed Title --}}

@section('content')
@php
$times = [
'Below 3 months',
'3-5 months',
'6 months-1 year',
'Over 1 year'
];
@endphp

@php
$methodsList = [ // Renamed from $methods to avoid conflict
'Career Center',
'Experimental Learning',
'Networking',
'Online Resources',
'Campus Resources'
];
@endphp

@php
$statuses = [
'Employed',
'Unemployed',
'Self-employed',
'Student',
'Working Student',
'Retired'
];
@endphp

<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Add Professional Info</h1> {{-- Changed Heading --}}
    <p class="text-gray-400 text-xs">Profile / <span class="text-blue-500">Add Professional Info</span></p> {{-- Changed Breadcrumb --}}

    <div class="flex max-h-[calc(100%-16px)]">
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" id="user-profile" alt="Profile">
                <p class="text-lg font-bold my-6">{{ $user->name }}</p>

                {{-- Removed display of existing professional info --}}
                <div class="flex place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_job.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">N/A</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">N/A</p>
                </div>

                <form action="/alumni/profile/upload/{{ $user->id }}" enctype="multipart/form-data" method="POST" id="change-profile">
                    @csrf
                    <label for="upload">
                        <p class="bg-blue-600 text-white w-fit p-2.5 rounded-lg mt-4 flex place-items-center cursor-pointer">
                            <img class="w-4 mr-2" src="{{ asset('assets/upload.svg') }}" alt="Upload">
                            Change Picture
                        </p>
                    </label>
                    <input class="hidden" type="file" name="profile" id="upload" accept="image/*">
                </form>
            </div>
        </div>

        <div class="mx-2"></div>

        <div class="flex-[3] flex flex-col mt-6 mb-3 max-h-full overflow-auto">
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg" id="main-content-area">
                    <div class="flex mb-4">
                        {{-- Removed Personal and Educational links --}}
                        <span class="text-gray-800 font-semibold pb-1 border-b-2 border-blue-500">Professional Info</span>
                    </div>

                    {{-- Use consistent vertical spacing between form elements using space-y-6 --}}
                    {{-- Changed form action to always point to create route --}}
                    <form enctype="multipart/form-data" action="{{ route('alumni.professional.store', $user->id) }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Grid container for form fields, 2 columns on medium screens and up --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"> {{-- Adjusted gap --}}

                            {{-- Use standard form styling for labels and inputs/selects --}}
                            <div>
                                <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                <select id="employment_status" name="employment_status" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                                    <option value="">Select...</option>
                                    @foreach ($statuses as $status)
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="{{ $status }}" {{ old('employment_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="employment_type1" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 1</label>
                                <select id="employment_type1" name="employment_type1" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                                    <option value="">Select...</option>
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="Private" {{ old('employment_type1') === 'Private' ? 'selected' : '' }}>Private</option>
                                    <option value="Government" {{ old('employment_type1') === 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="NGO/INGO" {{ old('employment_type1') === 'NGO/INGO' ? 'selected' : '' }}>NGO/INGO</option>
                                    <option value="Not Applicable" {{ old('employment_type1') === 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                </select>
                            </div>

                            <div>
                                <label for="employment_type2" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 2</label>
                                <select id="employment_type2" name="employment_type2" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                                    <option value="">Select...</option>
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="Full-Time" {{ old('employment_type2') === 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                                    <option value="Part-Time" {{ old('employment_type2') === 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                                    <option value="Traineeship" {{ old('employment_type2') === 'Traineeship' ? 'selected' : '' }}>Traineeship</option>
                                    <option value="Internship" {{ old('employment_type2') === 'Internship' ? 'selected' : '' }}>Internship</option>
                                    <option value="Contract" {{ old('employment_type2') === 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Not Applicable" {{ old('employment_type2') === 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                </select>
                            </div>

                            <div>
                                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="industry" name="industry" value="{{ old('industry') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                            </div>

                            <div>
                                <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                            </div>

                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="company" name="company_name" value="{{ old('company_name') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                            </div>

                            <div>
                                <label for="monthly_salary" class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary</label>
                                <select id="monthly_salary" name="monthly_salary" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                                    @php
                                    $ranges = [
                                    'No Income',
                                    'Below 10,000',
                                    '10,000-20,000',
                                    '20,001-40,000',
                                    '40,001-60,000',
                                    '60,001-80,000',
                                    '80,001-100,000',
                                    'Over 100,000'
                                    ];
                                    @endphp
                                    <option value="">Select...</option>
                                    @foreach ($ranges as $range)
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="{{ $range }}" {{ old('monthly_salary') === $range ? 'selected' : '' }}>{{ $range }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="work_location" class="block text-sm font-medium text-gray-700 mb-1">Work Location</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="work_location" name="work_location" value="{{ old('work_location') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                            </div>

                            {{-- Span this field across both columns on medium screens and up --}}
                            <div class="md:col-span-2">
                                <label for="waiting_time" class="block text-sm font-medium text-gray-700 mb-1">Waiting Time for First Job</label>
                                <select id="waiting_time" name="waiting_time" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500" required>
                                    <option value="">Select...</option>
                                    @foreach ($times as $time)
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="{{ $time }}" {{ old('waiting_time') === $time ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Span this field across both columns on medium screens and up --}}
                            <div class="md:col-span-2">
                                <label for="hard-skills" class="block text-sm font-medium text-gray-700 mb-1">Hard Skills</label>
                                {{-- Use select multiple for Choices.js --}}
                                <select multiple name="hard_skills[]" id="hard-skills" class="mt-1 block w-full"></select>
                                <p class="mt-1 text-xs text-gray-500">Select or type to add new skills.</p>
                            </div>

                            {{-- Span this field across both columns on medium screens and up --}}
                            <div class="md:col-span-2">
                                <label for="soft-skills" class="block text-sm font-medium text-gray-700 mb-1">Soft Skills</label>
                                <select multiple name="soft_skills[]" id="soft-skills" class="mt-1 block w-full"></select>
                                <p class="mt-1 text-xs text-gray-500">Select or type to add new skills.</p>
                            </div>

                            {{-- Span this field across both columns on medium screens and up --}}
                            <div class="md:col-span-2">
                                <label for="methods" class="block text-sm font-medium text-gray-700 mb-1">Job Search Methods Used</label>
                                <select multiple name="methods[]" id="methods" class="mt-1 block w-full"></select>
                                <p class="mt-1 text-xs text-gray-500">Select or type to add new methods.</p>
                            </div>

                            {{-- Span this field across both columns on medium screens and up --}}
                            <div class="md:col-span-2">
                                <label for="certs" class="block text-sm font-medium text-gray-700 mb-1">Certifications and Licenses (PDF)</label>
                                <div class="flex items-center space-x-4 mt-1">
                                    {{-- Hidden actual file input --}}
                                    <input type="file" id="certs" name="certs[]" class="hidden" multiple accept="application/pdf" onchange="updateFileName(this)">
                                    {{-- Custom styled button to trigger file input --}}
                                    <label for="certs" class="file-upload-label">
                                        Upload Files
                                    </label>
                                    {{-- Removed "View Existing" button --}}
                                </div>
                                <p id="file-name-display" class="mt-1 text-xs text-gray-500">You can upload multiple PDF files. No files selected.</p>
                            </div>
                        </div>

                        {{-- Add padding above the button and refine button styling --}}
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Save Professional Info {{-- Changed Button Text --}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const upload = document.getElementById('upload');
    const changeProfile = document.getElementById('change-profile');
    const userProfile = document.getElementById('user-profile');

    if (upload && changeProfile && userProfile) {
        upload.addEventListener('change', (e) => {
            const file = e.target.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.readAsDataURL(file);
            reader.addEventListener('load', () => {
                changeProfile.submit();
            });
        });
    }

    // Function to update the file name display for custom file input
    function updateFileName(inputElement) {
        const displayElement = document.getElementById('file-name-display');
        if (inputElement.files.length > 0) {
            const fileNames = Array.from(inputElement.files).map(file => file.name).join(', ');
            displayElement.textContent = `Selected: ${fileNames}`;
        } else {
            displayElement.textContent = 'No files selected.';
        }
    }
</script>

{{-- Removed Personal Info Script --}}
{{-- Removed Educational Info Script --}}

{{-- Professional Info Script --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
{{-- Link is already in @section('header') --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" /> --}}
<script>
    // --- Removed View Attachments Modal Script ---

    // --- Choices.js Initialization ---
    const methodsSelect = document.querySelector('#methods');
    const softSkillsSelect = document.querySelector('#soft-skills');
    const hardSkillsSelect = document.querySelector('#hard-skills');
    const choicesConfig = {
        removeItemButton: true,
        allowHTML: false, // Prevent HTML injection
        shouldSort: false, // Keep original order or order added
        placeholder: true,
        placeholderValue: 'Select or type to add...',
        // You can add custom templates or classes here if needed
    };

    if (methodsSelect) {
        const methodsChoices = new Choices(methodsSelect, {
            ...choicesConfig,
            choices: [
                @php
                // Removed logic for existing methods
                // Only add predefined options
                $oldMethods = old('methods', []); // Get old input if validation fails
                foreach($methodsList as $method) {
                    $selected = in_array($method, $oldMethods) ? 'true' : 'false';
                    echo "{ label: '".e($method).
                    "', value: '".e($method).
                    "', selected: $selected },";
                }
                // Add any old input values that weren't in the predefined list
                foreach($oldMethods as $oldMethod) {
                    if (!in_array($oldMethod, $methodsList)) {
                        echo "{ label: '".e($oldMethod).
                        "', value: '".e($oldMethod).
                        "', selected: true },";
                    }
                }
                @endphp
            ]
        });
    }

    if (softSkillsSelect) {
        const softSkillsChoices = new Choices(softSkillsSelect, {
            ...choicesConfig,
            choices: [
                @php
                $softSkillsList = [
                    'Communication Skills',
                    'Teamwork and Collaboration',
                    'Leadership Skills',
                    'Marketing Skills',
                    'Adaptability and Problem-Solving',
                    'Time Management',
                    'Work Ethic',
                    'Interpersonal Skills'
                ];
                // Removed logic for existing skills
                // Only add predefined options
                $oldSoftSkills = old('soft_skills', []); // Get old input
                foreach($softSkillsList as $skill) {
                    $selected = in_array($skill, $oldSoftSkills) ? 'true' : 'false';
                    echo "{ label: '".e($skill).
                    "', value: '".e($skill).
                    "', selected: $selected },";
                }
                // Add any old input values that weren't in the predefined list
                foreach($oldSoftSkills as $oldSkill) {
                    if (!in_array($oldSkill, $softSkillsList)) {
                        echo "{ label: '".e($oldSkill).
                        "', value: '".e($oldSkill).
                        "', selected: true },";
                    }
                }
                @endphp
            ]
        });
    }

    if (hardSkillsSelect) {
        const hardSkillsChoices = new Choices(hardSkillsSelect, {
            ...choicesConfig,
            choices: [
                @php
                $hardSkillsList = [
                    'Technical Skills',
                    'Engineering Skills',
                    'Business and Finance Skills',
                    'Marketing Skills',
                    'Cooking Skills'
                    // Add more predefined hard skills if needed
                ];

                // Only add predefined options
                $oldHardSkills = old('hard_skills', []); // Get old input
                foreach($hardSkillsList as $skill) {
                    $selected = in_array($skill, $oldHardSkills) ? 'true' : 'false';
                    echo "{ label: '".e($skill).
                    "', value: '".e($skill).
                    "', selected: $selected },";
                }

                foreach($oldHardSkills as $oldSkill) {
                    if (!in_array($oldSkill, $hardSkillsList)) {
                        echo "{ label: '".e($oldSkill).
                        "', value: '".e($oldSkill).
                        "', selected: true },";
                    }
                }
                @endphp
            ]
        });
    }
</script>
@endsection