<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrderWidget extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terakhir';

    protected static ?int $sort = 3;

    protected static ?int $defaultSortTableRecordsPerPage = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Sale::query()
            ->with(['user:id,name', 'paymentMethod:id,name'])
            ->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Tanggal Transaksi')
                ->date()
                ->sortable(),
            TextColumn::make('id')
                ->label('ID Pesanan')
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Kasir')
                ->searchable()
                ->sortable(),
            TextColumn::make('paymentMethod.name')
                ->label('Metode Bayar')
                ->badge()
                ->searchable()
                ->sortable(),
            TextColumn::make('total')
                ->label('Total')
                ->money('IDR')
                ->sortable(),
            TextColumn::make('tax')
                ->label('Pajak')
                ->money('IDR')
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        // If you have a SaleResource, you can link to the view page
        return [
            Tables\Actions\ViewAction::make(),
            // Uncomment and adjust if you have a SaleResource
            // ->url(fn (Sale $record): string => SaleResource::getUrl('view', ['record' => $record])),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getEmptyStateHeading(): ?string
    {
        return 'Belum ada transaksi';
    }
}
