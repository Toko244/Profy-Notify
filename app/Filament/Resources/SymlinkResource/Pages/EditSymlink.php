<?php

namespace App\Filament\Resources\SymlinkResource\Pages;

use App\Filament\Resources\SymlinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSymlink extends EditRecord
{
    protected static string $resource = SymlinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
