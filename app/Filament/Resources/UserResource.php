<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $slug = 'management/employee';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Karyawan';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::all()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('resource.user.name'))
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->label(__('resource.user.role'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->maxItems(1)
                    ->preload(),
                Forms\Components\TextInput::make('email')
                    ->label(__('resource.user.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label(__('resource.user.password'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label(__('resource.user.password_confirmation'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->maxLength(255)
                    ->same('password')
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resource.user.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('resource.user.role'))
                    ->badge()
                    ->color(function ($state) {
                        $availableColors = ['primary', 'success', 'warning', 'danger', 'info'];

                        $hash = crc32($state);
                        $index = abs($hash) % count($availableColors);

                        return $availableColors[$index];
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('resource.user.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resource.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resource.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder(__('resource.user.search_placeholder'))
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('resource.user.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('resource.user.title');
    }
}
