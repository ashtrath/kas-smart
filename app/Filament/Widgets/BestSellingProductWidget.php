<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BestSellingProductWidget extends BaseWidget
{
    protected static ?string $heading = 'Produk Terlaris Hari Ini';

    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        $startDate = today()->startOfDay();
        $endDate = today()->endOfDay();

        return SaleItem::query()
            ->select([
                DB::raw('MIN(sale_items.id) as id'),
                'sale_items.product_id',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
            ])
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('sale_items.product_id')
            ->orderByDesc('total_qty')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('product.name')
                ->label('Nama Produk')
                ->sortable(false),
            TextColumn::make('total_qty')
                ->label('Jumlah Terjual')
                ->numeric()
                ->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getEmptyStateHeading(): ?string
    {
        return 'Belum ada produk terjual hari ini';
    }
}
