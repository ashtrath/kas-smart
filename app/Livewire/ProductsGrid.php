<?php

namespace App\Livewire;

use App\Models\Product;
use App\Traits\InteractsWithCart;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProductsGrid extends Component
{
    use InteractsWithCart;

    public string $search = '';

    public function render(): View
    {
        $products = Product::query()
            ->isVisible()
            ->search($this->search)
            ->get();

        return view('filament.partials.products-grid', ['products' => $products]);
    }
}
