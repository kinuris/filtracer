@extends('layouts.setup')

@section('header')
<style>
    input::-webkit-file-upload-button {
        display: none;
    }
</style>
@endsection
@section('title', 'Profile Picture')

@section('content')

<div class="flex place-items-start h-full justify-center max-h-[calc(100vh-5rem)] pb-10 overflow-auto">
    <div class="shadow-lg bg-white mt-12 w-[60%] p-2 rounded-lg flex flex-col">
        <div class="flex gap-2">
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-blue-600"></div>
        </div>

        <h1 class="text-2xl font-semibold tracking-wider text-center mt-5">Complete your Profile</h1>
        <p class="text-blue-600 font-bold mt-2 text-xl text-center">Set Profile Picture</p>

        <div class="flex justify-center my-8">
            <img id="profile_img" class="w-24 h-24 shadow-lg rounded-full object-cover" src="{{ asset('assets/default_user.svg') }}" alt="Default User">
        </div>

        <form class="mx-8 flex flex-col" action="/alumni/setup/profilepic/{{ auth()->user()->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="profile_pic">
                <div class="block">
                    <p class="underline text-blue-600">Upload File</p>
                </div>
            </label>
            <input class="w-fit" type="file" name="profile_picture" id="profile_pic" accept="image/jpg,image/jpeg,image/png">

            <button class="p-2 self-end mt-4 bg-blue-600 text-white rounded" type="submit">Save & Finish</button>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    const profilePic = document.getElementById('profile_pic');

    profilePic.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.onload = () => {
            document.querySelector('#profile_img').src = reader.result;
        }
    });
</script>
@endsection