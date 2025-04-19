@extends('layouts.alumni')

@section('header')
<style>
    /* ... (keep existing styles) ... */

    /* Style for disabled fields */
    .form-input-disabled,
    .form-select-disabled {
        background-color: #f3f4f6; /* bg-gray-100 */
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Style for Choices.js disabled state */
    .choices[data-disabled] {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .choices[data-disabled] .choices__inner {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
    .choices[data-disabled] .choices__input {
        background-color: #f3f4f6; /* Ensure input area also looks disabled */
        cursor: not-allowed;
    }

    /* Custom file upload button style */
    .file-upload-label {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #4f46e5; /* indigo-600 */
        color: white;
        border-radius: 0.375rem; /* rounded-md */
        cursor: pointer;
        font-size: 0.875rem; /* text-sm */
        font-weight: 500; /* font-medium */
        transition: background-color 0.15s ease-in-out;
    }
    .file-upload-label:hover {
        background-color: #4338ca; /* indigo-700 */
    }

</style>
{{-- Link Choices.js CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection

@section('title', 'Add Professional Info') {{-- Changed Title --}}

@section('content')
@php
$times = [
'Below 3 months',
'3-5 months',
'6 months-1 year',
'Over 1 year',
'Not Applicable' // Added
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

@php
$industries = [
    'Education',
    'Healthcare and Medical Services',
    'IT and Software Development',
    'BPO / Call Center',
    'Engineering and Construction',
    'Manufacturing',
    'Banking and Financial Services',
    'Government and Public Administration',
    'Retail and Wholesale Trade',
    'Hospitality and Tourism',
    'Transportation and Logistics',
    'Media and Communications',
    'Legal Services',
    'Agriculture, Forestry, and Fisheries',
    'Real Estate',
    'Utilities',
    'Non-Profit',
    'Arts, Culture, and Entertainment',
    'Automotive',
    'Freelancing / Entrepreneurship',
    'Not Applicable', // Added
];
@endphp

@php
$salaryRanges = [
    'No Income',
    'Below 10,000',
    '10,000-20,000',
    '20,001-40,000',
    '40,001-60,000',
    '60,001-80,000',
    '80,001-100,000',
    'Over 100,000',
    'Not Applicable' // Added
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

                            {{-- Fields affected by employment status --}}
                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="employment_type1" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 1</label>
                                <select id="employment_type1" name="employment_type1" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                                    <option value="">Select...</option>
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="Private" {{ old('employment_type1') === 'Private' ? 'selected' : '' }}>Private</option>
                                    <option value="Government" {{ old('employment_type1') === 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="NGO/INGO" {{ old('employment_type1') === 'NGO/INGO' ? 'selected' : '' }}>NGO/INGO</option>
                                    <option value="Not Applicable" {{ old('employment_type1') === 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                </select>
                            </div>

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="employment_type2" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 2</label>
                                <select id="employment_type2" name="employment_type2" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
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

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                                {{-- Changed from input type="text" to select --}}
                                <select id="industry" name="industry" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                                    <option value="">Select Industry...</option>
                                    @foreach ($industries as $industry)
                                    <option value="{{ $industry }}" {{ old('industry') === $industry ? 'selected' : '' }}>{{ $industry }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                            </div>

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="company" name="company_name" value="{{ old('company_name') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                            </div>

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="monthly_salary" class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary</label>
                                <select id="monthly_salary" name="monthly_salary" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                                    <option value="">Select...</option>
                                    @foreach ($salaryRanges as $range)
                                    {{-- Removed pre-selection based on $prof --}}
                                    <option value="{{ $range }}" {{ old('monthly_salary') === $range ? 'selected' : '' }}>{{ $range }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="employment-field-group"> {{-- Wrap label and field together --}}
                                <label for="work_location" class="block text-sm font-medium text-gray-700 mb-1">Work Location</label>
                                {{-- Removed pre-filling based on $prof --}}
                                <input type="text" id="work_location" name="work_location" value="{{ old('work_location') }}" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-500 employment-field" required>
                            </div>
                            {{-- End of affected fields --}}

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
                                <select multiple name="methods[]" id="methods" class="mt-1 block w-full"></select> {{-- Choices.js will target this --}}
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
{{-- Choices.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    // --- Profile Picture Upload ---
    const upload = document.getElementById('upload');
    const changeProfile = document.getElementById('change-profile');
    const userProfile = document.getElementById('user-profile');

    if (upload && changeProfile && userProfile) {
        upload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.addEventListener('load', () => {
                changeProfile.submit();
            });
        });
    }

    // --- File Name Display ---
    function updateFileName(inputElement) {
        const displayElement = document.getElementById('file-name-display');
        if (inputElement.files.length > 0) {
            const fileNames = Array.from(inputElement.files).map(file => file.name).join(', ');
            displayElement.textContent = `Selected: ${fileNames}`;
        } else {
            displayElement.textContent = 'No files selected.';
        }
    }

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

    let methodsChoicesInstance = null; // Store instance to enable/disable later

    if (methodsSelect) {
        methodsChoicesInstance = new Choices(methodsSelect, { // Assign to instance variable
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


    // --- Employment Status Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const employmentStatusSelect = document.getElementById('employment_status');
        const employmentFields = document.querySelectorAll('.employment-field'); // Select input/select fields directly
        const notApplicableStatuses = ['Student', 'Retired', 'Unemployed'];
        const studentRetiredStatuses = ['Student', 'Retired']; // For disabling methods

        function toggleEmploymentFields() {
            const selectedStatus = employmentStatusSelect.value;
            const isDisabled = notApplicableStatuses.includes(selectedStatus);

            employmentFields.forEach(field => {
                const fieldWrapper = field.closest('.employment-field-group'); // Find the wrapping div if needed for styling

                // field.disabled = isDisabled;
                // field.required = !isDisabled; // Toggle required attribute

                if (isDisabled) {
                    // Add disabled style class to the field itself
                    if (field.tagName === 'SELECT') {
                        field.classList.add('form-select-disabled');
                        field.classList.remove('form-input-disabled'); // Ensure correct class
                    } else {
                        field.classList.add('form-input-disabled');
                        field.classList.remove('form-select-disabled'); // Ensure correct class
                    }

                    // Set default 'Not Applicable' / 'N/A' / 'No Income' values
                    if (field.tagName === 'SELECT') {
                        let defaultValue = 'Not Applicable';
                        if (field.id === 'monthly_salary') {
                             // Check if 'No Income' option exists, otherwise use 'Not Applicable'
                             const noIncomeOption = Array.from(field.options).find(opt => opt.value === 'No Income');
                             defaultValue = noIncomeOption ? 'No Income' : 'Not Applicable';
                        }
                        // Check if the default value exists as an option before setting it
                        const optionExists = Array.from(field.options).some(opt => opt.value === defaultValue);
                        if (optionExists) {
                            field.value = defaultValue;
                        } else {
                            field.value = ''; // Fallback if 'Not Applicable' isn't an option
                        }
                    } else if (field.tagName === 'INPUT') {
                        field.value = 'N/A';
                    }
                } else {
                    // Remove disabled style class from the field
                    field.classList.remove('form-input-disabled', 'form-select-disabled');

                    // Clear the field ONLY if its current value is one of the defaults we set
                    // This prevents clearing user input or old() values when switching back to 'Employed' etc.
                    const defaultValues = ['N/A', 'Not Applicable', 'No Income'];
                    if (defaultValues.includes(field.value)) {
                         field.value = ''; // Clear the default value
                    }
                    // If using old() values, they should repopulate automatically if validation failed previously
                }
            });
        }

        function toggleMethodsField() {
            if (!methodsChoicesInstance) return; // Check if Choices instance exists

            const selectedStatus = employmentStatusSelect.value;
            // Disable methods only for Student or Retired, not Unemployed
            const isDisabled = studentRetiredStatuses.includes(selectedStatus);

            if (isDisabled) {
                methodsChoicesInstance.disable();
                // Optionally clear selected items if needed when disabled
                // methodsChoicesInstance.removeActiveItems(); // Removes highlighted items
                // methodsChoicesInstance.clearStore(); // Clears all options and selections - use with caution
                // methodsChoicesInstance.clearInput(); // Clears the text input
                // methodsChoicesInstance.setValue([]); // Clears selected values
            } else {
                methodsChoicesInstance.enable();
            }
        }

        // Add event listener to employment status select
        employmentStatusSelect.addEventListener('change', () => {
            toggleEmploymentFields();
            toggleMethodsField(); // Also toggle methods field on status change
        });

        // Run on page load to set initial state based on current/old value
        toggleEmploymentFields();
        toggleMethodsField();
    });

</script>
@endsection
