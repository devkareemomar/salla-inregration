<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantResource\Pages;
use App\Filament\Resources\MerchantResource\RelationManagers\EventsRelationManager;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;


class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('merchant')->required()->unique(ignoreRecord: true)->disabled(),
                Forms\Components\TextInput::make('name')->required()->disabled(),
                Forms\Components\TextInput::make('access_token')->required()->unique(ignoreRecord: true)->disabled(),
                Forms\Components\TextInput::make('refresh_token')->required()->unique(ignoreRecord: true)->disabled(),
                Forms\Components\TextInput::make('email')->unique(ignoreRecord: true)->disabled(),
                Forms\Components\TextInput::make('phone')->unique(ignoreRecord: true)->disabled(),
                Select::make('status')
                ->label('status')
                ->options([
                    'pending' => 'pending',
                    'active' => 'active'
                    ])
                ->searchable()
                ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('access_token')->limit(30),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->searchable()->colors([
                    'warning' => static fn ($state): bool => $state === 'pending',
                    'success' => static fn ($state): bool => $state === 'active',
                ]),
            ])
            
            ->filters([
                SelectFilter::make('status')
                            ->options([
                                'pending' => 'pending',
                                'active' => 'active'
                                ])
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('id', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            EventsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }    


    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status','pending')->count();
    }
}
