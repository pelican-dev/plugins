<?php

namespace Boy132\Subdomains\Filament\Server\Resources\Subdomains;

use App\Models\Server;
use App\Traits\Filament\BlockAccessInConflict;
use App\Traits\Filament\HasLimitBadge;
use Boy132\Subdomains\Filament\Server\Resources\Subdomains\Pages\ListSubdomains;
use Boy132\Subdomains\Models\CloudflareDomain;
use Boy132\Subdomains\Models\Subdomain;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubdomainResource extends Resource
{
    use BlockAccessInConflict;
    use HasLimitBadge;

    protected static ?string $model = Subdomain::class;

    protected static ?int $navigationSort = 30;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-world-www';

    public static function canAccess(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return parent::canAccess() && $server->allocation && $server->allocation->ip !== '0.0.0.0' && $server->allocation->ip !== '::' && CloudflareDomain::count() > 0;
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('subdomains::strings.subdomain', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('subdomains::strings.subdomain', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('subdomains::strings.subdomain', 2);
    }

    protected static function getBadgeCount(): int
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $server->subdomains->count(); // @phpstan-ignore property.notFound
    }

    protected static function getBadgeLimit(): int
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $server->subdomain_limit ?? 0;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(trans('subdomains::strings.name'))
                    ->state(fn (Subdomain $subdomain) => $subdomain->getLabel()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->icon('tabler-world-plus')
                    ->tooltip(fn () => static::getBadgeCount() >= static::getBadgeLimit() ? trans('subdomains::strings.limit_reached') : trans('subdomains::strings.create_subdomain'))
                    ->disabled(fn () => static::getBadgeCount() >= static::getBadgeLimit())
                    ->color(fn () => static::getBadgeCount() >= static::getBadgeLimit() ? 'danger' : 'primary')
                    ->createAnother(false)
                    ->hiddenLabel()
                    ->iconButton()
                    ->iconSize(IconSize::ExtraLarge),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('subdomains::strings.name'))
                    ->required()
                    ->unique(),
                Select::make('domain_id')
                    ->label(trans_choice('subdomains::strings.domain', 1))
                    ->disabledOn('edit')
                    ->required()
                    ->relationship('domain', 'name')
                    ->preload()
                    ->searchable(),
                Hidden::make('record_type')
                    ->default(function () {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        return is_ipv6($server->allocation->ip) ? 'AAAA' : 'A';
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubdomains::route('/'),
        ];
    }
}
