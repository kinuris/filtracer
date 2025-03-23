@extends('layouts.admin')

@section('title', 'View Imports')

@section('content')
<div class="h-[calc(100%-4rem)]">
    <div class="bg-gray-100 w-full h-full p-8 flex flex-col overflow-auto max-h-[calc(100%-0.01px)]">
        <h1 class="font-medium tracking-widest text-lg">File Imports</h1>

        <div class="shadow rounded-lg mt-8">
            <form>
                <div class="bg-white py-4 flex items-center px-6 border-b rounded-t-lg">
                    <input type="text" name="search" placeholder="Search..." class="border rounded py-2 px-4 w-full max-w-96">
                    <div class="flex-1"></div>
                    <input type="submit" value="Filter" class="bg-blue-600 text-white rounded py-2 px-4 ml-4">
                </div>
            </form>

            <table class="w-full">
                <thead class="bg-white text-blue-900 border-b">
                    <th class="font-thin py-3">ID</th>
                    <th class="font-thin">Name</th>
                    <th class="font-thin">Uploaded By</th>
                    <th class="font-thin">Date Uploaded</th>
                    <th class="font-thin">File Size</th>
                    <th class="font-thin">Actions</th>
                </thead>
                <tbody class="bg-white text-center">
                    @foreach ($imports as $import)
                    <tr>
                        <td class="py-3">{{ $import->id }}</td>
                        <td>{{ $import->filename }}</td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <img src="{{ $import->uploader->image() }}" alt="{{ $import->uploader->name }}" class="w-8 h-8 rounded-full object-cover">
                                <span>{{ $import->uploader->name }}</span>
                            </div>
                        </td>
                        <td>{{ $import->created_at->format('M d, Y') }}</td>
                        <td>{{ number_format($import->size, 2) }} kb</td>
                        <td>
                            <div class="flex justify-center">
                                <a href="/superadmin/view-import/{{ $import->id }}" class="text-blue-600 hover:underline">
                                    <img src="{{ asset('assets/view.svg') }}" alt="View">
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="bg-white py-4 px-6 border-t rounded-b-lg">
                {{ $imports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection