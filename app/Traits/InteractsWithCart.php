<?php

namespace App\Traits;

use App\Models\CartItem;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

trait InteractsWithCart
{
    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $auth = Filament::auth()->id();

        $cartItem = CartItem::firstOrNew([
            'product_id' => $productId,
            'user_id' => $auth,
        ]);
        $cartItem->quantity++;

        if (! $this->validateStock($product, $cartItem->quantity)) {
            return;
        }

        $cartItem->price = $product?->price * $cartItem->quantity;
        $cartItem->save();

        $this->refreshCart();
    }

    public function reduceCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $cartItem = CartItem::whereProductId($productId)
            ->cashier()
            ->first();
        $qty = $cartItem->quantity - 1;
        if ($qty == 0) {
            $cartItem->delete();
            $this->refreshCart();

            return;
        }
        $price = $product?->price * ($qty);
        $cartItem->fill([
            'quantity' => $qty,
            'price' => $price,
        ]);
        $cartItem->save();
        $this->refreshCart();
    }

    public function deleteCart(int $cartItemId): void
    {
        $cartItem = CartItem::find($cartItemId);
        $cartItem->delete();
        $this->refreshCart();
    }

    public function updateCart(int $cartItemId, $value): void
    {
        $cartItem = CartItem::find($cartItemId);
        if ((int) $value === 0) {
            $cartItem->delete();
            $this->refreshCart();

            return;
        }
        $value = $value !== '' ? $value : 0;
        if ($cartItem->product->qty <= $value) {
            Notification::make()
                ->title(__('Stok produk tidak mencukupi!'))
                ->danger()
                ->send();
            $this->refreshCart();

            return;
        }
        $price = $cartItem->product->price * ((int) $value);
        $cartItem->fill([
            'quantity' => $value,
            'price' => $price,
        ]);
        $cartItem->save();
        $this->refreshCart();
    }

    public function clearCart(): void
    {
        CartItem::query()
            ->cashier()
            ->delete();

        Notification::make()
            ->title(__('Keranjang telah dibersihkan.'))
            ->success()
            ->send();
        $this->refreshCart();
    }

    private function validateStock(Product $product, int $quantity): bool
    {
        if ($product->stock <= 0 || $product->stock < $quantity) {
            Notification::make()
                ->title(__('Stok produk tidak mencukupi!'))
                ->danger()
                ->send();
            $this->refreshCart();

            return false;
        }

        return true;
    }
}
