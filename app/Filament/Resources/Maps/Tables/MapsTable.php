<?php

namespace App\Filament\Resources\Maps\Tables;

use App\Jobs\FindMapMonstersJob;
use App\Models\Map;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MapsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('join_name')
                    ->searchable(),
                TextColumn::make('registered_at')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions(ActionGroup::make([
                EditAction::make(),
                Action::make('find_map_monsters')
                    ->label('Find Monsters')
                    ->modalHeading('This action may take a while.')
                    ->color('primary')
                    ->icon(Heroicon::MagnifyingGlass)
                    ->requiresConfirmation()
                    ->action(function (Map $record) {
                        FindMapMonstersJob::dispatch($record);

                        Notification::make()
                            ->title('The map monsters are being found. Please check the logs for more details.')
                            ->success()
                            ->send();
                    }),
            ]));
    }
}
