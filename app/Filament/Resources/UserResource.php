<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Kullanıcılar';

    protected static ?string $modelLabel = 'Kullanıcı';

    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Hesap Bilgileri')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Ad Soyad')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->label('Şifre')
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Rol Yetkileri')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Roller')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roller')
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Pasif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
                Tables\Actions\DeleteAction::make()->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
