<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    use HasPageShield;

    protected static ?string $navigationGroup = 'Pengaturan';

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('Toko')
                        ->icon('heroicon-m-building-storefront')
                        ->columns(2)
                        ->schema([
                            TextInput::make('store.name')
                                ->label('Nama Toko')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            FileUpload::make('store.logo')
                                ->label('Logo Toko')
                                ->directory('settings')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('200')
                                ->imageResizeTargetHeight('200'),
                            TextArea::make('store.address')
                                ->label('Alamat Toko')
                                ->rows(3)
                                ->maxLength(500),
                            TextInput::make('store.phone')
                                ->label('Nomor Telepon Toko')
                                ->tel()
                                ->maxLength(20),
                            Textinput::make('store.email')
                                ->label('Email Toko')
                                ->email()
                                ->maxLength(255),
                        ]),
                    Tabs\Tab::make('Aplikasi')
                        ->icon('heroicon-m-cog')
                        ->columns(2)
                        ->schema([
                            Fieldset::make('stock_feature')
                                ->label('Fitur Stock')
                                ->schema([
                                    Toggle::make('app.stock_feature')
                                        ->label('Aktifkan?')
                                        ->live(),
                                    TextInput::make('app.min_stock_notification')
                                        ->label('Notifikasi Stok Minimal')
                                        ->numeric()
                                        ->minValue(0)
                                        ->visible(fn (Get $get) => $get('app.stock_feature') === true),
                                ]),
                            Fieldset::make('tax_feature')
                                ->label('Fitur Pajak')
                                ->schema([
                                    Toggle::make('app.tax_feature')
                                        ->label('Aktifkan?')
                                        ->live(),
                                    TextInput::make('app.tax_rate')
                                        ->label('Jumlah Pajak')
                                        ->prefix('%')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->visible(fn (Get $get) => $get('app.tax_feature') === true),
                                ]),
                        ]),
                ]),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Pengaturan',
            'Pengaturan Aplikasi',
        ];
    }
}
