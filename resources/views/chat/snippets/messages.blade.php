@foreach ($group->messages()->get() as $message)
@if ($message->sender->id == auth()->user()->id)
<div class="self-end mb-2 max-w-96 ind-chat-msg">
    <p class="font-light text-base bg-blue-600 text-white p-3 rounded-lg rounded-br-none">{{ $message->content }}</p>
    <p class="text-[10px] -mt-1 text-right tracking-tight text-gray-400 !font-light">{{ date_create($message->created_at)->format('g:i A M. j, Y') }}</p>
</div>
@else
<div class="self-start mb-2 max-w-96">
    <p class="text-sm font-normal mb-1.5 text-black !tracking-tighter">{{ $message->sender->name }}</p>
    <p class="font-light text-base bg-gray-100 text-black p-3 rounded-lg rounded-bl-none">{{ $message->content }}</p>
    <p class="text-[10px] -mt-1 tracking-tight text-gray-400 !font-light">{{ date_create($message->created_at)->format('g:i A M. j, Y') }}</p>
</div>
@endif
@endforeach