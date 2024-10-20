@php($initiate = request('initiate'))
@if (is_numeric($initiate))
@php($user = App\Models\User::find($initiate))
@php($group = auth()->user()->chatGroupWith($user))
@else
@php($group = App\Models\ChatGroup::query()->where('internal_id', '=', urldecode($initiate))->first())
@endif

@if (isset($group))
<div id="seeFilesModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/2">
        <h2 class="text-lg text-left p-3 border-b">Files</h2>
        <div class="p-3 grid grid-cols-3 gap-3">
            @foreach ($group->messages->where('type', '!=', 'text') as $message)
            @if ($message->isFile())
            <div class="border rounded-lg">
                <a class="text-sm flex p-2 place-items-center" href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
                    <img src="{{ asset('assets/file.svg') }}" class="mr-2" alt="File">
                    {{ $message->getFileName() }}
                </a>
            </div>
            @endif
            @endforeach
        </div>
        <button id="closeSeeFilesModal" class="m-2 mt-0 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Back</button>
    </div>
</div>
@endif