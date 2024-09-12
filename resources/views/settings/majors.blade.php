@extends('layouts.admin')

@section('content')
@include('components.add-major-modal')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Major List</h1>
    <p class="text-gray-400 text-xs mb-2">Settings / <span class="text-blue-500">Majors</span></p>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-2 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
                <button type="button" class="bg-blue-600 text-white p-2 rounded ml-3" id="openAddMajorModal">Add Major</button>
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Major Name</th>
                <th class="font-thin">Course Name</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody>
                @foreach ($majors as $major)
                <tr class="bg-white border-b text-center">
                    <td class="py-3">{{ $major->id }}</td>
                    <td>{{ $major->name }}</td>
                    <td>{{ $major->course->name }}</td>
                    <td>
                        <div class="flex justify-center place-items-center">
                            <a class="mr-3" href="/settings/major/edit/{{ $major->id }}"><img src="{{ asset('assets/settings_blue.svg') }}" alt="View"></a>
                            <a href="/settings/major/delete/{{ $major->id }}"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-white rounded-b-lg p-3">
            {{ $majors->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const openAddMajorModal = document.getElementById('openAddMajorModal');
    const addMajorModal = document.getElementById('addMajorModal');
    const closeAddMajorModal = document.getElementById('closeAddMajorModal');

    openAddMajorModal.addEventListener('click', () => {
        addMajorModal.classList.remove('hidden');
    });

    closeAddMajorModal.addEventListener('click', () => {
        addMajorModal.classList.add('hidden');
    });

    window.addEventListener('click', (e) => {
        if (e.target === addMajorModal) {
            addMajorModal.classList.add('hidden');
        }
    });
</script>
@endsection