@props(['id', 'label'])

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
    <div>
        <x-input-label for="{{ $id }}" :value="$label" />
    </div>
    <div class="md:col-span-2">
        {{ $slot }}
        <x-input-error :messages="$errors->get($id)" class="mt-2" />
    </div>
</div>
