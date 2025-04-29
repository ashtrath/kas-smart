@props(['product'])

@php
    $stockFeatureEnabled = setting('app.stock_feature', false);
    $lowStockThreshold = setting('app.min_stock_notification', 0);
    $isLowStock = $stockFeatureEnabled && $product->stock <= $lowStockThreshold;
@endphp

<li
    wire:click="$dispatch('add-to-cart', { productId: {{ $product->id }}})"
    wire:key="product-{{ $product->id }}"
    class="group flex flex-col h-full cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow duration-200 ease-in-out hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
>
    <div class="relative aspect-video overflow-hidden">
        @if($product->image)
            <img
                src="{{ asset('storage/'.$product->image) }}"
                alt="{{ $product->name }}"
                class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
            >
        @else
            <div class="flex size-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                <svg class="size-10 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                </svg>
            </div>
        @endif
    </div>

    <div
        class="flex flex-1 flex-col p-4">
        <h5 class="text-sm font-semibold tracking-tight text-gray-900 dark:text-white">
            <span title="{{ $product->name }}" class="block truncate">{{ $product->name }}</span>
        </h5>

        <div class="mt-auto">
            <div class="mb-1">
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ Number::currency($product->price, 'IDR', 'id', 0) }}
                </span>
            </div>

            @if($stockFeatureEnabled)
                <div @class([
                    'flex items-center gap-1 text-xs',
                    'text-gray-500 dark:text-gray-400' => !$isLowStock,
                    'font-semibold text-red-600 dark:text-red-400' => $isLowStock,
                ])>
                    @if($isLowStock)
                        <x-heroicon-m-exclamation-triangle class="size-4 shrink-0"/>
                        <span>Stok Menipis:</span>
                    @else
                        <span>Stok:</span>
                    @endif
                    <span>{{ number_format($product->stock) }}</span>
                </div>
            @endif
        </div>
    </div>
</li>
