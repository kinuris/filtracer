@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8">
    <h1 class="font-medium tracking-widest text-lg">Alumni List</h1>
    <p class="text-gray-400 text-xs mb-2">Department / <span class="text-blue-500">{{ $dept->name }}</span></p>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg justify-between">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-1 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
                <select class="pr-4 font-thin text-gray-500" name="course" id="course">
                    <option value="-1">All Courses</option>
                    @foreach ($courses as $course)
                    <option {{ request('course') == $course->id ? 'selected' : '' }} value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Image</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Employment Status</th>
                <th class="font-thin">Date Updated</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b">
                    <td class="text-blue-900 py">{{ $user->id }}</td>
                    <td class="flex justify-center place-items-center py-3"><img class="w-10 h-10 rounded-full" src="{{ $user->image() }}" alt="{{ $user->name }}"></td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->employment() }}</td>
                    <td>{{ $user->updated_at }}</td>
                    <td>
                        <div class="flex justify-center place-items-center">
                            <a class="mr-3" href="/user/view/{{ $user->id }}"><img src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            <a href="/user/delete/{{ $user->id }}"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-white rounded-b-lg p-3">
            {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection