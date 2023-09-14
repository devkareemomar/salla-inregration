<?php

namespace App\Filament\Resources;

use App\Enums\MerchentEnum;
use App\Filament\Resources\WorkspaceResource\Pages;
use App\Filament\Resources\WorkspaceResource\RelationManagers;
use App\Models\Merchant;
use App\Models\Workspace;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;


class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true),
                Select::make('merchant_id')
                ->label('merchant')
                ->options(MerchentEnum::merchants())
                ->searchable(),

                Forms\Components\TextInput::make('token')->required(),
                Forms\Components\TextInput::make('channelId')->type('number')->required(),
                
                Forms\Components\Checkbox::make('is_ready'),
                Forms\Components\Checkbox::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('token')->limit(50),
                Tables\Columns\TextColumn::make('channelId'),
                Tables\Columns\TextColumn::make('merchant.name'),
                Tables\Columns\TextColumn::make('is_ready'),
                Tables\Columns\TextColumn::make('is_active'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('created_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\TemplatesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkspaces::route('/'),
            'create' => Pages\CreateWorkspace::route('/create'),
            'edit' => Pages\EditWorkspace::route('/{record}/edit'),
        ];
    }    
}
