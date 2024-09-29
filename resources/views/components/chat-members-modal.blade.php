<div id="chatMembersModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3 max-h-[60%] overflow-auto">
        <h2 class="text-lg font-bold p-4 border-b">Chat Members</h2>
        <div class="m-6">
            <form action="/chat/add/{{ urlencode(request('initiate')) }}" method="POST">
                @csrf
                <p>Add Members</p>
                <div class="flex">
                    <div class="flex-1">
                        <select multiple name="additions[]" id="additions"></select>
                    </div>
                    <button class="p-2 px-4 rounded bg-blue-600 text-white ml-2">Add</button>
                </div>
            </form>
        </div>

        @php($initiate = request('initiate'))
        @if (is_numeric($initiate))
        @php($user = App\Models\User::find($initiate))
        @php($group = auth()->user()->chatGroupWith($user))

        @else
        @php($group = App\Models\ChatGroup::query()->where('internal_id', '=', urldecode($initiate))->first())
        <div class="flex flex-col">
            @foreach ($group->users as $user)
            <div class="flex px-4 py-3 border-b border-gray-50 hover:bg-gray-100 place-items-center @if($loop->iteration == count($group->users)) rounded-b-lg @endif">
                <img class="w-10 h-10 mr-3 object-cover rounded-full" src="{{ $user->image() }}" alt="Profile">
                <p>{{ $user->name }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <button class="p-2 rounded-lg bg-blue-600 text-white m-4" id="closeChatMembersModal">Close</button>
    </div>
</div>