<?php

namespace App\Filament\Resources\NotificationCategoryResource\Pages;

use App\Filament\Resources\NotificationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotificationCategory extends EditRecord
{
    protected static string $resource = NotificationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
