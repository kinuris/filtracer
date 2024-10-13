@foreach (auth()->user()->alerts()->latest()->get() as $alert)
<a href="{{ $alert->action }}" class="flex flex-col px-3 py-1 border-b">
    <div class="flex justify-between mb-1">
        @if ($alert->is_read)
        <p class="text-sm max-w-[calc(100%-3rem)] text-gray-400">{{ $alert->title }}</p>
        @else
        <p class="text-sm max-w-[calc(100%-3rem)] font-semibold">{{ $alert->title }}</p>
        @endif
        <p class="text-[8px] text-blue-600 font-semibold">{{ $alert->created_at->diffForHumans() }}</p>
    </div>
    <p class="text-xs text-gray-400 line-clamp-1">{{ $alert->content }}</p>
</a>
@endforeach