<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('profy_id')
                            ->label('Profy ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('first_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('last_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->disabled(),
                        Forms\Components\Toggle::make('allow_notification')
                            ->label('Notifications Enabled')
                            ->disabled(),
                        Forms\Components\TextInput::make('language')
                            ->disabled(),
                        Forms\Components\TextInput::make('onesignal_player_id')
                            ->label('OneSignal Player ID')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('profy_id')
                    ->label('Profy ID')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => "https://dev.profy.ge/admin/users/{$record->profy_id}/view")
                    ->openUrlInNewTab()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\IconColumn::make('allow_notification')
                    ->label('Notifications')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'en' => 'English',
                        'ka' => 'Georgian',
                    ]),
                Tables\Filters\TernaryFilter::make('allow_notification')
                    ->label('Notifications Enabled')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Customer Details')
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->form([
                        Forms\Components\TextInput::make('profy_id')
                            ->label('Profy ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('first_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('last_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->disabled(),
                        Forms\Components\Toggle::make('allow_notification')
                            ->label('Notifications Enabled')
                            ->disabled(),
                        Forms\Components\TextInput::make('language')
                            ->disabled(),
                        Forms\Components\TextInput::make('onesignal_player_id')
                            ->label('OneSignal Player ID')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->fillForm(function ($record) {
                        return $record->toArray();
                    }),
            ])
            ->bulkActions([
                // Read-only resource, no bulk actions
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            // 'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Read-only
    }

    public static function canEdit($record): bool
    {
        return false; // Read-only
    }

    public static function canDelete($record): bool
    {
        return false; // Read-only
    }
}
