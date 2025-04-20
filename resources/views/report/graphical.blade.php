@extends('layouts.admin')

@section('title', 'Graphical Report')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Graphical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Graphical</span></p>

    <div class="shadow rounded-lg mt-4">
        <div class="bg-white px-6 py-3 rounded-lg border-b">
            <div class="flex flex-col space-y-2">
                <!-- Header with information tooltip -->
                <div class="flex items-center">
                    <h2 class="text-lg font-medium text-gray-800">Report Filters</h2>
                    <div class="relative ml-3 group">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 rounded-full text-xs font-bold text-white cursor-help hover:bg-blue-600 transition">i</span>
                        <div class="absolute z-10 invisible group-hover:visible bg-gray-800 text-white text-sm p-3 rounded-md w-80 bottom-full left-1/2 transform -translate-x-1/2 -translate-y-2 shadow-lg">
                            <p class="mb-1">All category options besides <span class="font-bold text-yellow-300">All Users</span> only count accounts that have completed setup.</p>
                            <p>The subcategory options <span class="font-bold text-yellow-300">Batch</span>, <span class="font-bold text-yellow-300">Course</span> and <span class="font-bold text-yellow-300">Jobs</span> may have less people because they only count accounts with <span class="font-bold underline">complete setup</span>.</p>
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-6 border-transparent border-t-gray-800"></div>
                        </div>
                    </div>
                </div>

                <!-- Filter controls in a grid layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <!-- Category filter -->
                    <div class="flex flex-col">
                        <label for="category" class="text-sm font-medium text-gray-700 mb-0.5">Category</label>
                        @php
                        $category = request('category') ?? 'All Users'
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="category" id="category">
                            <option {{ $category == 'All Users' ? 'selected' : '' }} value="All Users">All Users</option>
                            <option {{ $category == 'Employed Alumni' ? 'selected' : '' }} value="Employed Alumni">Employed Alumni</option>
                            <option {{ $category == 'Unemployed Alumni' ? 'selected' : '' }} value="Unemployed Alumni">Unemployed Alumni</option>
                            <option {{ $category == 'Self-employed Alumni' ? 'selected' : '' }} value="Self-employed Alumni">Self-employed Alumni</option>
                            <option {{ $category == 'Students' ? 'selected' : '' }} value="Students">Student</option>
                            <option {{ $category == 'Working Student' ? 'selected' : '' }} value="Working Student">Working Student</option>
                            <option {{ $category == 'Retired' ? 'selected' : '' }} value="Retired">Retired Alumni</option>
                        </select>
                    </div>

                    <!-- Subcategory filter -->
                    <div class="flex flex-col">
                        <label for="subcategory" class="text-sm font-medium text-gray-700 mb-0.5">Group By</label>
                        @php
                        // Default to Department for super admin, Batch otherwise, matching statistical view
                        $subCategory = request('subcategory') ?? (Auth::user()->admin()->is_super ? 'Department' : 'Batch'); // Reverted
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="subcategory" id="subcategory">
                            @if (Auth::user()->admin()->is_super) // Reverted
                            <option {{ $subCategory == 'Department' ? 'selected' : '' }} value="Department">Department</option>
                            @endif
                            <option {{ $subCategory == 'Batch' ? 'selected' : '' }} value="Batch">Batch</option>
                            <option {{ $subCategory == 'Course' ? 'selected' : '' }} value="Course">Course</option>
                            {{-- Hide options irrelevant for Unemployed Alumni --}}
                            @if ($category !== 'Unemployed Alumni')
                            <option {{ $subCategory == 'Jobs' ? 'selected' : '' }} value="Jobs">Job Title</option>
                            <option {{ $subCategory == 'Industry' ? 'selected' : '' }} value="Industry">Industry</option>
                            <option {{ $subCategory == 'Employment Type 1' ? 'selected' : '' }} value="Employment Type 1">Employment Type (Sector)</option>
                            <option {{ $subCategory == 'Employment Type 2' ? 'selected' : '' }} value="Employment Type 2">Employment Type (Nature)</option>
                            <option {{ $subCategory == 'Monthly Salary' ? 'selected' : '' }} value="Monthly Salary">Monthly Salary</option>
                            <option {{ $subCategory == 'Waiting Time' ? 'selected' : '' }} value="Waiting Time">Waiting Time for First Job</option>
                            <option {{ $subCategory == 'Job Search Method' ? 'selected' : '' }} value="Job Search Method">Job Search Method</option>
                            @endif
                        </select>
                    </div>

                    <!-- Conditional third filter -->
                    @php
                    $professionalSubcategories = ['Jobs', 'Industry', 'Employment Type 1', 'Employment Type 2', 'Monthly Salary', 'Waiting Time', 'Job Search Method'];
                    $showBatchFilter = in_array($subCategory, ['Course', 'Department']) || in_array($subCategory, $professionalSubcategories);
                    $showDepartmentFilter = $subCategory == 'Batch' && Auth::user()->admin()->is_super; // Reverted
                    @endphp

                    @if ($showBatchFilter)
                    <div class="flex flex-col">
                        <label for="batch" class="text-sm font-medium text-gray-700 mb-0.5">Batch</label>
                        @php
                        // Optimized batch fetching
                        $batches = App\Models\EducationRecord::whereNotNull('end')
                                    ->distinct()
                                    ->orderBy('end', 'asc') // Sort ascending
                                    ->pluck('end')
                                    ->unique(); // Ensure uniqueness after plucking
                        $selectedBatch = request('batch') ?? '';
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="batch" id="batch">
                            <option value="">All Batches</option>
                            @foreach ($batches as $batch)
                            <option {{ $selectedBatch == $batch ? 'selected' : '' }} value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </select>
                    </div>
                    @elseif ($showDepartmentFilter)
                    <div class="flex flex-col">
                        <label for="department" class="text-sm font-medium text-gray-700 mb-0.5">Department</label>
                        @php
                        $selectedDepartment = request('department') ?? '';
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="department" id="department">
                            <option value="">All Departments</option>
                            @foreach (App\Models\Department::allValid() as $dept)
                            <option {{ $selectedDepartment == $dept->id ? 'selected' : '' }} value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    {{-- No third filter needed otherwise --}}
                </div>

                <!-- Generate Report Button -->
                <!-- <div class="flex justify-end mt-1">
                    <a href="" class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
                        </svg>
                        Generate Report
                    </a>
                </div> -->
            </div>
        </div>
    </div>

    <div class="shadow rounded-lg mt-4 flex-1 h-full max-h-full overflow-auto">
        <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg min-h-full">
            <div class="flex justify-between items-center">
                @php
                // Construct the dynamic title
                $titleParts = [];

                // Start with the base category description
                if ($category === 'All Users') {
                    $titleParts[] = 'All Registered Users';
                } else {
                    $titleParts[] = $category; // e.g., 'Employed Alumni'
                }

                // Add the subcategory (Group By)
                // Map internal subcategory value to a more readable name if needed
                $subCategoryDisplay = match($subCategory) {
                    'Employment Type 1' => 'Employment Type (Sector)',
                    'Employment Type 2' => 'Employment Type (Nature)',
                    'Jobs' => 'Job Title',
                    'Monthly Salary' => 'Monthly Salary',
                    'Waiting Time' => 'Waiting Time for First Job',
                    'Job Search Method' => 'Job Search Method',
                    default => $subCategory, // Keep original for Department, Batch, Course, Industry
                };
                $titleParts[] = 'by ' . $subCategoryDisplay;


                // Add the third filter if applicable and selected
                if ($showBatchFilter && !empty($selectedBatch)) {
                    $titleParts[] = 'from Batch ' . $selectedBatch;
                } elseif ($showDepartmentFilter && !empty($selectedDepartment)) {
                    // Fetch department name for the title
                    $deptName = App\Models\Department::find($selectedDepartment)?->name ?? 'Selected Department';
                    $titleParts[] = 'in ' . $deptName;
                } elseif (!$showBatchFilter && !$showDepartmentFilter && !Auth::user()->admin()->is_super) { // Reverted
                    // If no third filter and not super admin, add their office
                    $titleParts[] = 'in ' . Auth::user()->admin()->officeRelation->name; // Reverted
                }

                $dynamicTitle = implode(' ', $titleParts);

                // No need for complex default checks anymore with this structure

                @endphp
                <h1 class="font-medium text-lg h-12 flex items-center">{{ $dynamicTitle }}</h1>

                <div class="text-sm text-gray-600">
                    Total Count: <span class="font-semibold" id="totalAlumni"></span>
                </div>
            </div>
            <canvas id="graphical"></canvas>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const department = document.getElementById('department');
    const batch = document.getElementById('batch');

    if (department) {
        department.addEventListener('change', (e) => {
            const params = new URLSearchParams(window.location.search);

            params.delete('batch');
            if (params.has('department')) {
                params.delete('department');
            }

            params.append('department', e.target.value);
            window.location.search = params.toString();
        })
    }

    if (batch) {
        batch.addEventListener('change', (e) => {
            const params = new URLSearchParams(window.location.search);

            params.delete('department');
            if (params.has('batch')) {
                params.delete('batch');
            }

            params.append('batch', e.target.value);
            window.location.search = params.toString();
        })
    }
