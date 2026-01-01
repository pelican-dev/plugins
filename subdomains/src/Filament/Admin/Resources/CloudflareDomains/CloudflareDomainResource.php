<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains;

use Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\Pages\ManageCloudflareDomains;
use Boy132\Subdomains\Models\CloudflareDomain;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CloudflareDomainResource extends Resource
{
    protected static ?string $model = CloudflareDomain::class;

    protected static ?string $slug = 'domains';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-world-www';

    public static function getNavigationLabel(): string
    {
        return trans_choice('subdomains::strings.domain', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('subdomains::strings.domain', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('subdomains::strings.domain', 2);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
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
                    ->label(trans('subdomains::strings.name')),
                TextColumn::make('subdomains_count')
                    ->label(trans_choice('subdomains::strings.subdomain', 2))
                    ->counts('subdomains'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::canEdit($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateIcon('tabler-world-www')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('subdomains::strings.no_domains'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label(trans('subdomains::strings.name'))
                    ->required()
                    ->unique(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(trans('subdomains::strings.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCloudflareDomains::route('/'),
        ];
    }
}
