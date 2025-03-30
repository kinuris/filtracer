@foreach ($group->messages()->get() as $message)
    @if ($message->sender->id == auth()->user()->id)
    <div class="self-end mb-2 max-w-80 ind-chat-msg">
        @if ($message->type === 'text')
        <p class="font-light text-base bg-blue-600 text-white p-3 rounded-lg rounded-br-none">{{ $message->content }}</p>
        @elseif ($message->isImage())
        <a href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
            <img class="rounded" src="{{ asset('storage/chat/files/' . $message->content) }}" alt="Photo">
        </a>
        @elseif ($message->isFile())
        <a href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
            <p class="font-normal text-sm bg-blue-600 underline rounded-br-none text-white p-3 rounded-lg">{{ $message->getFileName() }}</p>
        </a>
        @endif
        <p class="text-[10px] -mt-1 text-right tracking-tight text-gray-400 !font-light">{{ date_create($message->created_at)->format('g:i A M. j, Y') }}</p>
    </div>
    @else
    <div class="self-start mb-2 max-w-80">
        @if ($message->type === 'text')
        <p class="font-light text-base bg-gray-200 p-3 rounded-lg rounded-bl-none">{{ $message->content }}</p>
        @elseif ($message->isImage())
        <a href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
            <img class="rounded" src="{{ asset('storage/chat/files/' . $message->content) }}" alt="Photo">
        </a>
        @elseif ($message->isFile())
        <a href="{{ asset('storage/chat/files/' . $message->content) }}" target="_blank">
            <p class="font-normal text-sm bg-gray-200 underline rounded-bl-none p-3 rounded-lg">{{ $message->getFileName() }}</p>
        </a>
        @endif
        <p class="text-[10px] -mt-1 tracking-tight text-gray-400 !font-light">{{ date_create($message->created_at)->format('g:i A M. j, Y') }}</p>
    </div>
    @endif
@endforeach