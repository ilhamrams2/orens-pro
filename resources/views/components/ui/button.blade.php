@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
])

@php
    $baseClasses = 'font-bold transition-all flex items-center justify-center gap-2 shadow-lg';
    
    $variants = [
        'primary' => 'bg-orens text-white hover:bg-orens-light shadow-orens/20',
        'secondary' => 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 shadow-red-200',
        'success' => 'bg-green-500 text-white hover:bg-green-600 shadow-green-200',
        'outline' => 'bg-transparent border-2 border-gray-100 text-gray-600 hover:bg-gray-50 shadow-none',
        'ghost' => 'bg-transparent text-gray-400 hover:text-gray-600 shadow-none',
    ];

    $sizes = [
        'xs' => 'px-3 py-1.5 text-[10px] rounded-lg',
        'sm' => 'px-4 py-2 text-xs rounded-xl',
        'md' => 'px-5 py-3 text-sm rounded-xl',
        'lg' => 'px-8 py-4 text-base rounded-2xl',
    ];

    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) {{ $icon }} @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) {{ $icon }} @endif
        <span>{{ $slot }}</span>
    </button>
@endif
