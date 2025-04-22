@extends('layouts.setup')

@section('header')
{{-- Link Choices.js CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    input::-webkit-file-upload-button {
        display: none;
    }

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
        background-color: #6b7280; /* gray-500 */
        color: white;
        border-radius: 0.5rem; /* rounded-lg */
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .file-upload-label:hover {
        background-color: #4b5563; /* gray-600 */
    }

    /* Ensure select text is black */
    select {
        color: black !important; /* Force black text color */
    }
    /* Ensure Choices.js text is black */
    .choices__inner,
    .choices__list--single .choices__item,
    .choices__list--dropdown .choices__item {
        color: black !important;
    }
    /* Ensure placeholder text is not black if desired, otherwise remove this */
    .choices__list--single .choices__item.choices__placeholder {
        color: #6b7280 !important; /* Keep placeholder gray */
    }
    select option {
        color: black; /* Ensure options are black */
    }
    select:disabled,
    .form-select-disabled {
        color: #6b7280 !important; /* Gray text when disabled */
    }
    .choices[data-disabled] .choices__inner,
    .choices[data-disabled] .choices__list--single .choices__item {
        color: #6b7280 !important; /* Gray text for disabled Choices.js */
    }


</style>
@endsection

@section('title', 'Professional Information')

@section('content')

@php
    $times = [
        'Below 3 months',
        '3-5 months',
        '6 months-1 year',
        'Over 1 year',
        'Job not secured' // Added
    ];
@endphp

@php
    $methodsList = [ // Renamed from $methods
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

<div class="flex place-items-start h-full justify-center max-h-[calc(100vh-5rem)] pb-10 overflow-auto">
    <div class="shadow-lg bg-white mt-12 w-[60%] p-2 rounded-lg flex flex-col">
        <div class="flex gap-2">
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-blue-600"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
        </div>

        <h1 class="text-2xl font-semibold tracking-wider text-center mt-5">Complete your Profile</h1>
        <p class="text-blue-600 font-bold mt-2 text-xl text-center">Professional Info</p>

        <form class="mx-8" enctype="multipart/form-data" action="/alumni/setup/professional/{{ auth()->user()->id }}" method="POST">
            @csrf
            <div class="flex flex-col space-y-4"> {{-- Added space-y-4 for consistent spacing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"> {{-- Use grid for layout --}}
                    {{-- Employment Status --}}
                    <div class="md:col-span-2"> {{-- Span full width initially or adjust as needed --}}
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                        {{-- Removed text-gray-500 --}}
                        <select class="block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="employment_status" id="employment_status" required>
                            <option value="">Select...</option>
                            @foreach ($statuses as $status)
                            <option value="{{ $status}}" {{ old('employment_status', $professionalRecord->employment_status ?? '') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Employment Type 1 --}}
                    <div class="employment-field-group">
                        <label for="employment_type1" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 1</label>
                        {{-- Removed text-gray-500 --}}
                        <select class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="employment_type1" id="employment_type1">
                            <option value="">Select...</option>
                            <option value="Private" {{ old('employment_type1', $professionalRecord->employment_type1 ?? '') == 'Private' ? 'selected' : '' }}>Private</option>
                            <option value="Government" {{ old('employment_type1', $professionalRecord->employment_type1 ?? '') == 'Government' ? 'selected' : '' }}>Government</option>
                            <option value="NGO/INGO" {{ old('employment_type1', $professionalRecord->employment_type1 ?? '') == 'NGO/INGO' ? 'selected' : '' }}>NGO/INGO</option>
                            <option value="Not Applicable" {{ old('employment_type1', $professionalRecord->employment_type1 ?? '') == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                        </select>
                    </div>

                    {{-- Employment Type 2 --}}
                    <div class="employment-field-group">
                        <label for="employment_type2" class="block text-sm font-medium text-gray-700 mb-1">Employment Type 2</label>
                        {{-- Removed text-gray-500 --}}
                        <select class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="employment_type2" id="employment_type2">
                             <option value="">Select...</option>
                            <option value="Full-Time" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                            <option value="Part-Time" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                            <option value="Traineeship" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Traineeship' ? 'selected' : '' }}>Traineeship</option>
                            <option value="Internship" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Internship' ? 'selected' : '' }}>Internship</option>
                            <option value="Contract" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Not Applicable" {{ old('employment_type2', $professionalRecord->employment_type2 ?? '') == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                        </select>
                    </div>

                    {{-- Industry --}}
                    <div class="employment-field-group">
                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                        {{-- Removed text-gray-500 --}}
                        <select class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('industry') border-red-500 @enderror" name="industry" id="industry">
                            <option value="">Select Industry...</option>
                            @foreach ($industries as $industry)
                            <option value="{{ $industry }}" {{ old('industry', $professionalRecord->industry ?? '') == $industry ? 'selected' : '' }}>{{ $industry }}</option>
                            @endforeach
                        </select>
                        @error('industry')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Current Job Title --}}
                    <div class="employment-field-group">
                        <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Current Job Title</label>
                        {{-- Removed text-gray-500 --}}
                        <input class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('job_title') border-red-500 @enderror" type="text" name="job_title" id="job_title" value="{{ old('job_title', $professionalRecord->job_title ?? '') }}">
                        @error('job_title')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Company / Employer --}}
                    <div class="employment-field-group">
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company / Employer</label>
                        {{-- Removed text-gray-500 --}}
                        <input class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('company_name') border-red-500 @enderror" type="text" name="company_name" id="company" value="{{ old('company_name', $professionalRecord->company_name ?? '') }}">
                         {{-- Changed name to company_name to match reference --}}
                        @error('company_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Monthly Salary Range --}}
                    <div class="employment-field-group">
                        <label for="monthly_salary" class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary Range</label>
                        {{-- Removed text-gray-500 --}}
                        <select class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="monthly_salary" id="monthly_salary">
                            <option value="">Select...</option>
                            @foreach ($salaryRanges as $range)
                            <option value="{{ $range }}" {{ old('monthly_salary', $professionalRecord->monthly_salary ?? '') == $range ? 'selected' : '' }}>{{ $range }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Location --}}
                    <div class="employment-field-group">
                        <label for="work_location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        {{-- Removed text-gray-500 --}}
                        <input class="employment-field block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('work_location') border-red-500 @enderror" type="text" name="work_location" id="work_location" value="{{ old('work_location', $professionalRecord->work_location ?? '') }}">
                        @error('work_location')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Waiting Time --}}
                    <div class="md:col-span-2">
                        <label for="waiting_time" class="block text-sm font-medium text-gray-700 mb-1">Waiting Time <span class="text-gray-400">(period to get first job after graduation)</span></label>
                        {{-- Removed text-gray-500 --}}
                        <select class="block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="waiting_time" id="waiting_time">
                            <option value="">Select...</option>
                            @foreach ($times as $time)
                            <option value="{{ $time }}" {{ old('waiting_time', $professionalRecord->waiting_time ?? '') == $time ? 'selected' : '' }}>{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        // Keep existing data retrieval logic
                        $existingMethods = old('methods', $professionalRecord ? $professionalRecord->methods->pluck('method')->toArray() : []);
                        $existingHardSkills = old('hard_skills', $professionalRecord ? $professionalRecord->hardSkills->pluck('skill')->toArray() : []);
                        $existingSoftSkills = old('soft_skills', $professionalRecord ? $professionalRecord->softSkills->pluck('skill')->toArray() : []);
                    @endphp

                    {{-- Job Search Methods --}}
                    <div class="md:col-span-2">
                        <label for="methods" class="block text-sm font-medium text-gray-700 mb-1">Job Search Methods <span class="text-gray-400">(used to find a job)</span></label>
                        <select multiple name="methods[]" id="methods" class="mt-1 block w-full"></select>
                        <p class="mt-1 text-xs text-gray-500">Select or type to add new methods.</p>
                    </div>

                    {{-- Hard Skills --}}
                    <div>
                        <label for="hard-skills" class="block text-sm font-medium text-gray-700 mb-1">Hard Skill/s</label>
                        <select multiple name="hard_skills[]" id="hard-skills" class="mt-1 block w-full"></select>
                         <p class="mt-1 text-xs text-gray-500">Select or type to add new skills.</p>
                    </div>

                    {{-- Soft Skills --}}
                    <div>
                        <label for="soft-skills" class="block text-sm font-medium text-gray-700 mb-1">Soft Skill/s</label>
                        <select multiple name="soft_skills[]" id="soft-skills" class="mt-1 block w-full"></select>
                         <p class="mt-1 text-xs text-gray-500">Select or type to add new skills.</p>
                    </div>

                    {{-- Certifications --}}
                    <div class="md:col-span-2">
                        <label for="certs" class="block text-sm font-medium text-gray-700 mb-1">Certification & Licenses (PDF)</label>
                         <div class="flex items-center space-x-4 mt-1">
                            {{-- Hidden actual file input --}}
                            <input type="file" id="certs" name="certs[]" class="hidden" multiple accept="application/pdf" onchange="updateFileName(this)">
                            {{-- Custom styled button to trigger file input --}}
                            <label for="certs" class="file-upload-label">
                                Upload New Files
                            </label>
                            @if($professionalRecord && $professionalRecord->attachments->isNotEmpty())
                                <button id="openViewAttachmentsModal" type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">View Existing</button>
                            @endif
                        </div>
                        <p id="file-name-display" class="mt-1 text-xs text-gray-500">Uploading new files will replace existing ones. No files selected.</p>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex mt-6 justify-end gap-2 pt-4"> {{-- Added pt-4 --}}
                    <a href="/alumni/setup/educational" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">Back</a>
                    <button class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" type="submit">Save & Next</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal for viewing attachments (if needed, keep existing logic) --}}
{{-- Example: --}}
{{-- @if($professionalRecord && $professionalRecord->attachments->isNotEmpty())
    <div id="viewAttachmentsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Existing Attachments</h3>
                <div class="mt-2 px-7 py-3">
                    <ul class="list-disc list-inside text-left">
                        @foreach($professionalRecord->attachments as $attachment)
                            <li><a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-blue-500 hover:underline">{{ $attachment->file_name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeViewAttachmentsModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif --}}

@endsection

@section('script')
{{-- Choices.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    // --- File Name Display ---
    function updateFileName(inputElement) {
        const displayElement = document.getElementById('file-name-display');
        if (!displayElement) return; // Check if element exists
        if (inputElement.files.length > 0) {
            const fileNames = Array.from(inputElement.files).map(file => file.name).join(', ');
            displayElement.textContent = `Selected: ${fileNames}. Uploading new files will replace existing ones.`;
        } else {
            displayElement.textContent = 'No files selected. Uploading new files will replace existing ones.';
        }
    }

    // --- Choices.js Initialization ---
    const existingMethods = @json($existingMethods);
    const existingHardSkills = @json($existingHardSkills);
    const existingSoftSkills = @json($existingSoftSkills);

    const choicesConfig = {
        removeItemButton: true,
        allowHTML: false,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select or type to add...',
        // Ensure Choices.js text color is handled by CSS
        // classNames: {
        //     containerOuter: 'choices',
        //     containerInner: 'choices__inner', // Default class, CSS targets this
        //     input: 'choices__input',
        //     inputCloned: 'choices__input--cloned',
        //     list: 'choices__list',
        //     listItems: 'choices__list--multiple',
        //     listSingle: 'choices__list--single',
        //     listDropdown: 'choices__list--dropdown',
        //     item: 'choices__item', // Default class, CSS targets this
        //     itemSelectable: 'choices__item--selectable',
        //     itemDisabled: 'choices__item--disabled',
        //     itemChoice: 'choices__item--choice', // Default class, CSS targets this
        //     placeholder: 'choices__placeholder', // Default class, CSS targets this
        //     group: 'choices__group',
        //     groupHeading: 'choices__heading',
        //     button: 'choices__button',
        //     activeState: 'is-active',
        //     focusState: 'is-focused',
        //     openState: 'is-open',
        //     disabledState: 'is-disabled',
        //     highlightedState: 'is-highlighted',
        //     selectedState: 'is-selected',
        //     flippedState: 'is-flipped',
        //     loadingState: 'is-loading',
        //     noResults: 'has-no-results',
        //     noChoices: 'has-no-choices'
        //   }
    };

    let methodsChoicesInstance = null; // Store instance to enable/disable later
    const methodsSelect = document.querySelector('#methods');
    if (methodsSelect) {
        methodsChoicesInstance = new Choices(methodsSelect, {
            ...choicesConfig,
            choices: [
                @foreach ($methodsList as $method) { label: '{{ e($method) }}', value: '{{ e($method) }}' }, @endforeach
            ]
        });
        // Set initial values after initialization
        methodsChoicesInstance.setValue(existingMethods);
    }


    const hardSkillsSelect = document.querySelector('#hard-skills');
    if (hardSkillsSelect) {
        const hardSkillsChoices = new Choices(hardSkillsSelect, {
            ...choicesConfig,
            choices: [
                @php $hardSkillsList = ['Technical Skills', 'Engineering Skills', 'Business and Finance Skills', 'Marketing Skills', 'Cooking Skills']; @endphp
                @foreach ($hardSkillsList as $skill) { label: '{{ e($skill) }}', value: '{{ e($skill) }}' }, @endforeach
            ]
        });
        hardSkillsChoices.setValue(existingHardSkills);
    }

    const softSkillsSelect = document.querySelector('#soft-skills');
    if (softSkillsSelect) {
        const softSkillsChoices = new Choices(softSkillsSelect, {
            ...choicesConfig,
            choices: [
                @php $softSkillsList = ['Communication Skills', 'Teamwork and Collaboration', 'Leadership Skills', 'Marketing Skills', 'Adaptability and Problem-Solving', 'Time Management', 'Work Ethic', 'Interpersonal Skills']; @endphp
                @foreach ($softSkillsList as $skill) { label: '{{ e($skill) }}', value: '{{ e($skill) }}' }, @endforeach
            ]
        });
        softSkillsChoices.setValue(existingSoftSkills);
    }

    // --- Employment Status Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const employmentStatusSelect = document.getElementById('employment_status');
        const employmentFields = document.querySelectorAll('.employment-field'); // Select input/select fields directly
        const notApplicableStatuses = ['Student', 'Retired', 'Unemployed'];
        const studentRetiredStatuses = ['Student', 'Retired']; // For disabling methods

        function toggleEmploymentFields() {
            if (!employmentStatusSelect) return; // Exit if select not found

            const selectedStatus = employmentStatusSelect.value;
            const isDisabled = notApplicableStatuses.includes(selectedStatus);

            employmentFields.forEach(field => {
                if (isDisabled) {
                    // Add disabled style class
                    field.classList.add(field.tagName === 'SELECT' ? 'form-select-disabled' : 'form-input-disabled');
                    field.classList.remove(field.tagName === 'SELECT' ? 'form-input-disabled' : 'form-select-disabled');

                    // Set default 'Not Applicable' / 'N/A' / 'No Income' values ONLY IF the field is currently empty or has an old default value
                    const currentVal = field.value.trim();
                    const defaultValuesToOverwrite = ['', 'N/A', 'Not Applicable', 'No Income']; // Values that can be safely overwritten

                    if (defaultValuesToOverwrite.includes(currentVal)) {
                        if (field.tagName === 'SELECT') {
                            let defaultValue = 'Not Applicable';
                            if (field.id === 'monthly_salary') {
                                const noIncomeOption = Array.from(field.options).find(opt => opt.value === 'No Income');
                                defaultValue = noIncomeOption ? 'No Income' : 'Not Applicable';
                            }
                            const optionExists = Array.from(field.options).some(opt => opt.value === defaultValue);
                            if (optionExists) {
                                field.value = defaultValue;
                            } else {
                                field.value = ''; // Fallback
                            }
                        } else if (field.tagName === 'INPUT') {
                            field.value = 'N/A';
                        }
                    }
                     // Make field not required when disabled
                    field.removeAttribute('required');

                } else {
                    // Remove disabled style class
                    field.classList.remove('form-input-disabled', 'form-select-disabled');
                    field.disabled = false; // Re-enable the field

                    // Clear the field ONLY if its current value is one of the defaults we set
                    const defaultValuesToClear = ['N/A', 'Not Applicable', 'No Income'];
                    if (defaultValuesToClear.includes(field.value)) {
                         field.value = ''; // Clear the default value
                    }
                    // Re-add required attribute if it was originally required (assuming all these fields are required when enabled)
                    // Check if the field is not an optional one before adding required
                    if (!field.classList.contains('optional-when-enabled')) { // Add this class to fields that shouldn't be required
                         field.setAttribute('required', 'required');
                    }
                }
            });
        }

        function toggleMethodsField() {
            if (!methodsChoicesInstance) return; // Check if Choices instance exists

            const selectedStatus = employmentStatusSelect ? employmentStatusSelect.value : '';
            // Disable methods only for Student or Retired, not Unemployed
            const isDisabled = studentRetiredStatuses.includes(selectedStatus);

            if (isDisabled) {
                methodsChoicesInstance.disable();
                // Clear selected values when disabled for Student/Retired
                methodsChoicesInstance.setValue([]);
            } else {
                methodsChoicesInstance.enable();
            }
        }

        // Add event listener to employment status select
        if (employmentStatusSelect) {
            employmentStatusSelect.addEventListener('change', () => {
                toggleEmploymentFields();
                toggleMethodsField(); // Also toggle methods field on status change
            });
        }

        // Run on page load to set initial state based on current/old value
        toggleEmploymentFields();
        toggleMethodsField();

        // --- Modal Logic (Example) ---
        const openModalBtn = document.getElementById('openViewAttachmentsModal');
        const closeModalBtn = document.getElementById('closeViewAttachmentsModal');
        const modal = document.getElementById('viewAttachmentsModal');

        if (openModalBtn && closeModalBtn && modal) {
            openModalBtn.addEventListener('click', () => {
                modal.style.display = 'block';
            });
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            // Close modal if clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }

    });
</script>
@endsection