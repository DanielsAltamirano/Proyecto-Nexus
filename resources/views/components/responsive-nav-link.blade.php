@props(['active'])

@php
$classes = ($active ?? false)
            // Estilo activo para el menú lateral/móvil
            ? 'block w-full ps-4 pe-4 py-3 border-l-4 border-green-700 text-start text-base font-bold text-green-800 bg-green-50 transition-all duration-200 shadow-inner'
            // Estilo inactivo para el menú lateral/móvil
            : 'block w-full ps-4 pe-4 py-3 border-l-4 border-transparent text-start text-base font-medium text-gray-500 hover:text-green-700 hover:bg-gray-50 hover:border-green-300 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>