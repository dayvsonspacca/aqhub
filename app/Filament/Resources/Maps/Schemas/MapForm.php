<?php

namespace App\Filament\Resources\Maps\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('join_name')
                    ->required(),
                DateTimePicker::make('registered_at')
                    ->required()
                    ->columnSpan(2),
            ]);
    }
}
