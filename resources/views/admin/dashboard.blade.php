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
        <div class="bg-white p-8 flex items-center justify-between rounded-lg">
            <a href="/report/statistical?category=All+Entities" class="flex items-center group transition-transform hover:transform hover:scale-105">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold text-gray-800 group-hover:text-blue-600">{{ $users->count() }}</p>
                    <p class="text-sm text-gray-500 group-hover:text-blue-500">Registered Users</p>
                </div>
                <div class="p-3 rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                    <img class="w-10 h-10" src="{{ asset('assets/users.svg') }}" alt="Registered">
                </div>
            </a>

            <div class="h-16 border-r border-gray-200 mx-4"></div>

            <a href="/report/statistical?category=All+Users" class="flex items-center group transition-transform hover:transform hover:scale-105">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold text-gray-800 group-hover:text-green-600">{{ $users->where('role', '=', 'Alumni')->count() }}</p>
                    <p class="text-sm text-gray-500 group-hover:text-green-500">Registered Alumni</p>
                </div>
                <div class="p-3 rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                    <img class="w-10 h-10" src="{{ asset('assets/registered.svg') }}" alt="Registered">
                </div>
            </a>

            <div class="h-16 border-r border-gray-200 mx-4"></div>

            <a href="/report/statistical?category=Employed+Alumni" class="flex items-center group transition-transform hover:transform hover:scale-105">
                <div class="flex flex-col mr-4">
                    <p class="text-3xl font-bold text-gray-800 group-hover:text-purple-600">{{ $users->where('role', '=', 'Alumni')->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')->count() }}</p>
                    <p class="text-sm text-gray-500 group-hover:text-purple-500">Employed Alumni</p>
                </div>
                <div class="p-3 rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                    <img class="w-10 h-10" src="{{ asset('assets/employed.svg') }}" alt="Employed">
                </div>
            </a>

            <div class="h-16 border-r border-gray-200 mx-4"></div>

            <a href="/report/statistical?category=Unemployed+Alumni" class="flex items-center group transition-transform hover:transform hover:scale-105">
                <div class="flex flex-col mr-4">
                    @php($users = App\Models\User::query())
                    <p class="text-3xl font-bold text-gray-800 group-hover:text-amber-600">{{ $users->where('role', '=', 'Alumni')->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')->count() }}</p>
                    <p class="text-sm text-gray-500 group-hover:text-amber-500">Unemployed Alumni</p>
                </div>
                <div class="p-3 rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                    <img class="w-10 h-10" src="{{ asset('assets/unemployed.svg') }}" alt="Unemployed">
                </div>
            </a>
        </div>
    </div>

    <div class="flex">
        <div class="shadow rounded-lg mt-4 mr-4 flex-1 h-full max-h-full overflow-auto">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg min-h-full">
                <div class="flex items-center h-12">
                    <h1 class="font-medium text-lg">Registered Alumni by Higher Education Department</h1>
                    <div class="relative ml-2 group cursor-help">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hover:text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 left-0 -bottom-10 mb-1 w-56 z-10">
                            This graph displays all registered alumni across departments, including both complete and incomplete profiles.
                        </div>
                    </div>
                </div>
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

            <div class="shadow rounded-lg overflow-hidden">
                <table class="min-w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Requested By</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(App\Models\Post::where('status', 'pending')->latest()->get() as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="w-10 h-10 rounded-full object-cover mr-2 shadow" src="{{ $post->creator->image() }}" alt="Creator Profile">
                                    <div class="text-sm text-gray-500">{{ $post->creator->name }}</div>
                                </div>
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
                                <a href="/admin/post/approve/{{ $post->id }}" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve
                                </a>
                                <a href="/admin/post/reject/{{ $post->id }}" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 flex items-center">
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
        <?php echo $users->whereRelation('professionalRecords', 'employment_status', '=', 'Retired')->count() . ','; ?>
    ];

    const data = {
        labels: [
            'Employed',
            'Unemployed',
            'Self-employed',
            'Student',
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