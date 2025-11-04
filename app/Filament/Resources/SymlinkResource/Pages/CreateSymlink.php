<?php

namespace App\Filament\Resources\SymlinkResource\Pages;

use App\Filament\Resources\SymlinkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSymlink extends CreateRecord
{
    protected static string $resource = SymlinkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
