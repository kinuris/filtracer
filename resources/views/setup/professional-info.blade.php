@extends('layouts.setup')

@section('header')
<style>
    input::-webkit-file-upload-button {
        display: none;
    }
</style>
@endsection

@section('title', 'Professional Information')

@section('content')

@php($times = [
'Below 3 months',
'3-5 months',
'6 months-1 year',
'Over 1 year'
])

@php($methods = [
'Career Center',
'Experimental Learning',
'Networking',
'Online Resources',
'Campus Resources'
])

@php($statuses = [
'Employed',
'Unemployed',
'Self-employed',
'Student',
'Working Student',
'Retired'
])
<div class="flex place-items-start h-full justify-center max-h-[calc(100vh-5rem)] pb-10 overflow-scroll">
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
            <div class="flex flex-col">
                <div class="flex">
                    <div class="flex flex-col flex-1 max-w-[50%]">
                        <label for="employment_status">Employment Status</label>
                        <select class="text-gray-400 border rounded-lg p-2" name="employment_status" id="employment_status">
                            @foreach ($statuses as $status)
                            <option value="{{ $status}}">{{ $status }}</option>
                            @endforeach
                        </select>

                        <label class="mt-3" for="type1">Employment Type 1</label>
                        <select class="text-gray-400 border rounded-lg p-2" type="text" name="employment_type1">
                            <option value="Private">Private</option>
                            <option value="Government">Government</option>
                            <option value="NGO/INGO">NGO/INGO</option>
                        </select>

                        <label class="mt-3" for="type2">Employment Type 2</label>
                        <select class="text-gray-400 border rounded-lg p-2" type="number" name="employment_type2">
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                            <option value="Traineeship">Traineeship</option>
                            <option value="Internship">Internship</option>
                            <option value="Contract">Contract</option>
                        </select>

                        <label class="mt-3" for="industry">Industry</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="text" name="industry">
                    </div>

                    <div class="mx-2"></div>

                    <div class="flex flex-col flex-1">
                        <label for="job-title">Current Job Title</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="text" name="job_title">

                        <label class="mt-3" for="company">Company / Employer</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="text" name="company">

                        @php($ranges = [
                        'No Income',
                        'Below 10,000',
                        '10,000-20,000',
                        '20,001-40,000',
                        '40,001-60,000',
                        '60,001-80,000',
                        '80,001-100,000',
                        'Over 100,000'
                        ])
                        <label class="mt-3" for="range">Monthly Salary Range</label>
                        <select class="text-gray-400 border rounded-lg p-2" name="monthly_salary" id="range">
                            @foreach ($ranges as $range)
                            <option value="{{ $range }}">{{ $range }}</option">
                            </option>
                            @endforeach
                        </select>

                        <label class="mt-3" for="location">Location</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="text" name="work_location">
                    </div>
                </div>

                <label class="mt-5" for="waiting">Waiting Time <span class="text-gray-400">(period to get a job after graduation)</span></label>
                <select class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waiting">
                    @foreach ($times as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                    @endforeach
                </select>

                <label class="mt-5" for="methods">Job Search Methods <span class="text-gray-400">(used to find a job)</span></label>
                <select multiple name="methods[]" id="methods">

                </select>

                <div class="flex">
                    <div class="flex-1 flex flex-col">
                        <label class="mt-3" for="hard-skills">Hard Skill/s</label>
                        <select multiple name="hard_skills[]" id="hard-skills">
                        </select>
                    </div>

                    <div class="mx-2"></div>

                    <div class="flex-1 flex flex-col">
                        <label class="mt-3" for="soft-skills">Soft Skill/s</label>
                        <select multiple name="soft_skills[]" id="soft-skills">
                        </select>
                    </div>
                </div>

                <label class="mt-3" for="certs">
                    <p>Certification & Licenses</p>
                    <div class="flex gap-2 mt-3">
                        <div class="bg-gray-500 rounded-lg text-white p-2 w-fit">Upload</div>
                        <button id="openViewAttachmentsModal" type="button" class="bg-blue-500 rounded-lg text-white p-2 w-fit">Existing</button>
                    </div>
                </label>
                <input class="mt-2" type="file" id="certs" accept="application/pdf" name="certs[]" multiple>

                <div class="flex mt-4 justify-end">
                    <button class="text-white bg-blue-600 p-2 rounded" type="submit">Save & Next</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    const waiting = document.querySelector('#methods');
    const softSkills = document.querySelector('#soft-skills');
    const hardSkills = document.querySelector('#hard-skills');

    new Choices(waiting, {
        removeItemButton: true,
        choices: [
            <?php
            foreach ($methods as $method) {
                echo "{ label: '$method', value: '$method' },";
            }
            ?>
        ]
    });

    new Choices(softSkills, {
        removeItemButton: true,
        choices: [
            <?php
            $softSkills = [
                'Communication Skills',
                'Teamwork and Collaboration',
                'Leadership Skills',
                'Adaptability and Problem-Solving',
                'Time Management',
                'Work Ethic',
                'Interpersonal Skills'
            ];

            foreach ($softSkills as $skill) {
                echo "{ label: '$skill', value: '$skill' },";
            }
            ?>
        ]
    });

    new Choices(hardSkills, {
        removeItemButton: true,
        choices: [
            <?php
            $hardSkills = [
                'Technical Skills',
                'Engineering Skills',
                'Business and Finance Skills',
                'Marketing Skills',
                'Cooking Skills'
            ];

            foreach ($hardSkills as $skill) {
                echo "{ label: '$skill', value: '$skill'},";
            }
            ?>
        ]
    });
</script>
@endsection