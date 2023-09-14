<?php

namespace App\Filament\Resources;

use App\Enums\EventEnum;
use App\Enums\ParameterEnum;
use App\Enums\ParameterSelect;
use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use App\Models\Workspace;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    public $parameters;
    public function __construct()
    {
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('lang')->required(),
                Select::make('workspace_id')
                    ->label('workspace')
                    ->options(Workspace::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('content')->required(),


                Select::make('event')
                    ->label('event')
                    ->options(EventEnum::values())
                    ->searchable()
                    ->reactive()
                    ->required(),

                   

                Repeater::make('parameters')
                    ->schema([

                        Select::make('parameters')
                        ->label('parameter')
                        ->options(function (callable $get){
                            return  EventEnum::parameters($get('../../event')) ?? [];
                        })
                        ->searchable()
                        ->required(),

                    ])->createItemButtonLabel('Add parameter')->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workspace.name')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('lang'),
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('content'),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
