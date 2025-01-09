@php($id = request('unverify_modal'))

@if($id)
@php($user = App\Models\User::find($id))
<div id="unverifyModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 max-h-[max(75%,450px)] overflow-auto">
        <h2 class="text-lg font-bold mb-4 flex place-items-center">
            {{ $user->role }} Details
            <span class="material-symbols-outlined ml-6">
                edit
            </span>
        </h2>

        <img class="w-36 h-36 object-cover rounded-full mb-3 shadow" src="{{ $user->image() }}" alt="">

        <p>Full Name</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->name }}</p>

        <p class="mt-3">Username</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->username }}</p>

        @if ($user->role != 'Admin')
         <p class="mt-3">Email</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->email_address }}</p>

        <p class="mt-3">Contact Number</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->phone_number }}</p>   
        @else
          <p class="mt-3">Email</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->email_address }}</p>

        <p class="mt-3">Contact Number</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->phone_number }}</p>  
        @endif

        @if ($user->role != 'Admin')
        <p class="mt-3">Student ID</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->student_id }}</p>

        <p class="mt-3">Course</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->getCourse()->name }}</p>

        <p class="mt-3">Batch</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->start }} - {{ $user->getEducationalBio()->end }}</p>
        @else
        <p class="mt-3">Position ID</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->adminRelation->position_id }}</p>

        <p class="mt-3">Office</p>
        <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->adminRelation->officeRelation->name }}</p>
        @endif

        <div class="mt-4 flex">
            <button type="button" id="closeUnverifyModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
            <a class="bg-blue-500 text-white px-4 py-2 rounded" href="/admin/useraccount/unverify/{{ $user->id }}">Unverify</a>
        </div>
    </div>
</div>
@endif