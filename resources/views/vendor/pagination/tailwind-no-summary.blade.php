@if ($paginator->hasPages())
    @php
        $mobileLinkClass = 'inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-100 leading-5 rounded-md hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring ring-blue-200 focus:border-blue-300 active:bg-blue-50 active:text-blue-800 transition ease-in-out duration-150';
        $mobileDisabledClass = 'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-blue-100 cursor-not-allowed leading-5 rounded-md';

        $desktopArrowClass = 'inline-flex items-center px-2 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-100 leading-5 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring ring-blue-200 focus:border-blue-300 active:bg-blue-50 active:text-blue-800 transition ease-in-out duration-150';
        $desktopArrowDisabledClass = 'inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-blue-100 cursor-not-allowed leading-5';

        $pageLinkClass = 'inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-blue-700 bg-white border border-blue-100 leading-5 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring ring-blue-200 focus:border-blue-300 active:bg-blue-50 active:text-blue-800 transition ease-in-out duration-150';
        $pageCurrentClass = 'inline-flex items-center px-4 py-2 -ml-px text-sm font-semibold text-blue-800 bg-white border border-blue-300 cursor-default leading-5';
        $separatorClass = 'inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-600 bg-white border border-blue-100 cursor-default leading-5';
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span class="{{ $mobileDisabledClass }}">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $mobileLinkClass }}">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $mobileLinkClass }}">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="{{ $mobileDisabledClass }}">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        <div class="hidden sm:flex sm:justify-end">
            <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">

                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="{{ $desktopArrowDisabledClass }} rounded-l-md" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $desktopArrowClass }} rounded-l-md" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="{{ $separatorClass }}">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="{{ $pageCurrentClass }}">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="{{ $pageLinkClass }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $desktopArrowClass }} -ml-px rounded-r-md" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="{{ $desktopArrowDisabledClass }} -ml-px rounded-r-md" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
