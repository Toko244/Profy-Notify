<?php

namespace App\Filament\Resources\SegmentResource\Pages;

use App\Filament\Resources\SegmentResource;
use App\Models\Customer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class CreateSegment extends CreateRecord
{
    protected static string $resource = SegmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the segment
        $segment = static::getModel()::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Process Excel file if uploaded
        if (isset($data['excel_file'])) {
            $this->processExcelFile($segment, $data['excel_file']);
        }

        return $segment;
    }

    protected function processExcelFile(Model $segment, $file): void
    {
        try {
            $path = storage_path('app/public/' . $file);

            $data = Excel::toArray([], $path);

            if (empty($data) || empty($data[0])) {
                Notification::make()
                    ->title('Excel file is empty or invalid')
                    ->danger()
                    ->send();
                return;
            }

            $emails = collect($data[0])
                ->skip(1) // Skip header row
                ->pluck(0) // Assume email is in first column
                ->filter()
                ->map(fn($email) => trim($email))
                ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique();

            // Find customers by email and attach to segment
            $customers = Customer::whereIn('email', $emails)->get();

            $segment->customers()->attach($customers->pluck('id'));

            Notification::make()
                ->title("Successfully added {$customers->count()} customers to segment")
                ->success()
                ->send();

            // Clean up uploaded file
            if (file_exists($path)) {
                unlink($path);
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error processing Excel file')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
