@foreach (auth()->user()->alerts()->latest()->get() as $alert)
<a href="{{ $alert->action }}" class="flex flex-col px-3 py-1 border-b">
    <div class="flex justify-between mb-1">
        <p class="text-sm max-w-[calc(100%-3rem)]">{{ $alert->title }}</p>
        <p class="text-[8px] text-blue-600 font-semibold">{{ $alert->created_at->diffForHumans() }}</p>
    </div>
    <p class="text-xs text-gray-400 line-clamp-1">{{ $alert->content }}</p>
</a>
@endforeach