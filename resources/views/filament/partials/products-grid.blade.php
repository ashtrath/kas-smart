<div>
    <x-filament-tables::search-field
        wire-model="search"
        debounce="300"
        placeholder="Cari (nama produk)"
    />

    <ul class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 mt-4 gap-4">
        @forelse ($products as $product)
            @include('filament.partials.product-card', ['product' => $product])
        @empty
            <p class="text-gray-500 mt-4 col-span-full text-center">Tidak dapat menemukan produk.</p>
        @endforelse
    </ul>
</div>