</script>
<script>
    const category = document.getElementById('category');
    const subCategory = document.getElementById('subcategory');

    category.addEventListener('change', (e) => {
        const params = new URLSearchParams(window.location.search);
        if (params.has('category')) {
            params.delete('category');
        }
        params.append('category', e.target.value);

        // Ensure professional-related subcategories are deselected if 'Unemployed Alumni' is chosen
        const professionalSubcats = ['Jobs', 'Industry', 'Employment Type 1', 'Employment Type 2', 'Monthly Salary', 'Waiting Time', 'Job Search Method'];
        if (params.get('category') === 'Unemployed Alumni' && professionalSubcats.includes(params.get('subcategory'))) {
            params.delete('subcategory');
        }

        window.location.search = params.toString();
    })

    subCategory.addEventListener('change', (e) => {
        const params = new URLSearchParams(window.location.search);
        const newSubCatValue = e.target.value;

        // Update subcategory param
        params.delete('subcategory');
        params.append('subcategory', newSubCatValue);

        // Define which subcategories use which third filter
        const professionalSubcats = ['Jobs', 'Industry', 'Employment Type 1', 'Employment Type 2', 'Monthly Salary', 'Waiting Time', 'Job Search Method'];
        const usesBatchFilter = ['Course', 'Department', ...professionalSubcats].includes(newSubCatValue);
        const usesDepartmentFilter = newSubCatValue === 'Batch'; // Assuming only 'Batch' uses Department filter

        // Remove conflicting third filter parameters
        if (!usesBatchFilter) {
            params.delete('batch');
        }
        if (!usesDepartmentFilter) {
             params.delete('department');
        }

        // Double-check: Ensure professional subcats are removed if category is 'Unemployed Alumni'
        const currentCategory = params.get('category') ?? 'All Users';
        if (currentCategory === 'Unemployed Alumni' && professionalSubcats.includes(newSubCatValue)) {
            params.delete('subcategory'); // Reset subcategory if incompatible
            params.delete('batch'); // Also remove batch if subcategory is reset
        }

        window.location.search = params.toString();
    })
