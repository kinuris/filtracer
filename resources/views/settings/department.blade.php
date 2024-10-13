@extends('layouts.admin')

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
                            <a href="/settings/department/delete/{{ $department->id }}"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></a>
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