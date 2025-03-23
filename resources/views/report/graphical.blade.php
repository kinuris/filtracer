@extends('layouts.admin')

@section('title', 'Graphical Report')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Graphical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Graphical</span></p>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <p class="text-md mr-3">All</p>
            @php($category = request('category') ?? 'All Users')
            <select class="border p-2 pr-32 rounded-lg text-gray-400 mr-8" name="category" id="category">
                <option {{ $category == 'All Users' ? 'selected' : '' }} value="All Users">All Users</option>
                <option {{ $category == 'Employed Alumni' ? 'selected' : '' }} value="Employed Alumni">Employed Alumni</option>
                <option {{ $category == 'Unemployed Alumni' ? 'selected' : '' }} value="Unemployed Alumni">Unemployed Alumni</option>
                <option {{ $category == 'Self-employed Alumni' ? 'selected' : '' }} value="Self-employed Alumni">Self-employed Alumni</option>
                <option {{ $category == 'Students' ? 'selected' : '' }} value="Students">Student</option>
                <option {{ $category == 'Working Student' ? 'selected' : '' }} value="Working Student">Working Student</option>
                <option {{ $category == 'Retired' ? 'selected' : '' }} value="Retired">Retired Alumni</option>
                <!-- <option {{ $category == 'Salary' ? 'selected' : '' }} value="Salary">Salary</option>
                <option {{ $category == 'Waiting Time' ? 'selected' : '' }} value="Waiting Time">Waiting Time</option>
                <option {{ $category == 'Job Search Methods' ? 'selected' : '' }} value="Job Search Methods">Job Search Methods</option> -->
            </select>

            <p class="text-md mr-3">By</p>
            @php($subCategory = request('subcategory') ?? 'Department')
            <select class="border p-2 pr-32 rounded-lg text-gray-400 mr-2" name="subcategory" id="subcategory">
                <option {{ $subCategory == 'Department' ? 'selected' : '' }} value="Department">Department</option>
                <option {{ $subCategory == 'Batch' ? 'selected' : '' }} value="Batch">Batch</option>
                <option {{ $subCategory == 'Course' ? 'selected' : '' }} value="Course">Course</option>
                @if ($category !== 'Unemployed Alumni')
                <option {{ $subCategory == 'Jobs' ? 'selected' : '' }} value="Jobs">Jobs</option>
                @endif
            </select>

            <div class="relative inline-block group">
                <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 rounded-full text-xs font-bold text-white cursor-help hover:bg-blue-600">i</span>
                <div class="absolute z-10 invisible group-hover:visible bg-gray-800 text-white text-xs p-3 rounded-md w-72 bottom-full left-1/2 transform -translate-x-1/2 -translate-y-2 shadow-lg">
                    All category options besides <span class="font-bold text-yellow-300">All Users</span> only count accounts that have completed setup. <br><br> The subcategory options <span class="font-bold text-yellow-300">Batch</span>, <span class="font-bold text-yellow-300">Course</span> and <span class="font-bold text-yellow-300">Jobs</span> may have less people because they only count accounts with <span class="font-bold underline">complete setup</span>
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-6 border-transparent border-t-gray-800"></div>
                </div>
            </div>

            <div class="flex-1"></div>

            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="">Generate Report</a>
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
            if ($subCategory === 'Department' && $category === 'All Users') {
                return true;
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

    $depts = Department::allValid();
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
                label: 'User in department',
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