<div>
    @php
    $acceptedGroups = collect();
    $pendingGroups = collect();
    $isAdmin = Auth::user()->role === 'Admin';
    $baseUrl = $isAdmin ? '/admin/chat' : '/alumni/chat';

    foreach (auth()->user()->chatGroups()->get() as $group) {
    // Add the latest message to each group for sorting purposes
    $latestMessage = $group->messages()->latest()->first();
    $group->latestMessage = $latestMessage;
    $group->latestMessageTime = $latestMessage ? $latestMessage->created_at : null;

    $associations = $group->associations;
    if ($associations->where('user_id', '=', Auth::user()->id)->where('status', '=', 'pending')->count() > 0) {
    $pendingGroups->push($group);
    } else {
    $acceptedGroups->push($group);
    }
    }

    // Sort accepted groups by latest message timestamp (newest first)
    $acceptedGroups = $acceptedGroups->sortByDesc(function($group) {
    return $group->latestMessageTime;
    });
    @endphp

    @foreach ($acceptedGroups as $group)
    @php
    $associations = $group->associations
    @endphp
    <a href="{{ $baseUrl }}?initiate={{ $group->initiateLink() }}" data-latest="{{ $group->latestMessage->created_at }}" class="flex place-items-center p-2 hover:bg-gray-100">
        <img class="w-12 h-12 object-cover rounded-full shadow mr-3.5" src="{{ $group->image() }}" alt="Group">
        <div class="flex flex-col">
            <p class="text-sm font-normal">{{ $group->name }}</p>
            @php
            $first = $group->latestMessage;
            @endphp
            @if(!is_null($first))
            @if ($first->isImage())
            <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}} Sent an image</p>
            @elseif ($first->isFile())
            <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}} Sent a file</p>
            @else
            <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}}{{ $first ? $first->content : 'No Messages Yet' }}</p>
            @endif
            @endif
        </div>
    </a>
    @endforeach

    @if($pendingGroups->count() > 0)
    <div class="w-full py-2 px-3 bg-gray-50 text-xs text-gray-500 font-medium">
        Pending Invitations
    </div>
    @endif

    @foreach ($pendingGroups as $group)
    @php
    $association = $group->associations->where('user_id', '=', Auth::user()->id)->first()
    @endphp
    <div class="flex place-items-center p-2 hover:bg-gray-100" data-latest="{{ $group->latestMessage->created_at }}">
        <a class="flex place-items-center" href="{{ $baseUrl }}?initiate={{ $group->initiateLink() }}">
            <img class="w-12 h-12 object-cover rounded-full shadow mr-3.5" src="{{ $group->image() }}" alt="Group">
            <div class="flex flex-col">
                <p class="text-sm font-normal">{{ $group->name }}</p>
                @php
                $first = $group->messages()->latest()->first()
                @endphp
                @if(!is_null($first))
                @if ($first->isImage())
                <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}} Sent an image</p>
                @elseif ($first->isFile())
                <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}} Sent a file</p>
                @else
                <p class="text-xs font-light text-gray-400 line-clamp-1 max-w-48">{{ $first && $first->sender->id == auth()->user()->id ? 'You: ' : ''}}{{ $first ? $first->content : 'No Messages Yet' }}</p>
                @endif
                @endif
            </div>
        </a>
        <div class="ml-auto relative">
            <form action="{{ route('chat.accept', $association->id) }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded shadow hover:bg-indigo-700 transition-all duration-200 flex items-center">
                    <span>Accept</span>
                </button>
            </form>
            <div class="absolute hidden group-hover:block bg-white text-gray-700 text-xs rounded-md p-2.5 w-52 right-0 -top-14 z-10 shadow-lg border border-gray-100">
                <p class="font-normal">Accept invitation to join this chat group</p>
                <div class="absolute w-3 h-3 bg-white transform rotate-45 -bottom-1.5 right-4 border-r border-b border-gray-100"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>