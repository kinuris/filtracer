@extends('layouts.admin')

@section('title', 'Alumni List')

@section('content')
<?php
// Variables passed from controller:
// $dept, $courses, $users, $selectedCourse, $selectedBatch, $batches,
// $selectedCategory, $withField, $withValue, $withFieldsMap, $withFieldValues
?>
<div class="bg-gray-100 w-full max-h-screen min-h-[calc(100vh-64px)] overflow-auto p-4 md:p-8">
    <h1 class="font-medium tracking-widest text-lg">Alumni List</h1>
    <p class="text-gray-400 text-xs mb-4">Department / <span class="text-blue-500">{{ $dept->name }}</span></p>

    {{-- Filters Section --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <form id="filterForm" action="{{ route('admin.alumni.list', ['department' => $dept->id]) }}" method="GET">
            <div class="p-4 md:p-6 space-y-4">

                {{-- Filters Row 1 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    {{-- Category Select --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                        <select onchange="handleChangeCategory()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" name="category" id="category">
                            <option @if ($selectedCategory=='All Alumni' ) selected @endif value="All Alumni">All Employment Status</option>
                            <option @if ($selectedCategory=='Employed Alumni' ) selected @endif value="Employed Alumni">Employed Alumni</option>
                            <option @if ($selectedCategory=='Working Student' ) selected @endif value="Working Student">Working Student</option>
                            <option @if ($selectedCategory=='Unemployed Alumni' ) selected @endif value="Unemployed Alumni">Unemployed Alumni</option>
                            <option @if ($selectedCategory=='Self-Employed Alumni' ) selected @endif value="Self-Employed Alumni">Self-Employed Alumni</option>
                            <option @if ($selectedCategory=='Student Alumni' ) selected @endif value="Student Alumni">Student Alumni</option>
                            <option @if ($selectedCategory=='Retired Alumni' ) selected @endif value="Retired Alumni">Retired Alumni</option>
                            {{-- Add Verified/Unverified if needed --}}
                            {{-- <optgroup label="Status">
                                <option @if ($selectedCategory == 'Verified Alumni') selected @endif value="Verified Alumni">Verified Alumni</option>
                                <option @if ($selectedCategory == 'Unverified Alumni') selected @endif value="Unverified Alumni">Unverified Alumni</option>
                            </optgroup> --}}
                        </select>
                    </div>

                    {{-- Batch Dropdown --}}
                    <div>
                        <label for="batch" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                        <select onchange="handleBatchChange()" name="batch" id="batch" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Batches</option>
                            @foreach ($batches as $batchYear)
                            <option value="{{ $batchYear }}" {{ $selectedBatch == $batchYear ? 'selected' : '' }}>{{ $batchYear }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course Select --}}
                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select onchange="handleCourseChange()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" name="course" id="course">
                            <option value="-1">All Courses</option>
                            @foreach ($courses as $courseOption)
                            <option value="{{ $courseOption->id }}" {{ $selectedCourse == $courseOption->id ? 'selected' : '' }}>{{ $courseOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Additional Filter Row --}}
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-4 items-end pt-1">
                    {{-- Filter Field Selection --}}
                    <div>
                        <label for="with_field" class="block text-sm font-medium text-gray-700 mb-1">Filter By Field</label>
                        <select name="with_field" id="with_field" onchange="handleWithFieldChange()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Field --</option>
                            @foreach ($withFieldsMap as $fieldKey => $fieldName)
                            <option value="{{ $fieldKey }}" {{ $withField == $fieldKey ? 'selected' : '' }}>{{ $fieldName }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Value Selection --}}
                    <div>
                        <label for="with_value" class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                        <select name="with_value" id="with_value" onchange="handleWithValueChange()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed" {{ !$withField ? 'disabled' : '' }}>
                            <option value="">-- Select Value --</option>
                            {{-- Ensure $withFieldValues is available and not empty before looping --}}
                            @if ($withField && isset($withFieldValues) && $withFieldValues->isNotEmpty())
                            @foreach ($withFieldValues as $value)
                            {{-- Explicitly cast to string for reliable comparison, especially with numeric-like strings --}}
                            <option value="{{ $value }}" {{ (string) $withValue === (string) $value ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Remove Filter Button --}}
                    {{-- Aligned vertically with select inputs using flex and matching approximate height --}}
                    <div class="flex items-center h-[38px]">
                        <button type="button" onclick="removeWithFilter()" title="Remove this filter" class="text-sm text-red-600 hover:text-red-800 whitespace-nowrap py-2 px-1 transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500 rounded {{ !$withField ? 'invisible' : '' }}">
                            Remove
                        </button>
                        {{-- Optional: Icon alternative --}}
                        {{-- <button type="button" onclick="removeWithFilter()" title="Remove this filter" class="p-2 text-gray-500 hover:text-red-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 {{ !$withField ? 'invisible' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button> --}}
                    </div>
                </div>

                {{-- Search Input Row --}}
                <div class="pt-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input value="{{ request('search') ?? '' }}" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Name, ID, email..." type="text" name="search" id="search">
                </div>

                {{-- Action Buttons Row --}}
                <div class="flex flex-col sm:flex-row gap-2 justify-end pt-4">
                    <button class="w-full sm:w-auto rounded-md px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" type="submit">Filter</button>
                    {{-- Generate Report Button --}}
                    <button type="button" onclick="generateReport()" class="w-full sm:w-auto rounded-md px-4 py-2 text-sm bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Generate Report</button>
                    {{-- Optional: Add a reset button --}}
                    <a href="{{ route('admin.alumni.list', ['department' => $dept->id]) }}" class="w-full sm:w-auto rounded-md px-4 py-2 text-sm bg-gray-200 text-gray-700 text-center hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">Reset Filters</a>
                </div>

            </div>
        </form>
    </div>

    {{-- Results Section --}}
    <div class="shadow rounded-lg">
        <div class="bg-white py-3 px-4 md:px-6 border-b rounded-t-lg">
            <p class="font-semibold text-sm md:text-base text-gray-800">
                @php
                $titleParts = [$selectedCategory];
                if ($selectedBatch != '') {
                $titleParts[] = 'from Batch ' . e($selectedBatch);
                } else {
                $titleParts[] = 'from All Batches';
                }

                $courseNameForTitle = 'All Courses';
                if ($selectedCourse != -1) {
                $foundCourse = $courses->firstWhere('id', $selectedCourse);
                if($foundCourse) $courseNameForTitle = $foundCourse->name;
                }
                $titleParts[] = 'in ' . e($courseNameForTitle);

                if ($withField && $withValue && array_key_exists($withField, $withFieldsMap)) {
                $titleParts[] = 'with ' . e($withFieldsMap[$withField]) . ' = "' . e($withValue) . '"';
                }

                $reportTitle = implode(' ', $titleParts);
                @endphp
                {{ $reportTitle }}
                @if(request()->filled('search'))
                <span class="text-gray-500 font-normal"> (matching search "{{ e(request('search')) }}")</span>
                @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] border-collapse bg-white">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3 text-center">Image</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Employment Status</th>
                        <th class="px-6 py-3">Date Updated</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-center">
                                <img class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" src="{{ $user->image() }}" alt="{{ $user->name }}">
                            </div>
                        </td>
                        <td>
                            <a href="/user/view/{{ $user->id }}" class="flex items-center space-x-2 px-6 py-4 whitespace-nowrap font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->isCompset() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $user->isCompset() ? $user->employment() : 'Incomplete' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $user->updated_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="/user/view/{{ $user->id }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View details">
                                    <img src="{{ asset('assets/view.svg') }}" alt="View" class="w-5 h-5">
                                </a>
                                <button type="button"
                                    onclick="document.getElementById('deleteModal{{ $user->id }}').classList.remove('hidden')"
                                    class="text-red-600 hover:text-red-900 transition-colors focus:outline-none"
                                    title="Delete record">
                                    <img src="{{ asset('assets/trash.svg') }}" alt="Delete" class="w-5 h-5">
                                </button>

                                <!-- Delete Confirmation Modal -->
                                <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Alumni Record</h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">Are you sure you want to delete this alumni record? This action cannot be undone and all data associated with this record will be permanently removed.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <a href="/user/delete/{{ $user->id }}/department" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Delete
                                                </a>
                                                <button type="button" onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">No alumni found matching the criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
        <div class="bg-white rounded-b-lg p-4 border-t">
            {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    function handleBatchChange() {
        document.getElementById('filterForm').submit();
    }

    function handleCourseChange() {
        document.getElementById('filterForm').submit();
    }

    function handleChangeCategory() {
        document.getElementById('filterForm').submit();
    }

    function handleWithFieldChange() {
        const valueSelect = document.getElementById('with_value');
        if (valueSelect) {
            valueSelect.value = ''; // Reset value when field changes
        }
        document.getElementById('filterForm').submit();
    }

    function handleWithValueChange() {
        document.getElementById('filterForm').submit();
    }

    function removeWithFilter() {
        const fieldSelect = document.getElementById('with_field');
        const valueSelect = document.getElementById('with_value');
        if (fieldSelect) {
            fieldSelect.value = '';
        }
        if (valueSelect) {
            valueSelect.value = '';
        }
        // Set hidden inputs if needed to clear server-side values, or just submit
        document.getElementById('filterForm').submit();
    }

    function generateReport() {
        const form = document.getElementById('filterForm');
        const category = form.elements['category'].value;
        const batch = form.elements['batch'].value;
        const course = form.elements['course'].value;
        const search = form.elements['search'].value;
        const withField = form.elements['with_field'].value;
        const withValue = form.elements['with_value'].value;
        const departmentId = '{{ $dept->id }}'; // Get department ID from PHP

        // Construct the URL for the statistical report generation page
        // Assuming the route name is 'admin.report.statistical.generate'
        const reportUrl = new URL('/report/statistical/generate/', window.location.origin);

        // Add parameters conditionally
        reportUrl.searchParams.set('department', departmentId);
        if (category) reportUrl.searchParams.set('category', category);
        if (batch) reportUrl.searchParams.set('batch', batch);
        // Use 'course' as the parameter name, matching the form input name
        if (course && course !== '-1') reportUrl.searchParams.set('courses', course);
        if (search) reportUrl.searchParams.set('search', search);
        if (withField) reportUrl.searchParams.set('with_field', withField);
        if (withValue) reportUrl.searchParams.set('with_value', withValue);

        // Redirect to the report page
        window.location.href = reportUrl.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const withFieldSelect = document.getElementById('with_field');
        const withValueSelect = document.getElementById('with_value');

        function updateWithValueState() {
            if (withValueSelect && withFieldSelect) {
                withValueSelect.disabled = !withFieldSelect.value;
            }
        }

        updateWithValueState();
    });
</script>
@endsection