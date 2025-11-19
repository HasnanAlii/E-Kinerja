@if ($paginator->hasPages())
    <div class="px-6 py-4 flex items-center justify-between border-t border-gray-100 bg-white">

        {{-- MOBILE VIEW --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-default">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition">
                    Selanjutnya <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-default">
                    Selanjutnya <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                </span>
            @endif
        </div>

        {{-- DESKTOP VIEW --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            
            {{-- Info Data --}}
            <div>
                <p class="text-sm text-gray-500">
                    Menampilkan
                    <span class="font-bold text-indigo-600">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-bold text-indigo-600">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-bold text-indigo-600">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            {{-- Tombol Navigasi --}}
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm gap-1" aria-label="Pagination">
                    
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-default">
                            <i data-feather="chevron-left" class="w-4 h-4"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition duration-150" aria-label="Previous">
                            <i data-feather="chevron-left" class="w-4 h-4"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="inline-flex items-center justify-center w-9 h-9 text-gray-400">...</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    {{-- Active State --}}
                                    <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-200 ring-1 ring-indigo-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    {{-- Inactive State --}}
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition duration-150">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition duration-150" aria-label="Next">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-default">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </div>
@endif