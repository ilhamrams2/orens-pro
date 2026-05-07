@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4']) }}>
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-800 font-outfit tracking-tight">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-xs sm:text-sm text-gray-500 font-medium">{{ $subtitle }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
