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
            <select class="border p-2 pr-32 rounded-lg text-gray-400 mr-8" name="subcategory" id="subcategory">
                <option {{ $subCategory == 'Department' ? 'selected' : '' }} value="Department">Department</option>
                <option {{ $subCategory == 'Batch' ? 'selected' : '' }} value="Batch">Batch</option>
                <option {{ $subCategory == 'Course' ? 'selected' : '' }} value="Course">Course</option>
                @if ($category !== 'Unemployed Alumni')
                <option {{ $subCategory == 'Jobs' ? 'selected' : '' }} value="Jobs">Jobs</option>
                @endif
            </select>

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

    $groups = User::groupBy($subCategory);
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
                        return `${value} (${value / <?php echo $total ?> * 100}%)`;
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