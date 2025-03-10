@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

@if(request('view'))
    @php
    $viewPost = App\Models\Post::find(request('view'));
    @endphp
    @if($viewPost)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-3/4 max-w-xl max-h-[90vh] overflow-auto">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Post Details</h2>
                        <a href="{{ url()->current() }}" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="shadow rounded-lg mt-4">
                        <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg h-full">
                            <div class="flex">
                                <img class="w-10 h-10 rounded-full object-cover" src="{{ $viewPost->creator->image() }}" alt="Poster Profile">
                                <div class="flex flex-col ml-3">
                                    <p>{{ $viewPost->creator->name }} - 
                                        <span class="
                                            @if($viewPost->status == 'Denied') text-red-500 
                                            @elseif($viewPost->status == 'Approved') text-green-500 
                                            @elseif($viewPost->status == 'pending') text-yellow-500 
                                            @endif">
                                            {{ ucfirst($viewPost->status) }}
                                        </span>
                                    </p>
                                    <div class="flex place-items-center">
                                        <p class="text-xs text-gray-400">{{ $viewPost->creator->role === 'Admin' ? ($viewPost->creator->admin()->is_super ? 'Superadmin' : 'Admin' ) : 'Alumni'  }}</p>
                                        @if ($viewPost->post_category === 'Event')
                                        <img src="{{ asset('assets/calendar.svg') }}" class="inline w-3 h-3 ml-1" alt="Event Post">
                                        @elseif ($viewPost->post_category === 'Job Opening')
                                        <img src="{{ asset('assets/job_opening.svg') }}" class="inline w-3 h-3 ml-1" alt="Job Opening Post">
                                        @elseif ($viewPost->post_category === 'Announcement')
                                        <img src="{{ asset('assets/announcement.svg') }}" class="inline w-3 h-3 ml-1" alt="Announcement Post">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($viewPost->attached_image !== null)
                            <img class="mt-3" src="{{ $viewPost->image() }}" alt="Attachment">
                            @endif

                            <p class="text-lg font-bold mt-3">{{ $viewPost->title }}</p>
                            <div class="text-sm text-gray-400 break-words prose max-w-none mt-2">
                                {!! $viewPost->content !!}
                            </div>

                            @if ($viewPost->source !== null)
                            <p class="text-sm mt-3">Source:</p>
                            <a class="text-sm underline text-blue-500" target="_blank" href="{{ $viewPost->source }}">{{ $viewPost->source }}</a>
                            @endif

                            @if ($viewPost->post_category !== 'Announcement')
                            <p class="text-sm mt-3">Status: <span class="text-gray-400 font-light">{{ $viewPost->post_status ?? 'N/A' }}</span></p>
                            @endif

                            <p class="text-sm mt-3">Posted on: <span class="text-gray-400 font-light">{{ $viewPost->created_at->format('F j, Y \a\t g:i a') }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                    <a href="/admin/post/approve/{{ $viewPost->id }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200">
                        Approve
                    </a>
                    <a href="/admin/post/reject/{{ $viewPost->id }}" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">
                        Reject
                    </a>
                </div>
            </div>
        </div>
    @endif
@endif

@php($users = App\Models\User::query())
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-8 flex place-items-center justify-between rounded-lg">
            <div class="flex">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold">{{ $users->count() }}</p>
                    <p class="text-sm text-gray-400">Registered Users</p>
                </div>
                <img class="w-10" src="{{ asset('assets/users.svg') }}" alt="Registered">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold">{{ $users->where('role', '=', 'Alumni')->count() }}</p>
                    <p class="text-sm text-gray-400">Registered Alumni</p>
                </div>
                <img class="w-10" src="{{ asset('assets/registered.svg') }}" alt="Registered">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold">{{ $users->where('role', '=', 'Alumni')->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')->count() }}</p>
                    <p class="text-sm text-gray-400">Employed Alumni</p>
                </div>
                <img class="w-10" src="{{ asset('assets/employed.svg') }}" alt="Employed">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-4">
                    @php($users = App\Models\User::query())
                    <p class="text-3xl font-bold">{{ $users->where('role', '=', 'Alumni')->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')->count() }}</p>
                    <p class="text-sm text-gray-400">Unemployed Alumni</p>
                </div>
                <img class="w-10" src="{{ asset('assets/unemployed.svg') }}" alt="Unemployed">
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

    <div class="shadow rounded-lg min-h-96 overflow-auto mt-8">
        <div class="bg-white py-4 h-full flex flex-col px-6 rounded-lg">
            <h1 class="font-medium text-lg mb-4">Post Request Approval List</h1>
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(App\Models\Post::where('status', 'pending')->latest()->get() as $post)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $post->creator->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $post->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                            <a href="?view={{ $post->id }}" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View
                            </a>
                            <a href="/admin/post/approve/{{ $viewPost->id }}" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve
                            </a>
                            <a href="/admin/post/reject/{{ $viewPost->id }}" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reject
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-500">No pending post requests</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
        plugins: [ChartDataLabels],
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
            plugins: {
                legend: {
                    display: false,
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value * 100 / sum).toFixed(2) + "%";
                        return percentage;
                    },
                }
            },
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                    }
                }
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