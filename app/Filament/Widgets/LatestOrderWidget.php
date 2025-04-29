<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        return [
            Tables\Actions\ViewAction::make()
                ->infolist(function (Infolist $infolist, Model $record): Infolist {
                    return $infolist
                        ->schema([
                            Section::make('Detail Transaksi')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('id')
                                        ->label('ID Pesanan'),
                                    TextEntry::make('created_at')
                                        ->label('Tanggal Transaksi')
                                        ->dateTime('d M Y, H:i:s'),
                                    TextEntry::make('user.name')
                                        ->label('Kasir'),
                                    TextEntry::make('paymentMethod.name')
                                        ->label('Metode Bayar')
                                        ->badge(),
                                    TextEntry::make('subtotal')
                                        ->label('Subtotal')
                                        ->money('IDR'),
                                    TextEntry::make('tax')
                                        ->label('Pajak')
                                        ->money('IDR'),
                                    TextEntry::make('total')
                                        ->label('Total')
                                        ->money('IDR'),
                                    TextEntry::make('total_quantity')
                                        ->label('Total Kuantitas Produk')
                                        ->state(fn (Sale $record): int => $record->items->sum('quantity')),
                                ]),
                            Section::make('Detail Produk Dibeli')
                                ->schema([
                                    RepeatableEntry::make('items')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->columns(3)
                                        ->schema([
                                            TextEntry::make('product.name')
                                                ->label('Produk')
                                                ->columnSpan(1),
                                            TextEntry::make('quantity')
                                                ->label('Jumlah')
                                                ->numeric()
                                                ->columnSpan(1),
                                            TextEntry::make('price_at_sale')
                                                ->label('Harga Satuan')
                                                ->money('IDR')
                                                ->columnSpan(1),
                                            TextEntry::make('subtotal')
                                                ->label('Subtotal')
                                                ->money('IDR')
                                                ->columnSpan(3),
                                        ]),
                                ]),
                        ]);
                })
                ->modalHeading(fn (Model $record): string => "Detail Transaksi #{$record->id}"),
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
