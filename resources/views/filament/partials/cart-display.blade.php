@if (!empty($cart))
    <ul class="divide-y divide-gray-200 overflow-y-auto h-full">
        @foreach ($cart as $productId => $item)
            <li
                wire:key="cart-item-{{ $item['product']['id'] }}"
                class="grid grid-cols-[1fr_auto] items-center gap-x-4 py-2"
            >
                <div class="min-w-0 overflow-hidden">
                    <h3 class="truncate font-semibold text-sm">
                        {{ $item['product']['name'] }}
                    </h3>
                    <p class="font-medium text-muted-foreground text-xs">
                        {{ Number::currency($item['price'], 'IDR', 'id', 0) }}
                    </p>
                </div>
                <div
                    class="flex items-center divide-gray-200 divide-x overflow-hidden rounded-md border border-gray-200">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium text-sm transition-colors focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 size-9"
                        wire:click="reduceCart({{ $item['product']['id'] }})"
                    >
                        <x-heroicon-m-minus/>
                    </Button>
                    <span
                        class="flex h-9 w-12 px-3 py-1 font-medium text-sm items-center justify-center">{{ $item['quantity'] }}</span>
                    <button
                        type="button"
                        class="inline-flex bg-primary-600 text-white items-center justify-center gap-2 whitespace-nowrap font-medium text-sm transition-colors focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 size-9"
                        wire:click="addToCart({{ $item['product']['id'] }})"
                    >
                        <x-heroicon-m-plus/>
                    </button>
                </div>
            </li>
            </li>
        @endforeach
    </ul>
@else
    <div class="text-center text-gray-500 dark:text-gray-400 py-4 h-full">
        Keranjang kosong. Silahkan tambah produk terlebih dahulu.
    </div>
@endif
