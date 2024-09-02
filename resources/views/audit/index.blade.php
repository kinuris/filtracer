@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8">
    <h1 class="font-medium tracking-widest text-lg mb-4">Audit Trail</h1>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg justify-between">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-1 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Date and Time</th>
                <th class="font-thin">Activity</th>
                <th class="font-thin">Action</th>
            </thead>
        </table>
    </div>
</div>
@endsection