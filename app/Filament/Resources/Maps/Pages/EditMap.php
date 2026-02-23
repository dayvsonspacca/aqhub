<?php

namespace App\Filament\Resources\Maps\Pages;

use App\Filament\Resources\Maps\MapResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditMap extends EditRecord
{
    protected static string $resource = MapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Delete')->icon(Heroicon::Trash),
        ];
    }
}
