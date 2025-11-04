<?php

namespace App\Filament\Resources\SymlinkResource\Pages;

use App\Filament\Resources\SymlinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSymlinks extends ListRecords
{
    protected static string $resource = SymlinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
