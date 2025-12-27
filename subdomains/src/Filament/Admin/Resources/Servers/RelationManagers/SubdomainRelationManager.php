<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\Users\RelationManagers;

use App\Models\Server;
use Boy132\Subdomains\Models\CloudflareDomain;
use Boy132\Subdomains\Models\Subdomain;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * @method Server getOwnerRecord()
 */
class SubdomainRelationManager extends RelationManager
{
    protected static string $relationship = 'subdomains';

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn () => trans_choice('subdomains::strings.subdomain', 2) . ' (' . trans('subdomains::strings.limit') .': ' . ($this->getOwnerRecord()->subdomain_limit ?? 0) . ')')
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
                Action::make('change_limit')
                    ->label(trans('subdomains::strings.change_limit'))
                    ->schema([
                        TextInput::make('limit')
                            ->label(trans('subdomains::strings.limit'))
                            ->numeric()
                            ->required()
                            ->default($this->getOwnerRecord()->subdomain_limit ?? 0)
                            ->minValue(0),
                    ])
                    ->action(function ($data) {
                        $oldLimit = $this->getOwnerRecord()->subdomain_limit ?? 0;
                        $newLimit = $data['limit'];

                        $this->getOwnerRecord()->update(['subdomain_limit' => $newLimit]);

                        Notification::make()
                            ->title(trans('subdomains::strings.limit_changed'))
                            ->body($oldLimit . ' -> ' . $newLimit)
                            ->success()
                            ->send();
                    }),
                CreateAction::make()
                    ->visible(fn () => CloudflareDomain::count() > 0)
                    ->disabled(fn () => !$this->getOwnerRecord()->allocation || $this->getOwnerRecord()->allocation->ip === '0.0.0.0' || $this->getOwnerRecord()->allocation->ip === '::')
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
                Hidden::make('record_type')
                    ->default(fn () => is_ipv6($this->getOwnerRecord()->allocation->ip) ? 'AAAA' : 'A'),
            ]);
    }
}
