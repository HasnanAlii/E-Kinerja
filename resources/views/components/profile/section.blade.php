@props(['title', 'desc'])

<div class="border-t border-gray-200 pt-6 space-y-6">
    <header>
        <h2 class="text-lg font-bold text-green-900">{{ $title }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
    </header>

    {{ $slot }}
</div>
