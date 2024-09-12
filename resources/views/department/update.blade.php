@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Department Settings</h1>
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-4 flex place-items-center rounded-lg">
            <form enctype="multipart/form-data" action="/settings/department/update/{{ $department->id }}" method="POST" class="w-full">
                @csrf
                <div class="flex flex-col">
                    <label for="name">Department Name</label>
                    <input value="{{ $department->name }}" name="name" class="mt-1 p-2 rounded border" placeholder="Department Name" type="text">
                </div>

                <div class="flex flex-col mt-4">
                    <label for="logo">Logo</label>
                    <img class="max-w-32 aspect-square object-cover rounded-full shadow-lg" src="{{ asset('storage/departments/'. $department->logo) }}" id="preview" alt="Logo">
                    <input class="mt-3" type="file" name="logo" id="logo">
                </div>

                <div class="flex justify-end mt-6">
                    <a href="/settings/department" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</a>
                    <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const upload = document.getElementById('logo');

    upload.addEventListener('change', () => {
        const file = upload.files[0];
        const preview = document.getElementById('preview');

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.addEventListener('load', () => {
            preview.src = reader.result;
        });
    });
</script>
@endsection