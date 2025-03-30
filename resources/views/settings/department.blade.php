@extends('layouts.admin')

@section('title', 'Settings > Departments')

@section('content')
@include('components.add-department-modal')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col">
    <h1 class="font-medium tracking-widest text-lg">Department List</h1>
    <p class="text-gray-400 text-xs mb-2">Settings / <span class="text-blue-500">Departments</span></p>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-2 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
                <button type="button" class="bg-blue-600 text-white p-2 rounded ml-3" id="openAddDepartmentModal">Add Department</button>
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Logo</th>
                <th class="font-thin">Department Name</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($departments as $department)
                <tr class="border-b">
                    <td class="text-gray-500">{{ $department->id }}</td>
                    <td class="flex justify-center place-items-center py-3">
                        <img class="w-12 h-12 rounded-full shadow-md" src="{{ asset('storage/departments/'. $department->logo) }}" alt="{{ $department->name }}">
                    </td>
                    <td>{{ $department->name }}</td>
                    <td>
                        <div class="flex justify-center place-items-center">
                            <a class="mr-3" href="/settings/department/edit/{{ $department->id }}"><img src="{{ asset('assets/settings_blue.svg') }}" alt="View"></a>
                            <button type="button" onclick="confirmDelete({{ $department->id }})" class="cursor-pointer"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></button>
                            
                           <!-- Delete Confirmation Modal for {{ $department->id }} -->
                            <div id="deleteModal-{{ $department->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                                <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-auto transform transition-all">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900">Confirm Deletion</h3>
                                    </div>
                                    <p class="text-gray-600 mb-6">Are you sure you want to delete <span class="font-medium text-gray-800">"{{ $department->name }}"</span>? This action cannot be undone.</p>
                                    <div class="flex justify-end space-x-3">
                                        <button onclick="closeDeleteModal({{ $department->id }})" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400">Cancel</button>
                                        <a href="/settings/department/delete/{{ $department->id }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">Delete Department</a>
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
            {{ $departments->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function confirmDelete(departmentId) {
        const modal = document.getElementById(`deleteModal-${departmentId}`);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeDeleteModal(departmentId) {
        const modal = document.getElementById(`deleteModal-${departmentId}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
<script>
    const upload = document.getElementById('logo');
    const preview = document.getElementById('preview');
    const nofile = document.getElementById('nofile');

    upload.addEventListener('change', function() {
        const file = this.files[0];  

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.addEventListener('load', function() {
            preview.src = reader.result;

            preview.classList.remove('hidden');
            nofile.classList.add('hidden');
        });
    });
</script>
<script>
    const openAddDeparmentModal = document.getElementById('openAddDepartmentModal');
    const addDepartmentModal = document.getElementById('addDepartmentModal');
    const closeAddDeparmentModal = document.getElementById('closeAddDeparmentModal');

    openAddDeparmentModal.addEventListener('click', () => {
        addDepartmentModal.classList.remove('hidden');
    });

    closeAddDeparmentModal.addEventListener('click', () => {
        addDepartmentModal.classList.add('hidden');
    });

    window.addEventListener('click', function(event) {
        if (event.target === addDepartmentModal) {
            addDepartmentModal.classList.add('hidden');
        }
    });
</script>
@endsection