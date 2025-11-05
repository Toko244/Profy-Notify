<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Enums\CriteriaType;
use App\Enums\EmailTemplate;
use App\Enums\NotificationType;
use App\Enums\OrderType;
use App\Enums\Trigger;
use App\Filament\Resources\NotificationResource;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateNotification extends CreateRecord
{
    use HasWizard;

    protected static string $resource = NotificationResource::class;

    protected function getSteps(): array
    {
        return [
            // Step 1: Information
            Forms\Components\Wizard\Step::make('Information')
                ->description('Set up the basic details for your notification')
                ->icon('heroicon-o-information-circle')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Notification Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g., Welcome Email, Order Confirmation')
                                        ->helperText('A clear, descriptive name for this notification')
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->columnSpan(2),

                                    Forms\Components\Select::make('category_id')
                                        ->label('Category')
                                        ->relationship('category', 'title')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->placeholder('Select a category')
                                        ->helperText('Organize notifications by category')
                                        ->prefixIcon('heroicon-o-folder')
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('title')
                                                ->label('Category Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('e.g., Marketing, Transactional')
                                                ->prefixIcon('heroicon-o-tag'),
                                        ])
                                        ->createOptionModalHeading('Create New Category'),

                                    Forms\Components\Select::make('trigger')
                                        ->label('Trigger Event')
                                        ->options(Trigger::class)
                                        ->required()
                                        ->reactive()
                                        ->placeholder('When should this send?')
                                        ->helperText('Choose when this notification should be triggered')
                                        ->prefixIcon('heroicon-o-bolt'),

                                    Forms\Components\Toggle::make('active')
                                        ->label('Active Status')
                                        ->default(true)
                                        ->helperText('Inactive notifications won\'t be sent')
                                        ->inline(false)
                                        ->onIcon('heroicon-o-check-circle')
                                        ->offIcon('heroicon-o-x-circle')
                                        ->onColor('success'),
                                ]),
                        ]),

                    Forms\Components\Section::make('Quick Tips')
                        ->description('💡 Helpful information for this step')
                        ->collapsed()
                        ->schema([
                            Forms\Components\Placeholder::make('tips')
                                ->content('
                                    **Choose the right trigger:**
                                    - **Register**: Sent when a customer signs up
                                    - **Order Created**: Sent immediately after order placement
                                    - **Order Finished**: Sent when service is completed
                                    - **Scheduled**: Send at a specific date/time
                                    - **Daily/Weekly/Monthly**: Recurring notifications
                                ')
                                ->columnSpanFull(),
                        ]),
                ])
                ->columns(2),

            // Step 2: Trigger Options
            Forms\Components\Wizard\Step::make('Trigger Options')
                ->description('Configure when and how to send this notification')
                ->icon('heroicon-o-clock')
                ->completedIcon('heroicon-o-check-circle')
                ->schema(fn (Forms\Get $get): array => [
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('trigger_selected')
                                ->content(fn () => $get('trigger')
                                    ? '**Selected Trigger:** ' . Trigger::from($get('trigger'))->getLabel()
                                    : '⚠️ Please select a trigger in the previous step')
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(3)
                                ->schema(self::getTriggerFields($get('trigger'))),
                        ]),

                    Forms\Components\Section::make('ℹ️ Timing Information')
                        ->description('Understand how timing works')
                        ->collapsed()
                        ->schema([
                            Forms\Components\Placeholder::make('timing_info')
                                ->content(function () use ($get) {
                                    if (!$get('trigger')) {
                                        return 'Select a trigger to see timing options.';
                                    }

                                    $trigger = Trigger::from($get('trigger'));
                                    return match($trigger) {
                                        Trigger::SCHEDULED => '📅 **Scheduled**: Send once at a specific date and time',
                                        Trigger::DAILY => '🔄 **Daily**: Repeats every day at the specified time',
                                        Trigger::WEEKLY => '📆 **Weekly**: Repeats every week on the chosen day',
                                        Trigger::MONTHLY => '📊 **Monthly**: Repeats every month on the chosen day',
                                        default => '⏱️ **Event-Based**: Delay after the trigger event occurs'
                                    };
                                })
                                ->columnSpanFull(),
                        ]),
                ])
                ->columns(1),

            // Step 3: Notification Type
            Forms\Components\Wizard\Step::make('Channels')
                ->description('Choose how to deliver this notification')
                ->icon('heroicon-o-paper-airplane')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Forms\Components\Section::make('Delivery Channels')
                        ->description('Select one or more channels to reach your customers')
                        ->schema([
                            Forms\Components\CheckboxList::make('notification_type')
                                ->label('Select Channels')
                                ->options([
                                    'email' => 'Email',
                                    'sms' => 'SMS',
                                    'push' => 'Push Notification',
                                ])
                                ->descriptions([
                                    'email' => '📧 Send via email with rich content and templates',
                                    'sms' => '💬 Send via SMS for instant delivery',
                                    'push' => '📱 Send push notifications to mobile devices',
                                ])
                                ->required()
                                ->reactive()
                                ->columns(3)
                                ->columnSpanFull()
                                ->gridDirection('row'),
                        ]),

                    Forms\Components\Section::make('Email Settings')
                        ->description('Configure email-specific options')
                        ->icon('heroicon-o-envelope')
                        ->schema([
                            Forms\Components\Select::make('email_template')
                                ->label('Email Template')
                                ->options(EmailTemplate::class)
                                ->placeholder('Choose a template design')
                                ->helperText('Select the visual template for your email')
                                ->prefixIcon('heroicon-o-document-duplicate')
                                ->columnSpanFull(),
                        ])
                        ->visible(fn (Forms\Get $get) =>
                            is_array($get('notification_type')) &&
                            in_array('email', $get('notification_type'))
                        )
                        ->collapsible(),

                    Forms\Components\Section::make('Push Notification Settings')
                        ->description('Configure push-specific options')
                        ->icon('heroicon-o-bell-alert')
                        ->schema([
                            Forms\Components\Toggle::make('send_sms_if_push_disabled')
                                ->label('SMS Fallback')
                                ->helperText('Send SMS if customer has disabled push notifications')
                                ->inline(false)
                                ->onIcon('heroicon-o-check-circle')
                                ->offIcon('heroicon-o-x-circle'),
                        ])
                        ->visible(fn (Forms\Get $get) =>
                            is_array($get('notification_type')) &&
                            in_array('push', $get('notification_type'))
                        )
                        ->collapsible(),

                    Forms\Components\Section::make('💡 Channel Tips')
                        ->collapsed()
                        ->schema([
                            Forms\Components\Placeholder::make('channel_tips')
                                ->content('
                                    **Email**: Best for detailed content, images, and links
                                    **SMS**: Best for urgent, short messages (160 characters)
                                    **Push**: Best for real-time alerts and re-engagement

                                    **Pro Tip**: Use multiple channels for important notifications to ensure delivery!
                                ')
                                ->columnSpanFull(),
                        ]),
                ]),

            // Step 4: Translations
            Forms\Components\Wizard\Step::make('Content')
                ->description('Add notification content for each language')
                ->icon('heroicon-o-language')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Forms\Components\Section::make('Multi-Language Content')
                        ->description('Create personalized content for each language')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            Forms\Components\Repeater::make('translations')
                                ->relationship('translations')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('language_id')
                                                ->label('Language')
                                                ->options(Language::pluck('name', 'id'))
                                                ->required()
                                                ->distinct()
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                ->prefixIcon('heroicon-o-language')
                                                ->placeholder('Select language'),

                                            Forms\Components\TextInput::make('subject')
                                                ->label('Subject Line')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('e.g., Welcome to Profy!')
                                                ->prefixIcon('heroicon-o-chat-bubble-bottom-center-text')
                                                ->helperText('Use {first_name} and {last_name} for personalization'),
                                        ]),

                                    Forms\Components\RichEditor::make('content')
                                        ->label('Message Content')
                                        ->required()
                                        ->placeholder('Write your notification message here...')
                                        ->helperText('Available variables: {first_name}, {last_name}')
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'underline',
                                            'link',
                                            'bulletList',
                                            'orderedList',
                                            'h2',
                                            'h3',
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(1)
                                ->addActionLabel('➕ Add Another Language')
                                ->collapsible()
                                ->cloneable()
                                ->itemLabel(fn (array $state): ?string =>
                                    Language::find($state['language_id'])?->name ?? '🆕 New Translation'
                                )
                                ->deleteAction(
                                    fn ($action) => $action->requiresConfirmation()
                                ),
                        ]),

                    Forms\Components\Section::make('✏️ Content Writing Tips')
                        ->collapsed()
                        ->schema([
                            Forms\Components\Placeholder::make('content_tips')
                                ->content('
                                    **Personalization Variables:**
                                    - `{first_name}` - Customer\'s first name
                                    - `{last_name}` - Customer\'s last name

                                    **Best Practices:**
                                    - Keep subject lines under 50 characters
                                    - Write in a friendly, conversational tone
                                    - Include a clear call-to-action
                                    - Test with different customer names

                                    **Example:**
                                    "Hi {first_name}, welcome to Profy! We\'re excited to have you."
                                ')
                                ->columnSpanFull(),
                        ]),
                ]),

            // Step 5: Criteria
            Forms\Components\Wizard\Step::make('Targeting')
                ->description('Define who should receive this notification')
                ->icon('heroicon-o-user-group')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Forms\Components\Section::make('Audience Targeting')
                        ->description('Add criteria to target specific customer segments (optional)')
                        ->icon('heroicon-o-funnel')
                        ->schema([
                            Forms\Components\Placeholder::make('targeting_info')
                                ->content('**ℹ️ Leave empty to send to all customers**, or add criteria below to filter your audience.')
                                ->columnSpanFull(),

                            Forms\Components\Repeater::make('criteria')
                                ->relationship('criteria')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema(fn (Forms\Get $get): array => [
                                            Forms\Components\Select::make('type')
                                                ->label('Criterion Type')
                                                ->options(CriteriaType::class)
                                                ->required()
                                                ->reactive()
                                                ->prefixIcon('heroicon-o-funnel')
                                                ->placeholder('Select a criterion')
                                                ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                                    $set('additional', [])
                                                )
                                                ->columnSpan(3),

                                            ...self::getCriteriaFields($get('type')),
                                        ]),
                                ])
                                ->columns(1)
                                ->defaultItems(0)
                                ->addActionLabel('➕ Add Targeting Criterion')
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->itemLabel(fn (array $state): ?string =>
                                    isset($state['type'])
                                        ? '🎯 ' . CriteriaType::from($state['type'])->getLabel()
                                        : '🆕 New Criterion'
                                )
                                ->deleteAction(
                                    fn ($action) => $action
                                        ->requiresConfirmation()
                                        ->modalHeading('Delete this criterion?')
                                        ->modalDescription('This action cannot be undone.')
                                ),
                        ]),

                    Forms\Components\Section::make('🎯 Targeting Examples')
                        ->collapsed()
                        ->schema([
                            Forms\Components\Placeholder::make('targeting_examples')
                                ->content('
                                    **Common Targeting Scenarios:**

                                    📦 **New Customers**: "Does not have order" in last 30 days
                                    💰 **High Value**: "Order price more than" ₾500 in last 90 days
                                    ⭐ **Satisfied Customers**: "Order rated more than" 4 stars
                                    🔄 **Repeat Buyers**: "More than order count" 3 in last 180 days
                                    😞 **Dissatisfied**: "Order rated less than" 3 stars

                                    **Pro Tip**: Combine multiple criteria for precise targeting!
                                ')
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    protected static function getTriggerFields(?string $trigger): array
    {
        if (!$trigger) {
            return [
                Forms\Components\Placeholder::make('no_trigger')
                    ->content('⬅️ Please select a trigger in Step 1')
                    ->columnSpan(3),
            ];
        }

        $triggerEnum = Trigger::tryFrom($trigger);

        return match ($triggerEnum) {
            Trigger::DAILY, Trigger::WEEKLY, Trigger::MONTHLY => array_filter([
                $triggerEnum === Trigger::MONTHLY ?
                    Forms\Components\Select::make('additional.day')
                        ->label('Day of Month')
                        ->options(array_combine(range(1, 31), range(1, 31)))
                        ->required()
                        ->placeholder('Select day (1-31)')
                        ->prefixIcon('heroicon-o-calendar')
                        ->helperText('The day of the month to send') : null,

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
                        ->placeholder('Select day')
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->helperText('Which day of the week') : null,

                Forms\Components\TimePicker::make('additional.time')
                    ->label('Time of Day')
                    ->required()
                    ->seconds(false)
                    ->prefixIcon('heroicon-o-clock')
                    ->helperText('When to send each occurrence')
                    ->columnSpan($triggerEnum === Trigger::DAILY ? 3 : 1),
            ]),

            Trigger::SCHEDULED => [
                Forms\Components\DateTimePicker::make('additional.time')
                    ->label('Scheduled Date & Time')
                    ->required()
                    ->native(false)
                    ->minDate(now())
                    ->prefixIcon('heroicon-o-calendar')
                    ->helperText('When to send this one-time notification')
                    ->columnSpan(3),
            ],

            default => [
                Forms\Components\TextInput::make('additional.delay_d')
                    ->label('Days')
                    ->numeric()
                    ->default(0)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('Days to wait'),

                Forms\Components\TextInput::make('additional.delay_h')
                    ->label('Hours')
                    ->numeric()
                    ->default(0)
                    ->prefixIcon('heroicon-o-clock')
                    ->suffix('hours')
                    ->helperText('Hours to wait'),

                Forms\Components\TextInput::make('additional.delay_m')
                    ->label('Minutes')
                    ->numeric()
                    ->default(0)
                    ->prefixIcon('heroicon-o-clock')
                    ->suffix('minutes')
                    ->helperText('Minutes to wait'),
            ],
        };
    }

    protected static function getCriteriaFields(?string $type): array
    {
        if (!$type) {
            return [];
        }

        $criteriaType = CriteriaType::tryFrom($type);

        $fields = [];

        // Order type field (common for many criteria)
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
                ->placeholder('All types')
                ->prefixIcon('heroicon-o-shopping-cart')
                ->helperText('Filter by order type (optional)');
        }

        // Type-specific fields
        $fields = array_merge($fields, match ($criteriaType) {
            CriteriaType::HAS_ORDER => [
                Forms\Components\TextInput::make('additional.count')
                    ->label('Minimum Orders')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-hashtag')
                    ->suffix('orders')
                    ->helperText('Customer must have at least this many orders'),

                Forms\Components\TextInput::make('additional.duration')
                    ->label('Time Period')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('Within the last X days'),
            ],

            CriteriaType::DOES_NOT_HAVE_ORDER, CriteriaType::ORDER_NOT_COMPLETED => [
                Forms\Components\TextInput::make('additional.duration')
                    ->label('Time Period')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('In the last X days')
                    ->columnSpan(2),
            ],

            CriteriaType::ORDER_PRICE_MORE_THAN, CriteriaType::ORDER_PRICE_LESS_THAN => [
                Forms\Components\TextInput::make('additional.price')
                    ->label('Price Amount')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('₾')
                    ->prefixIcon('heroicon-o-banknotes')
                    ->helperText('Price threshold'),

                Forms\Components\TextInput::make('additional.duration')
                    ->label('Time Period')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('In the last X days'),
            ],

            CriteriaType::MORE_THAN_ORDER_COUNT, CriteriaType::LESS_THAN_ORDER_COUNT => [
                Forms\Components\TextInput::make('additional.count')
                    ->label('Order Count')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefixIcon('heroicon-o-hashtag')
                    ->suffix('orders')
                    ->helperText('Number of orders threshold'),

                Forms\Components\TextInput::make('additional.duration')
                    ->label('Time Period')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('In the last X days'),
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
                    ->prefixIcon('heroicon-o-star')
                    ->helperText('Rating threshold'),

                Forms\Components\TextInput::make('additional.duration')
                    ->label('Time Period')
                    ->numeric()
                    ->minValue(1)
                    ->prefixIcon('heroicon-o-calendar')
                    ->suffix('days')
                    ->helperText('In the last X days'),
            ],

            default => [],
        });

        return $fields;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction()
                ->label('Create Notification')
                ->icon('heroicon-o-check-circle'),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }
}
