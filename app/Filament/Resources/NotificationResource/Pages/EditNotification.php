<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotification extends EditRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggle')
                ->label(fn () => $this->record->active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->active ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn () => $this->record->active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['active' => !$this->record->active]))
                ->after(fn () => $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]))),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
            Actions\ForceDeleteAction::make()
                ->icon('heroicon-o-trash'),
            Actions\RestoreAction::make()
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Notification updated successfully!';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon('heroicon-o-check-circle'),
            $this->getCancelFormAction(),
        ];
    }
}
