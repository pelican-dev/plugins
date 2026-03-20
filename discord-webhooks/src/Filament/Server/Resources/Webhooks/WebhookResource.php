<?php

namespace Notjami\Webhooks\Filament\Server\Resources\Webhooks;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Notjami\Webhooks\Enums\WebhookEvent;
use Notjami\Webhooks\Filament\Server\Resources\Webhooks\Pages\ManageWebhooks;
use Notjami\Webhooks\Models\Webhook;
use Notjami\Webhooks\Services\DiscordWebhookService;
class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-webhook';

    public static function getNavigationLabel(): string
    {
        return 'Discord Webhooks';
    }

    public static function getModelLabel(): string
    {
        return 'Discord Webhook';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Discord Webhooks';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('webhook_url')
                    ->label('Webhook URL')
                    ->limit(30)
                    ->formatStateUsing(function ($state) {
                        // Mask the URL: show scheme://host/...****
                        if (empty($state)) return '';
                        $parsed = parse_url($state);
                        if (!$parsed || !isset($parsed['scheme'], $parsed['host'])) return '••••••';
                        $last4 = substr($parsed['path'] ?? '', -4);
                        $masked = $parsed['scheme'] . '://' . $parsed['host'] . '/...';
                        if ($last4) {
                            $masked .= $last4;
                        }
                        return $masked;
                    })
                    ->tooltip('••••••'),
                TextColumn::make('events')
                    ->label('Events')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $events = is_array($state) ? $state : (array) $state;
                        return collect($events)
                            ->map(fn ($event) => WebhookEvent::tryFrom($event))
                            ->filter()
                            ->map(fn ($event) => $event->getLabel())
                            ->join(', ');
                    }),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean(),
                TextColumn::make('last_triggered_at')
                    ->label('Last Triggered')
                    ->dateTime()
                    ->placeholder('Never'),
            ])
            ->recordActions([
                Action::make('test')
                    ->label('Test')
                    ->icon('tabler-send')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Test Webhook')
                    ->modalDescription('This will send a test message to the webhook URL.')
                    ->action(function (Webhook $record, DiscordWebhookService $service) {
                        $success = $service->sendTestMessage($record);
                        
                        if ($success) {
                            Notification::make()
                                ->title('Test message sent!')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to send test message')
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false),
            ])
            ->emptyStateIcon('tabler-webhook')
            ->emptyStateDescription('')
            ->emptyStateHeading('No webhooks configured');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('My Discord Webhook')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('webhook_url')
                    ->label('Discord Webhook URL')
                    ->placeholder('https://discord.com/api/webhooks/...')
                    ->required()
                    ->url()
                    ->regex('/^https:\/\/discord\\.com\/api\/webhooks\/.+/')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('events')
                    ->label('Events')
                    ->multiple()
                    ->options(collect(WebhookEvent::cases())->mapWithKeys(fn ($event) => [
                        $event->value => $event->getLabel(),
                    ]))
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('enabled')
                    ->label('Enabled')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWebhooks::route('/'),
        ];
    }
}
