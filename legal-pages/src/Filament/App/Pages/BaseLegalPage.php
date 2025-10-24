<?php

namespace Boy132\LegalPages\Filament\App\Pages;

use Boy132\LegalPages\Enums\LegalPageType;
use Boy132\LegalPages\LegalPagesPlugin;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\Width;

abstract class BaseLegalPage extends SimplePage implements HasForms
{
    use InteractsWithForms;

    /** @var array<string, mixed> */
    public array $data = [];

    protected string $view = 'legal-pages::base-page';

    protected ?string $content = null;

    public function getTitle(): string
    {
        return $this->getPageType()->getLabel();
    }

    public function getMaxContentWidth(): Width|string
    {
        return Width::SevenExtraLarge;
    }

    public function mount(): void
    {
        $this->content = LegalPagesPlugin::Load($this->getPageType());

        abort_if(!$this->content, 404);
    }

    /**
     * @return Component[]
     *
     * @throws Exception
     */
    protected function getFormSchema(): array
    {
        return [
            TextEntry::make('content')
                ->hiddenLabel()
                ->markdown()
                ->state(fn () => $this->content),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    abstract public function getPageType(): LegalPageType;
}
