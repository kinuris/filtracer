@extends('layouts.admin')

@section('title', 'Alumni List')

@section('content')
<div class="bg-gray-100 w-full max-h-screen overflow-auto p-8">
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
        <table class="w-full border-collapse bg-white">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Image</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Employment Status</th>
                    <th class="px-6 py-3">Date Updated</th>
                    <th class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">{{ $user->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex justify-center">
                            <img class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" src="{{ $user->image() }}" alt="{{ $user->name }}">
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->isCompset() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $user->isCompset() ? $user->employment() : 'Incomplete' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->updated_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        <div class="flex justify-center space-x-3">
                            <a href="/user/view/{{ $user->id }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View details">
                                <img src="{{ asset('assets/view.svg') }}" alt="View" class="w-5 h-5">
                            </a>
                            <button type="button"
                                onclick="document.getElementById('deleteModal{{ $user->id }}').classList.remove('hidden')"
                                class="text-red-600 hover:text-red-900 transition-colors focus:outline-none"
                                title="Delete record">
                                <img src="{{ asset('assets/trash.svg') }}" alt="Delete" class="w-5 h-5">
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Alumni Record</h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">Are you sure you want to delete this alumni record? This action cannot be undone and all data associated with this record will be permanently removed.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <a href="/user/delete/{{ $user->id }}/department" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Delete
                                            </a>
                                            <button type="button" onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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