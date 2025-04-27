@props(['product'])

<li
    wire:click="$dispatch('add-to-cart', { productId: {{ $product->id }}})"
    wire:key="product-{{ $product->id }}"
    class="cursor-pointer border bg-primary-50 dark:bg-gray-950 rounded-lg shadow hover:shadow-md transition-shadow duration-200 ease-in-out overflow-hidden dark:border-gray-700 flex flex-col h-full"
>
    @if($product->image)
        <img src="{{ asset('/storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover">
    @else
        <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="1">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
            </svg>
        </div>
    @endif

    <div class="p-3 flex flex-col flex-grow">
        <h5 class="text-sm font-semibold tracking-tight truncate text-gray-900 dark:text-white mb-1 flex-grow">
            {{ $product->name }}
        </h5>
        <div class="flex items-center justify-between mt-auto">
            <span class="text-lg font-bold text-gray-900 dark:text-white">
                {{ Number::currency($product->price, 'IDR', 'id', 0) }}
            </span>
        </div>
    </div>
</li>
