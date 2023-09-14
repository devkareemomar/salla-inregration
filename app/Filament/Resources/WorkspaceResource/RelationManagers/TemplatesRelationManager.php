<?php

namespace App\Filament\Resources\WorkspaceResource\RelationManagers;

use App\Enums\EventEnum;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\EditAction;

class TemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'templates';

    protected static ?string $recordTitleAttribute = 'name';


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('lang')->required(),
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
                            ->options(function (callable $get) {
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
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('lang'),
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('content')->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['parameters']  = json_encode(collect($data['parameters'])->pluck('parameters')->toArray());
                        return $data;
                    })
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['parameters']  =  json_decode($data['parameters'], true);
                        $array = array();

                        foreach ($data['parameters'] as $param) {
                            array_push($array, ['parameters' => $param]);
                        }

                        $data['parameters'] = $array;

                        return $data;
                    })->mutateFormDataUsing(function (array $data): array {
                        $data['parameters']  = json_encode(collect($data['parameters'])->pluck('parameters')->toArray());
                        return $data;
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('created_at', 'desc');
    }
}
