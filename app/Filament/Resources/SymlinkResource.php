<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SymlinkResource\Pages;
use App\Models\Symlink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SymlinkResource extends Resource
{
    protected static ?string $model = Symlink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Utilities';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('original')
                            ->label('Original URL')
                            ->url()
                            ->required()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('symlink')
                            ->label('Short Link Code')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('The short code for your link (e.g., "promo2024")')
                            ->suffixIcon('heroicon-o-sparkles')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->action(function (Forms\Set $set) {
                                        $set('symlink', Str::random(8));
                                    })
                            ),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symlink')
                    ->label('Short Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Short link copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('original')
                    ->label('Original URL')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->original),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Clicks')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => url('/link/' . $record->symlink))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListSymlinks::route('/'),
            'create' => Pages\CreateSymlink::route('/create'),
            'edit' => Pages\EditSymlink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
