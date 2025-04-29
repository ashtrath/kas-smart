<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Widgets\ProductOverview;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $slug = 'management/product';

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::all()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('resource.product.name'))
                                    ->unique(Product::class, ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('resource.product.price'))
                                    ->prefix('Rp.')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                                    ->stripCharacters('.')
                                    ->required(),
                                Forms\Components\FileUpload::make('image')
                                    ->label(__('resource.product.image'))
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('1:1')
                                    ->required()
                                    ->columnSpan('full'),
                                Forms\Components\MarkdownEditor::make('description')
                                    ->label(__('resource.product.description'))
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'strike',
                                        'blockquote',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'redo',
                                        'undo',
                                    ])
                                    ->columnSpan('full'),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('resource.product.section.inventory'))
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label(__('resource.product.is_visible.label'))
                                    ->helperText(fn (bool $state
                                    ): string => $state ? __('resource.product.is_visible.true') : __('resource.product.is_visible.false'))
                                    ->live(onBlur: true)
                                    ->default(true),
                                Forms\Components\Select::make('category_id')
                                    ->label(__('resource.product.category_id'))
                                    ->relationship('category', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('stock')
                                    ->label(__('resource.product.stock'))
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(setting('app.stock_feature', false))
                                    ->visible(setting('app.stock_feature', false)),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('resource.product.image')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resource.product.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('resource.product.category_id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resource.product.price'))
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label(__('resource.product.stock'))
                    ->sortable()
                    ->toggleable()
                    ->icon(fn (int $state) => (setting('app.stock_feature', false) && $state <= setting('app.min_stock_notification', 0)) ? 'heroicon-m-exclamation-triangle' : '')
                    ->iconColor('danger')
                    ->color(fn (int $state) => (setting('app.stock_feature', false) && $state <= setting('app.min_stock_notification', 0)) ? 'danger' : '')
                    ->visible(setting('app.stock_feature', false)),
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label(__('resource.product.is_visible.label'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resource.created_at'))
                    ->dateTime('d F Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resource.updated_at'))
                    ->dateTime('d F Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder(__('resource.product.search_placeholder'))
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label(__('resource.product.category_id'))
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label(__('resource.product.is_visible.label'))
                    ->native(false),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Stok Menipis')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '<=', setting('app.min_stock_notification', 0)))
                    ->visible(setting('app.stock_feature', false)),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\Group::make()
                                        ->schema([
                                            Infolists\Components\TextEntry::make('name')
                                                ->label(__('resource.product.name')),
                                            Infolists\Components\TextEntry::make('is_visible')
                                                ->label(__('resource.product.is_visible.label'))
                                                ->badge()
                                                ->getStateUsing(fn (Product $product): string => $product->is_visible ? 'Ya' : 'Tidak')
                                                ->color(fn (string $state): array => $state === 'Ya' ? Color::Green : Color::Red),
                                            Infolists\Components\TextEntry::make('category.name')
                                                ->label(__('resource.product.category_id'))
                                                ->badge(),
                                        ]),
                                    Infolists\Components\Group::make()
                                        ->schema([
                                            Infolists\Components\TextEntry::make('price')
                                                ->label(__('resource.product.price'))
                                                ->money('IDR'),
                                            Infolists\Components\TextEntry::make('stock')
                                                ->label(__('resource.product.stock'))
                                                ->icon(fn (int $state) => (setting('app.stock_feature', false) && $state <= setting('app.min_stock_notification', 0)) ? 'heroicon-m-exclamation-triangle' : '')
                                                ->iconColor('danger')
                                                ->visible(setting('app.stock_feature', false)),
                                        ]),
                                ]),
                            Infolists\Components\ImageEntry::make('image')
                                ->label(__('resource.product.image'))
                                ->hiddenLabel()
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                Infolists\Components\Section::make(__('resource.product.description'))
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('resource.product.description'))
                            ->hiddenLabel()
                            ->markdown()
                            ->prose()
                            ->visible(fn (Product $record) => ! empty($record->description)),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('resource.product.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('resource.product.title');
    }
}
