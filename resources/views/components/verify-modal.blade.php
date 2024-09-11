@php($id = request('verify_modal'))

@if($id)
@php($user = App\Models\User::find($id))
<div id="verifyModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4 flex place-items-center">
            {{ $user->role }} Details
            <span class="material-symbols-outlined ml-6">
                edit
            </span>
        </h2>

        <p>Full Name</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->name }}</p>

        <p class="mt-3">Username</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->username }}</p>

        <p class="mt-3">Email</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->email_address }}</p>

        <p class="mt-3">Contact Number</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->phone_number }}</p>

        <p class="mt-3">Student ID</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->student_id }}</p>

        <p class="mt-3">Course</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->getCourse()->name }}</p>

        <p class="mt-3">Batch</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->start }} - {{ $user->getEducationalBio()->end }}</p>

        <div class="mt-4 flex">
            <button type="button" id="closeVerifyModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
            <a class="bg-blue-500 text-white px-4 py-2 rounded" href="/admin/useraccount/verify/{{ $user->id }}">Verify</a>
        </div> 
    </div>
</div>
@endif