<div id="leaveGroupModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <img class="w-12 mx-auto" src="{{ asset('assets/leave.svg') }}" alt="Leave Group">
        <h2 class="text-lg font-semibold text-center my-6">Are you sure you want to leave this group?</h2>
        <form class="flex justify-center" action="/chat/leave/{{ urlencode(request('initiate')) }}" method="POST">
            @csrf
            <button type="button" id="closeLeaveGroupModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">No, Cancel</button>
            <button type="submit" class="mr-2 px-4 py-2 bg-blue-600 text-white rounded">Yes, I'm Sure</button>
        </form>
    </div>
</div>