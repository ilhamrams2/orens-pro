@props([
    'padding' => 'p-6 sm:p-8',
    'overflow' => 'overflow-hidden',
])

<div {{ $attributes->merge(['class' => "bg-white rounded-[32px] border border-gray-100 shadow-sm $padding $overflow"]) }}>
    {{ $slot }}
</div>
