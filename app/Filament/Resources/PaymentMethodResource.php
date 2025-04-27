<?php

namespace App\Filament\Resources;

use App\Enum\PaymentMethodType;
use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $slug = 'management/payment-methods';

    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('resource.payment_method.name'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label(__('resource.payment_method.type'))
                            ->options(PaymentMethodType::class)
                            ->default(PaymentMethodType::CASH)
                            ->required()
                            ->native(false),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('resource.created_at'))
                            ->content(fn (?PaymentMethod $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label(__('resource.updated_at'))
                            ->content(fn (?PaymentMethod $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resource.payment_method.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resource.payment_method.type'))
                    ->badge()
                    ->colors([
                        'primary',
                        'secondary',
                        'success',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resource.created_at'))
                    ->dateTime('d F Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resource.updated_at'))
                    ->dateTime('d F Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('resource.payment_method.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('resource.payment_method.title');
    }
}
