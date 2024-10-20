@php($initiate = request('initiate'))
@if (is_numeric($initiate))
@php($user = App\Models\User::find($initiate))
@php($group = auth()->user()->chatGroupWith($user))
@else
@php($group = App\Models\ChatGroup::query()->where('internal_id', '=', urldecode($initiate))->first())
@endif

@if (isset($group))
<div id="seeImagesModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/2">
        <h2 class="text-lg text-left p-3 border-b">Images</h2>
        <div class="p-3 grid grid-cols-3 gap-3">
            @foreach ($group->messages->where('type', '!=', 'text') as $message)
            @if ($message->isImage())
            <a href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
                <img class="rounded min-h-full min-w-full object-cover" src="{{ asset('storage/chat/files/' . $message->content) }}" alt="Image">
            </a>
            @endif
            @endforeach
        </div>
        <button id="closeSeeImagesModal" class="m-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Back</button>
    </div>
</div>
@endif