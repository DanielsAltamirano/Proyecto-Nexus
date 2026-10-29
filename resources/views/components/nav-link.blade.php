@props(['active'])

@php
$classes = ($active ?? false)
            // Estilo cuando estás en esa página (Activo)
            ? 'inline-flex items-center px-4 py-2 rounded-xl bg-purple-50 text-purple-800 font-bold shadow-sm border border-purple-100 transition-all duration-200'
            // Estilo normal (Inactivo) con efecto hover de elevación
            : 'inline-flex items-center px-4 py-2 rounded-xl text-gray-500 font-medium border border-transparent hover:text-purple-700 hover:bg-gray-50 hover:shadow-sm transition-all duration-200 transform hover:-translate-y-0.5';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>