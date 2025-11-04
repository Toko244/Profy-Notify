<?php

namespace App\Filament\Resources;

use App\Enums\CriteriaType;
use App\Enums\EmailTemplate;
use App\Enums\NotificationType;
use App\Enums\OrderType;
use App\Enums\Trigger;
use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Language;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Configure the essential details of your notification')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Notification Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Welcome Email, Order Confirmation')
                                    ->helperText('A clear, descriptive name')
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->columnSpan(2),

                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select category')
                                    ->prefixIcon('heroicon-o-folder')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-o-tag'),
                                    ]),

                                Forms\Components\Select::make('trigger')
                                    ->label('Trigger Event')
                                    ->options(Trigger::class)
                                    ->required()
                                    ->reactive()
                                    ->placeholder('When to send?')
                                    ->prefixIcon('heroicon-o-bolt'),

                                Forms\Components\Toggle::make('active')
                                    ->label('Active')
                                    ->default(true)
                                    ->inline(false)
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->onColor('success'),

                                Forms\Components\Toggle::make('send_sms_if_push_disabled')
                                    ->label('SMS Fallback for Push')
                                    ->helperText('Send SMS when push is disabled')
                                    ->inline(false),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Delivery Channels')
                    ->description('Select how to reach your customers')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\CheckboxList::make('notification_type')
                            ->label('Channels')
                            ->options(NotificationType::class)
                            ->required()
                            ->reactive()
                            ->columns(3)
                            ->descriptions([
                                'email' => 'Rich content with templates',
                                'sms' => 'Instant text messages',
                                'push' => 'Mobile push notifications',
                            ]),

                        Forms\Components\Select::make('email_template')
                            ->label('Email Template')
                            ->options(EmailTemplate::class)
                            ->prefixIcon('heroicon-o-document-duplicate')
                            ->visible(fn (Forms\Get $get) =>
                                is_array($get('notification_type')) &&
                                in_array('email', $get('notification_type'))
                            ),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Trigger Configuration')
                    ->description('When and how to send this notification')
                    ->icon('heroicon-o-clock')
                    ->schema(fn (Forms\Get $get): array =>
                        static::getTriggerFields($get('trigger'))
                    )
                    ->visible(fn (Forms\Get $get) => $get('trigger') !== null)
                    ->collapsible(),

                Forms\Components\Section::make('Multi-Language Content')
                    ->description('Add content for each language')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->relationship('translations')
                            ->schema([
                                Forms\Components\Select::make('language_id')
                                    ->label('Language')
                                    ->options(Language::pluck('name', 'id'))
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->prefixIcon('heroicon-o-language'),

                                Forms\Components\TextInput::make('subject')
                                    ->label('Subject')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-chat-bubble-bottom-center-text')
                                    ->helperText('Use {first_name} and {last_name}'),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Content')
                                    ->required()
                                    ->helperText('Use {first_name} and {last_name}')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                Language::find($state['language_id'])?->name ?? 'New'
                            ),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Audience Targeting')
                    ->description('Define who receives this notification')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Forms\Components\Repeater::make('criteria')
                            ->relationship('criteria')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Criterion')
                                    ->options(CriteriaType::class)
                                    ->required()
                                    ->reactive()
                                    ->prefixIcon('heroicon-o-funnel')
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                        $set('additional', [])
                                    ),

                                Forms\Components\Grid::make(3)
                                    ->schema(fn (Forms\Get $get): array =>
                                        static::getCriteriaFields($get('type'))
                                    ),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['type']) ? CriteriaType::from($state['type'])->getLabel() : 'New'
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    protected static function getTriggerFields(?string $trigger): array
    {
        if (!$trigger) return [];

        $triggerEnum = Trigger::tryFrom($trigger);

        return match ($triggerEnum) {
            Trigger::DAILY, Trigger::WEEKLY, Trigger::MONTHLY => array_filter([
                $triggerEnum === Trigger::MONTHLY ?
                    Forms\Components\Select::make('additional.day')
                        ->label('Day of Month')
                        ->options(array_combine(range(1, 31), range(1, 31)))
                        ->required()
                        ->prefixIcon('heroicon-o-calendar') : null,
                $triggerEnum === Trigger::WEEKLY ?
                    Forms\Components\Select::make('additional.week_day')
                        ->label('Day of Week')
                        ->options([
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        ])
                        ->required()
                        ->prefixIcon('heroicon-o-calendar-days') : null,
                Forms\Components\TimePicker::make('additional.time')
                    ->label('Time')
                    ->required()
                    ->seconds(false)
                    ->prefixIcon('heroicon-o-clock'),
            ]),
            Trigger::SCHEDULED => [
                Forms\Components\DateTimePicker::make('additional.time')
                    ->label('Scheduled Date & Time')
                    ->required()
                    ->native(false)
                    ->minDate(now())
                    ->prefixIcon('heroicon-o-calendar'),
            ],
            default => [
                Forms\Components\TextInput::make('additional.delay_d')
                    ->label('Days')
                    ->numeric()
                    ->default(0)
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar'),
                Forms\Components\TextInput::make('additional.delay_h')
                    ->label('Hours')
                    ->numeric()
                    ->default(0)
                    ->suffix('hours')
                    ->prefixIcon('heroicon-o-clock'),
                Forms\Components\TextInput::make('additional.delay_m')
                    ->label('Minutes')
                    ->numeric()
                    ->default(0)
                    ->suffix('min')
                    ->prefixIcon('heroicon-o-clock'),
            ],
        };
    }

    protected static function getCriteriaFields(?string $type): array
    {
        if (!$type) return [];

        $criteriaType = CriteriaType::tryFrom($type);
        $fields = [];

        if (in_array($criteriaType, [
            CriteriaType::HAS_ORDER,
            CriteriaType::DOES_NOT_HAVE_ORDER,
            CriteriaType::ORDER_NOT_COMPLETED,
            CriteriaType::ORDER_PRICE_MORE_THAN,
            CriteriaType::ORDER_PRICE_LESS_THAN,
            CriteriaType::MORE_THAN_ORDER_COUNT,
            CriteriaType::LESS_THAN_ORDER_COUNT,
        ])) {
            $fields[] = Forms\Components\Select::make('additional.type')
                ->label('Order Type')
                ->options(OrderType::class)
                ->prefixIcon('heroicon-o-shopping-cart');
        }

        $fields = array_merge($fields, match ($criteriaType) {
            CriteriaType::HAS_ORDER => [
                Forms\Components\TextInput::make('additional.count')
                    ->label('Min Orders')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->suffix('orders')
                    ->prefixIcon('heroicon-o-hashtag'),
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Period')
                    ->numeric()
                    ->required()
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar'),
            ],
            CriteriaType::DOES_NOT_HAVE_ORDER, CriteriaType::ORDER_NOT_COMPLETED => [
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Period')
                    ->numeric()
                    ->required()
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar')
                    ->columnSpan(2),
            ],
            CriteriaType::ORDER_PRICE_MORE_THAN, CriteriaType::ORDER_PRICE_LESS_THAN => [
                Forms\Components\TextInput::make('additional.price')
                    ->label('Price')
                    ->numeric()
                    ->required()
                    ->prefix('₾')
                    ->prefixIcon('heroicon-o-banknotes'),
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Period')
                    ->numeric()
                    ->required()
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar'),
            ],
            CriteriaType::MORE_THAN_ORDER_COUNT, CriteriaType::LESS_THAN_ORDER_COUNT => [
                Forms\Components\TextInput::make('additional.count')
                    ->label('Count')
                    ->numeric()
                    ->required()
                    ->suffix('orders')
                    ->prefixIcon('heroicon-o-hashtag'),
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Period')
                    ->numeric()
                    ->required()
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar'),
            ],
            CriteriaType::ORDER_RATED_MORE_THAN, CriteriaType::ORDER_RATED_LESS_THAN => [
                Forms\Components\Select::make('additional.rating')
                    ->label('Rating')
                    ->options([
                        1 => '⭐ 1 Star',
                        2 => '⭐⭐ 2 Stars',
                        3 => '⭐⭐⭐ 3 Stars',
                        4 => '⭐⭐⭐⭐ 4 Stars',
                        5 => '⭐⭐⭐⭐⭐ 5 Stars',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-star'),
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Period')
                    ->numeric()
                    ->required()
                    ->suffix('days')
                    ->prefixIcon('heroicon-o-calendar'),
            ],
            default => [],
        });

        return $fields;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->weight(FontWeight::SemiBold)
                    ->icon('heroicon-o-bell'),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color('info'),
                Tables\Columns\TextColumn::make('trigger')
                    ->badge()
                    ->color(fn (string $state): string => match (Trigger::from($state)) {
                        Trigger::REGISTER => 'success',
                        Trigger::ORDER_CREATED, Trigger::ORDER_FINISHED => 'info',
                        Trigger::DAILY, Trigger::WEEKLY, Trigger::MONTHLY => 'warning',
                        Trigger::SCHEDULED => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match (Trigger::from($state)) {
                        Trigger::REGISTER => 'heroicon-o-user-plus',
                        Trigger::ORDER_CREATED, Trigger::ORDER_FINISHED => 'heroicon-o-shopping-cart',
                        Trigger::DAILY, Trigger::WEEKLY, Trigger::MONTHLY => 'heroicon-o-arrow-path',
                        Trigger::SCHEDULED => 'heroicon-o-calendar',
                        default => 'heroicon-o-bolt',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('notification_type')
                    ->label('Channels')
                    ->badge()
                    ->separator(',')
                    ->icon('heroicon-o-paper-airplane'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'title')
                    ->preload()
                    ->multiple()
                    ->indicator('Category'),
                Tables\Filters\SelectFilter::make('trigger')
                    ->options(Trigger::class)
                    ->multiple()
                    ->indicator('Trigger'),
                Tables\Filters\TernaryFilter::make('active')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->indicator('Status'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record) => $record->active ? 'Deactivate' : 'Activate')
                    ->icon(fn ($record) => $record->active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => $record->active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['active' => !$record->active]))
                    ->successNotificationTitle('Status updated'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Notifications activated'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Notifications deactivated'),
                ]),
            ])
            ->emptyStateHeading('No notifications yet')
            ->emptyStateDescription('Create your first notification to get started.')
            ->emptyStateIcon('heroicon-o-bell')
            ->defaultSort('created_at', 'desc')
            ->striped();
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
