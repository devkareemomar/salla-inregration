<?php

namespace App\Filament\Resources\MerchantResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $recordTitleAttribute = 'event';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('event')
    //                 ->required()
    //                 ->maxLength(255),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('order_id'),
                Tables\Columns\BadgeColumn::make('status')->colors(['primary']),
                Tables\Columns\TextColumn::make('customer_name'),
                Tables\Columns\TextColumn::make('customer_phone'),
                Tables\Columns\BadgeColumn::make('message')->colors([
                    'danger' => static fn ($state): bool => $state === 'failed',
                    'success' => static fn ($state): bool => $state === 'sent',
                ]),
                Tables\Columns\TextColumn::make('created_at'),
            ])->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
