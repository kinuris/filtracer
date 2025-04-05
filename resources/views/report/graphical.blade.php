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
                        $subCategory = request('subcategory') ?? 'Department'
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="subcategory" id="subcategory">
                            <option {{ $subCategory == 'Department' ? 'selected' : '' }} value="Department">Department</option>
                            <option {{ $subCategory == 'Batch' ? 'selected' : '' }} value="Batch">Batch</option>
                            <option {{ $subCategory == 'Course' ? 'selected' : '' }} value="Course">Course</option>
                            @if ($category !== 'Unemployed Alumni')
                            <option {{ $subCategory == 'Jobs' ? 'selected' : '' }} value="Jobs">Jobs</option>
                            @endif
                        </select>
                    </div>

                    <!-- Conditional third filter -->
                    @if ($subCategory == 'Course' || $subCategory == 'Department')
                    <div class="flex flex-col">
                        <label for="batch" class="text-sm font-medium text-gray-700 mb-0.5">Batch</label>
                        @php
                        $batches = [];
                        foreach (App\Models\User::all() as $user) {
                            if ($user->isCompSet() && $user->getEducationalBio()->end) {
                                $batches[$user->getEducationalBio()->end] = true;
                            }
                        }

                        $batches = array_keys($batches);
                        sort($batches);
                        $selectedBatch = request('batch') ?? '';
                        @endphp
                        <select class="border p-1.5 rounded-lg bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="batch" id="batch">
                            <option value="">All Batches</option>
                            @foreach ($batches as $batch)
                            <option {{ $selectedBatch == $batch ? 'selected' : '' }} value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </select>
                    </div>
                    @elseif ($subCategory == 'Batch')
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
            <h1 class="font-medium text-lg h-12">All Registered Alumni by Department</h1>
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
        if (params.get('category') === 'Unemployed Alumni' && params.get('subcategory') === 'Jobs') {
            params.delete('subcategory');
        }

        window.location.search = params.toString();
    })

    subCategory.addEventListener('change', (e) => {
        const params = new URLSearchParams(window.location.search);
        if (params.has('subcategory')) {
            params.delete('subcategory');
        }

        params.append('subcategory', e.target.value);
        if (params.get('category') === 'Unemployed Alumni' && params.get('subcategory') === 'Jobs') {
            params.delete('subcategory');
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

    // $groups = User::groupBy($subCategory);
    $users = User::partialSet()->get()->merge(User::compSet()->get());
    $groups = $users
        ->filter(function ($user) use ($subCategory, $category) {
            if ($subCategory === 'Department' || $subCategory === 'Course') {
                if (empty(request('batch'))) {
                    return $user->isCompSet();
                }

                return $user->isCompSet() && $user->getEducationalBio()->end === request('batch');
            } else if ($subCategory === 'Batch') {
                if (empty(request('department'))) {
                    return $user->isCompSet();
                }

                return $user->isCompSet() && $user->department_id == request('department');
            }

            return $user->isCompSet();
        })
        ->groupBy(function ($user) use ($subCategory) {
            switch ($subCategory) {
                case 'Department':
                    return $user->department->name;
                case 'Batch':
                    return $user->getEducationalBio()->end;
                case 'Course':
                    return $user->course->name;
                case 'Jobs':
                    return $user->getProfessionalBio()->job_title;
            }
        })->all();

    $xAxis = array_keys($groups);
    $yAxis = array_map(fn($group) => User::countFromGroup($category, $group), $groups);
    ?>

    <?php $total = 0 ?>

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
                        $total += $value;
                        echo $value . ',';
                    }
                    ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
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
                        return `${value} (${(value / <?php echo $total ?> * 100).toFixed(2)}%)`;
                    },
                    display: ctx => {
                        return ctx.chart.data.datasets[0].data[ctx.dataIndex] > 0
                    },
                    anchor: 'center',
                }
            }
        }
    });
</script>
@endsection