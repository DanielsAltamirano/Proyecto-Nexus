@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Mobile: solo anterior / siguiente --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition-all duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition-all duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop: info izquierda + controles derecha --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between gap-4">

            {{-- Izquierda: texto informativo --}}
            <p class="text-sm text-gray-500 leading-5">
                Mostrando
                @if ($paginator->firstItem())
                    <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span>
                    –
                    <span class="font-bold text-gray-800">{{ $paginator->lastItem() }}</span>
                @else
                    <span class="font-bold text-gray-800">{{ $paginator->count() }}</span>
                @endif
                de
                <span class="font-bold text-gray-800">{{ $paginator->total() }}</span>
                registros
            </p>

            {{-- Derecha: controles de paginación --}}
            <div class="flex items-center gap-1">

                {{-- Botón Anterior --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-300 bg-white border border-gray-200 cursor-not-allowed select-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-600 bg-white border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition-all duration-150 shadow-sm"
                       aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                @endif

                {{-- Números de página --}}
                @foreach ($elements as $element)
                    {{-- Separador "..." --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Páginas numéricas --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold text-white bg-green-600 border border-green-600 shadow-md select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-medium text-gray-700 bg-white border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition-all duration-150 shadow-sm"
                                   aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Botón Siguiente --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-600 bg-white border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition-all duration-150 shadow-sm"
                       aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-300 bg-white border border-gray-200 cursor-not-allowed select-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                @endif

            </div>
        </div>
    </nav>
@endif
