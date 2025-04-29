<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Filament\Resources\ProductResource\Pages\ListProducts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ProductOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return setting('app.stock_feature') ? 3 : 2;
    }

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();
        $stats = [];

        $stats[] = Stat::make(
            __('resource.product.widget.total_products'),
            $query->clone()->count()
        );
        if (setting('app.stock_feature', false)) {
            $stats[] = Stat::make(
                __('resource.product.widget.total_inventory'),
                Number::format($query->clone()->sum('stock') ?? 0, locale: 'id')
            );
        }
        $stats[] = Stat::make(
            __('resource.product.widget.average_price'),
            Number::currency($query->clone()->avg('price') ?? 0, 'IDR', 'id', 0)
        );

        return $stats;
    }
}
