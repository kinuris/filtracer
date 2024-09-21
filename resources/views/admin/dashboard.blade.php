@extends('layouts.admin')

@section('content')
@php($users = App\Models\User::query())
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-8 flex place-items-center justify-between rounded-lg">
            <div class="flex">
                <div class="flex flex-col mr-10">
                    <p class="text-3xl font-bold">{{ $users->where('role', '!=', 'Admin')->count() }}</p>
                    <p class="text-sm text-gray-400">Registered Users</p>
                </div>
                <img src="{{ asset('assets/registered.svg') }}" alt="Registered">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-10">
                    <p class="text-3xl font-bold">{{ $users->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')->count() }}</p>
                    <p class="text-sm text-gray-400">Employed Users</p>
                </div>
                <img src="{{ asset('assets/employed.svg') }}" alt="Employed">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-10">
                    @php($users = App\Models\User::query())
                    <p class="text-3xl font-bold">{{ $users->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')->count() }}</p>
                    <p class="text-sm text-gray-400">Unemployed Users</p>
                </div>
                <img src="{{ asset('assets/unemployed.svg') }}" alt="Unemployed">
            </div>
        </div>
    </div>

    <div class="flex">
        <div class="shadow rounded-lg mt-4 mr-4 flex-1 h-full max-h-full overflow-auto">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg min-h-full">
                <h1 class="font-medium text-lg h-12">Registered Alumni by Higher Education Department</h1>
                <canvas id="summary"></canvas>
            </div>
        </div>

        <div class="shadow rounded-lg mt-4 h-full max-h-full overflow-auto">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg min-h-full">
                <h1 class="flex-1 font-medium text-lg text-center h-12">Alumni Employement Status</h1>
                <canvas id="employement_stats"></canvas>
                <div class="flex-1"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const ctx = document.getElementById('summary');
    const statCtx = document.getElementById('employement_stats');

    ctx.height = parseInt(getComputedStyle(ctx.parentNode.parentNode).getPropertyValue('height'), 10) - 30;

    <?php

    use App\Models\Department;
    use App\Models\User;

    $depts = Department::allValid();
    ?>

    new Chart(ctx, {
        type: 'bar',

        data: {
            labels: [
                <?php
                foreach ($depts as $dept) {
                    echo '"' . $dept->shortened() . '",';
                }
                ?>
            ],
            datasets: [{
                label: '# of Users',
                data: [
                    <?php
                    foreach ($depts as $dept) {
                        echo $dept->students->count() . ',';
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
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        options: {
            plugins: {
                legend: {
                    display: false,
                },

            }
        },
    });

    const stats = [
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')->count() . ','; ?>
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')->count() . ','; ?>
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Self-employed')->count() . ','; ?>
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Student')->count() . ','; ?>
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Working Student')->count() . ','; ?>
        <?php $users = User::query()->where('role', '=', 'Alumni'); ?>
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Retired')->count() . ','; ?>
    ];

    const data = {
        labels: [
            'Employed',
            'Unemployed',
            'Self-employed',
            'Student',
            'Working Student',
            'Retired',
        ],
        datasets: [{
            data: stats,
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
            borderWidth: 1,
            hoverOffset: 4
        }]
    };

    new Chart(statCtx, {
        type: 'doughnut',
        data,
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        return value;
                    },
                    display: ctx => {
                        return stats[ctx.dataIndex] > 0
                    },
                    anchor: 'center',
                },
            }
        }
    });
</script>
@endsection