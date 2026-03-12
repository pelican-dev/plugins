<?php

namespace Ebnater\BackupTemplates\Filament\Server\Resources\BackupTemplates;

use App\Models\Server;
use Ebnater\BackupTemplates\Filament\Server\Resources\BackupTemplates\Pages\ListBackupTemplates;
use Ebnater\BackupTemplates\Models\BackupTemplate;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BackupTemplateResource extends Resource
{
    protected static ?string $model = BackupTemplate::class;

    protected static ?int $navigationSort = 31;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-template';

    public static function getNavigationLabel(): string
    {
        return trans_choice('backup-templates::strings.template', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('backup-templates::strings.template', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('backup-templates::strings.template', 2);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('backup-templates::strings.fields.name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('ignored')
                    ->label(trans('backup-templates::strings.fields.ignored'))
                    ->helperText(trans('backup-templates::strings.fields.ignored_help'))
                    ->rows(8)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('backup-templates::strings.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ignored')
                    ->label(trans('backup-templates::strings.fields.ignored'))
                    ->lineClamp(2)
                    ->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackupTemplates::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var Server|null $server */
        $server = Filament::getTenant();

        return parent::getEloquentQuery()
            ->when($server, fn (Builder $query) => $query->where('server_id', $server->id));
    }
}
