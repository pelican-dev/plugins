<?php

namespace Boy132\LegalPages\Filament\Admin\Pages;

use Boy132\LegalPages\Enums\LegalPageType;
use Boy132\LegalPages\LegalPagesPlugin;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

/**
 * @property Schema $form
 */
class LegalPages extends Page
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-gavel';

    protected string $view = 'filament.server.pages.server-form-page';

    /** @var array<mixed>|null */
    public ?array $data = [];

    public function getTitle(): string
    {
        return trans_choice('legal-pages::strings.legal_page', 2);
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('legal-pages::strings.legal_page', 2);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
    }

    public static function canAccess(): bool
    {
        return user()?->can('view legalPage');
    }

    public function mount(): void
    {
        $data = [];

        foreach (LegalPageType::cases() as $legalPageType) {
            $data[$legalPageType->getId()] = LegalPagesPlugin::Load($legalPageType);
        }

        $this->form->fill($data);
    }

    /**
     * @return Component[]
     *
     * @throws Exception
     */
    public function getFormSchema(): array
    {
        $schema = [];

        foreach (LegalPageType::cases() as $legalPageType) {
            $schema[] = MarkdownEditor::make($legalPageType->getId())
                ->label($legalPageType->getLabel())
                ->disabled(fn () => !user()?->can('update legalPage'))
                ->hintActions([
                    Action::make('view')
                        ->label(trans('filament-actions::view.single.label'))
                        ->icon('tabler-eye')
                        ->url($legalPageType->getUrl(), true)
                        ->visible(fn (Get $get) => $get($legalPageType->getId())),
                    Action::make('clear')
                        ->label(trans('legal-pages::strings.clear'))
                        ->authorize(fn () => user()?->can('update legalPage'))
                        ->color('danger')
                        ->icon('tabler-trash')
                        ->action(fn (Set $set) => $set($legalPageType->getId(), null)),
                ]);
        }

        return $schema;
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(trans('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->authorize(fn () => user()?->can('update legalPage'))
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $legalPageType => $contents) {
                LegalPagesPlugin::Save($legalPageType, $contents);
            }

            Notification::make()
                ->title(trans('legal-pages::strings.notifications.saved'))
                ->success()
                ->send();
        } catch (Exception $exception) {
            Notification::make()
                ->title(trans('legal-pages::strings.notifications.saved_error'))
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
