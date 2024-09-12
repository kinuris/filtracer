@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Graphical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Graphical</span></p>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <p class="text-md mr-3">All</p>
            <select class="border p-2 pr-32 rounded-lg text-gray-400 mr-8" name="all" id="all">
                <option value="all">Choose here...</option>
            </select>

            <p class="text-md mr-3">By</p>
            <select class="border p-2 pr-32 rounded-lg text-gray-400 mr-8" name="by" id="by">
                <option value="by">Choose here...</option>
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
<script>
    const ctx = document.getElementById('graphical');
    ctx.height = parseInt(getComputedStyle(ctx.parentNode.parentNode).getPropertyValue('height'), 10);

    <?php

    use App\Models\Department;

    $depts = Department::allValid();
    ?>

    new Chart(ctx, {
        type: 'bar',
        data: {

            labels: [
                <?php
                foreach ($depts as $dept) {
                    echo '"' . $dept->name . '",';
                }
                ?>
            ],
            datasets: [{
                label: '# of Votes',
                data: [
                    <?php
                    foreach ($depts as $dept) {
                        echo $dept->students->count() . ',';
                    }
                    ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection