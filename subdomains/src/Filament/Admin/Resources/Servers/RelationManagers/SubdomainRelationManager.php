<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\Servers\RelationManagers;

use App\Models\Server;
use Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\CloudflareDomainResource;
use Boy132\Subdomains\Models\CloudflareDomain;
use Boy132\Subdomains\Models\Subdomain;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Server getOwnerRecord()
 */
class SubdomainRelationManager extends RelationManager
{
    protected static string $relationship = 'subdomains';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        return CloudflareDomainResource::canViewAny();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn () => trans_choice('subdomains::strings.subdomain', 2) . ' (' . trans('subdomains::strings.limit') . ': ' . ($this->getOwnerRecord()->subdomain_limit ?? 0) . ')')
            ->columns([
                TextColumn::make('label')
                    ->label(trans('subdomains::strings.name'))
                    ->state(fn (Subdomain $subdomain) => $subdomain->getLabel()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => CloudflareDomain::count() > 0)
                    ->disabled(fn () => !$this->getOwnerRecord()->allocation || in_array($this->getOwnerRecord()->allocation->ip, ['0.0.0.0', '::']))
                    ->createAnother(false),
            ]);
    }

    public function form(Schema $schema): Schema
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
                Toggle::make('srv_record')
                    ->label(trans('subdomains::strings.srv_record'))
                    ->helperText(trans('subdomains::strings.srv_record_help'))
                    ->default(false),
            ]);
    }
}