</script>
<script>
    const ctx = document.getElementById('graphical');
    ctx.height = parseInt(getComputedStyle(ctx.parentNode.parentNode).getPropertyValue('height'), 10);

    <?php

    use App\Models\Department;
    use App\Models\User;
    use Illuminate\Support\Facades\Auth;

    // Get base users (partial or complete setup)
    $users = User::partialSet()->get()->merge(User::compSet()->get());

    $groups = $users
        ->filter(function ($user) use ($subCategory, $category) {
            // Initial filter: Most categories/subcategories require completed setup
            $needsCompSet = $category !== 'All Users';

            // Define subcategories requiring professional bio
            $profBioSubcategories = ['Jobs', 'Industry', 'Employment Type 1', 'Employment Type 2', 'Monthly Salary', 'Waiting Time', 'Job Search Method'];
            if (in_array($subCategory, $profBioSubcategories)) {
                $needsCompSet = true; // These always require a professional bio
            }
            // Batch and Course also require completed educational bio (implied by compSet)
            if ($subCategory === 'Batch' || $subCategory === 'Course') {
                 $needsCompSet = true;
            }

            // Exclude if complete setup is needed but not present
            if ($needsCompSet && !$user->isCompSet()) {
                return false;
            }

            // Apply department filtering based on admin role
            $admin = Auth::user()->admin(); // Reverted: Access relationship via method
            if (!$admin->is_super) {
                if ($user->department_id != $admin->office) {
                    return false; // Non-super admin sees only their department
                }
            } elseif (!empty(request('department')) && $subCategory === 'Batch') {
                 // Super admin filtering by department when grouping by Batch
                 if ($user->department_id != request('department')) {
                     return false;
                 }
            }

            // Define subcategories where batch filtering is applicable
            $professionalSubcategories = ['Jobs', 'Industry', 'Employment Type 1', 'Employment Type 2', 'Monthly Salary', 'Waiting Time', 'Job Search Method'];
            $batchFilterApplicable = in_array($subCategory, ['Course', 'Department']) || in_array($subCategory, $professionalSubcategories);

            // Apply batch filtering if selected and applicable
            if (!empty(request('batch')) && $batchFilterApplicable) {
                // Ensure educational bio exists before checking 'end'
                $eduBio = $user->getEducationalBio();
                if (!$eduBio || $eduBio->end != request('batch')) {
                    return false;
                }
            }

            // If we reach here, the user passes the filters
            return true;

        })
        ->groupBy(function ($user) use ($subCategory) {
            // Ensure related records exist before accessing properties
            $profBio = $user->getProfessionalBio(); // Can be null
            $eduBio = $user->getEducationalBio();   // Can be null

            switch ($subCategory) {
                case 'Department':
                    return $user->department?->name ?? 'N/A';
                case 'Batch':
                    return $eduBio?->end ?? 'N/A';
                case 'Course':
                    return $user->course?->name ?? 'N/A';
                case 'Jobs':
                    return $profBio?->job_title ?? 'N/A';
                case 'Industry':
                    return $profBio?->industry ?? 'N/A';
                case 'Employment Type 1':
                    return $profBio?->employment_type1 ?? 'N/A';
                case 'Employment Type 2':
                    return $profBio?->employment_type2 ?? 'N/A';
                case 'Monthly Salary':
                    // Handle 'no income' explicitly if needed, otherwise use value or N/A
                    return $profBio?->monthly_salary ?? 'N/A';
                case 'Waiting Time':
                    return $profBio?->waiting_time ?? 'N/A';
                case 'Job Search Method':
                    // Group by the first method found, or 'N/A' if none or no profBio
                    return $profBio?->methods->first()?->method ?? 'N/A';
                default:
                    return 'N/A'; // Fallback for unexpected subcategory
            }
        })->all();

    $xAxis = array_keys($groups);
    // Ensure yAxis calculation uses the filtered $groups
    $yAxis = array_map(fn($group) => User::countFromGroup($category, $group), $groups);
    ?>

    <?php $total = array_sum($yAxis); // Calculate total based on the final grouped data ?>

    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: [
                <?php
                foreach ($xAxis as $label) {
                    echo '"' . $label . '",';
                }
                ?>
            ],
            datasets: [{
                data: [
                    <?php
                    foreach ($yAxis as $value) {
                        echo $value . ',';
                    }
                    ?>
                ],
                backgroundColor: [
                    <?php
                    if ($subCategory == 'Department') {
                        foreach ($xAxis as $label) {
                            switch ($label) {
                                case 'College of Arts and Science':
                                    echo "'rgba(153, 102, 255, 0.2)',";
                                    break;
                                case 'College of Business and Accountancy':
                                    echo "'rgba(255, 205, 86, 0.2)',";
                                    break;
                                case 'College of Computer Studies':
                                    echo "'rgba(75, 192, 192, 0.2)',";
                                    break;
                                case 'College of Criminal Justice Education':
                                    echo "'rgba(255, 99, 132, 0.2)',";
                                    break;
                                case 'College of Hospitality and Tourism Management':
                                    echo "'rgba(255, 0, 255, 0.2)',";
                                    break;
                                case 'College of Nursing':
                                    echo "'rgba(0, 255, 0, 0.2)',";
                                    break;
                                case 'College of Engineering':
                                    echo "'rgba(255, 165, 0, 0.2)',";
                                    break;
                                case 'College of Teacher Education':
                                    echo "'rgba(0, 0, 255, 0.2)',";
                                    break;
                                case 'Graduate School':
                                    echo "'rgba(255, 215, 0, 0.2)',";
                                    break;
                                default:
                                    echo "'rgba(201, 203, 207, 0.2)',";
                                    break;
                            }
                        }
                    } else {
                        echo "'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'";
                    }
                    ?>
                ],
                borderColor: [
                    <?php
                    if ($subCategory == 'Department') {
                        foreach ($xAxis as $label) {
                            switch ($label) {
                                case 'College of Arts & Science':
                                    echo "'rgba(153, 102, 255, 1)',";
                                    break;
                                case 'College of Business & Accountancy':
                                    echo "'rgba(255, 205, 86, 1)',";
                                    break;
                                case 'College of Computer Studies':
                                    echo "'rgba(75, 192, 192, 1)',";
                                    break;
                                case 'College of Criminal Justice Education':
                                    echo "'rgba(255, 99, 132, 1)',";
                                    break;
                                case 'College of Hospitality & Tourism Management':
                                    echo "'rgba(255, 0, 255, 1)',";
                                    break;
                                case 'College of Nursing':
                                    echo "'rgba(0, 255, 0, 1)',";
                                    break;
                                case 'College of Engineering':
                                    echo "'rgba(255, 165, 0, 1)',";
                                    break;
                                case 'College of Teacher Education':
                                    echo "'rgba(0, 0, 255, 1)',";
                                    break;
                                case 'Graduate School':
                                    echo "'rgba(255, 215, 0, 1)',";
                                    break;
                                default:
                                    echo "'rgba(201, 203, 207, 1)',";
                                    break;
                            }
                        }
                    } else {
                        echo "'rgba(255, 99, 132, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(201, 203, 207, 1)'";
                    }
                    ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            onClick: (event, elements, chart) => {
                if (elements.length > 0) {
                    const elementIndex = elements[0].index;
                    const clickedLabel = chart.data.labels[elementIndex];

                    // Don't navigate if the label is 'N/A'
                    if (clickedLabel === 'N/A') {
                        return;
                    }

                    const currentParams = new URLSearchParams(window.location.search);
                    const statisticalParams = new URLSearchParams();

                    // Preserve existing relevant filters
                    if (currentParams.has('category')) {
                        statisticalParams.set('category', currentParams.get('category'));
                    }
                    if (currentParams.has('batch')) {
                        statisticalParams.set('batch', currentParams.get('batch'));
                    }
                     if (currentParams.has('department') && '{{ $subCategory }}' === 'Batch') { // Only carry over department if subcategory was Batch
                        statisticalParams.set('department', currentParams.get('department'));
                    }


                    // Add filter based on the clicked bar and current subcategory
                    const subCategoryValue = '{{ $subCategory }}';
                    let filterKey = '';
                    let filterValue = clickedLabel;

                    switch (subCategoryValue) {
                        case 'Department': filterKey = 'department';
                            // Need to map department name back to ID if possible, otherwise skip this filter for now
                            // This requires passing department IDs along with names to the JS, or making an AJAX call.
                            // For simplicity, we'll skip adding this specific filter on click for now.
                            // TODO: Implement department name to ID mapping if required for statistical view filtering.
                            break;
                        case 'Batch': filterKey = 'batch'; break;
                        case 'Course': filterKey = 'courses';
                            // Similar to department, mapping name to ID might be needed.
                            // TODO: Implement course name to ID mapping if required.
                             break;
                        case 'Jobs': filterKey = 'job_title'; break;
                        case 'Industry': filterKey = 'industry'; break;
                        case 'Employment Type 1': filterKey = 'employment_type1'; break;
                        case 'Employment Type 2': filterKey = 'employment_type2'; break;
                        case 'Monthly Salary': filterKey = 'monthly_salary'; break;
                        case 'Waiting Time': filterKey = 'waiting_time'; break;
                        case 'Job Search Method': filterKey = 'job_search_method'; break;
                    }

                    if (filterKey && filterKey !== 'department_id' && filterKey !== 'course_id') { // Add filter if key is determined and not needing ID mapping for now
                         statisticalParams.set(filterKey, filterValue);
                    } else if (filterKey) {
                        console.warn(`Filter key '${filterKey}' requires mapping name ('${filterValue}') to ID. Navigation will proceed without this specific filter.`);
                    }


                    // Construct the target URL
                    const targetUrl = `/report/statistical?${statisticalParams.toString()}`;
                    window.location.href = targetUrl;
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        // Avoid division by zero if total is 0
                        let percentage = <?php echo $total > 0 ? '(value / ' . $total . ' * 100).toFixed(2)' : '0'; ?>;
                        return `${value} (${percentage}%)`;
                    },
                    display: ctx => {
                        return ctx.chart.data.datasets[0].data[ctx.dataIndex] > 0
                    },
                    anchor: 'center',
                }
            }
        }
    });

    const totalAlumni = document.getElementById('totalAlumni');
    totalAlumni.innerText = <?php echo $total ?>;
</script>
@endsection