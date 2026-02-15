<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Leads\Models\Lead;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Leads';
    
    protected static ?string $modelLabel = 'Lead';
    
    protected static ?string $pluralModelLabel = 'Leads';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('İletişim Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                'corporate_quote' => 'Kurumsal Teklif',
                                'courier_apply' => 'Kurye Başvuru',
                                'contact' => 'İletişim',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('İsim')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Firma Adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('Mesaj')
                            ->rows(3)
                            ->maxLength(2000),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Durum')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'new' => '🆕 Yeni',
                                'contacted' => '📞 İletişime Geçildi',
                                'qualified' => '✅ Uygun',
                                'lost' => '❌ Kayıp',
                                'won' => '🎉 Kazanıldı',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(3),
                    ])->columns(1),
                    
                Forms\Components\Section::make('Kaynak Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('source')
                            ->label('Kaynak (utm_source)')
                            ->disabled(),
                        Forms\Components\TextInput::make('medium')
                            ->label('Ortam (utm_medium)')
                            ->disabled(),
                        Forms\Components\TextInput::make('campaign')
                            ->label('Kampanya (utm_campaign)')
                            ->disabled(),
                        Forms\Components\TextInput::make('page_url')
                            ->label('Sayfa URL')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tip')
                    ->colors([
                        'primary' => 'corporate_quote',
                        'success' => 'courier_apply',
                        'gray' => 'contact',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'corporate_quote' => 'Kurumsal',
                        'courier_apply' => 'Kurye',
                        'contact' => 'İletişim',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('İsim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Firma')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'gray' => 'new',
                        'warning' => 'contacted',
                        'success' => 'qualified',
                        'danger' => 'lost',
                        'primary' => 'won',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yeni',
                        'contacted' => 'İletişimde',
                        'qualified' => 'Uygun',
                        'lost' => 'Kayıp',
                        'won' => 'Kazanıldı',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Kaynak')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        'corporate_quote' => 'Kurumsal',
                        'courier_apply' => 'Kurye',
                        'contact' => 'İletişim',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişimde',
                        'qualified' => 'Uygun',
                        'lost' => 'Kayıp',
                        'won' => 'Kazanıldı',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
