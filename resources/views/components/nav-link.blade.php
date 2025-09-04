{{-- resources/views/components/nav-link.blade.php --}}
@props(['active' => false])

@php
    $classes = 'inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition ' .
               ($active ? 'text-gray-900 bg-white' : 'text-gray-600 hover:text-gray-800');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if($active) aria-current="page" @endif>
    {{ $slot }}
</a>
