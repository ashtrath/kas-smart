<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Filament\Resources\ProductResource\Pages\ListProducts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('resource.product.widget.total_products'), $this->getPageTableQuery()->count()),
            Stat::make(__('resource.product.widget.total_inventory'), $this->getPageTableQuery()->sum('stock')),
            Stat::make(__('resource.product.widget.average_price'), 'Rp ' . number_format($this->getPageTableQuery()->avg('price'), 2, ',', '.')),
        ];
    }
}
