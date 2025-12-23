<?php

namespace Boy132\Snowflakes;

use App\Contracts\Plugins\HasPluginSettings;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Group;
use App\Traits\EnvironmentWriterTrait;

class SnowflakesPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'snowflakes';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        $schema = [
            Toggle::make('SNOWFLAKES_ENABLED')
                ->label('Enable Snowflakes')
                ->columnSpanFull()
                ->live()
                ->default(fn () => config('snowflakes.enabled')),
            Slider::make('SNOWFLAKES_SIZE')
                ->range(minValue: 0.5, maxValue: 4)
                ->decimalPlaces(1)
                ->step(0.1)
                ->label('Size')
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Size of the snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_ENABLED'))
                ->default(fn () => config('snowflakes.size')),
            Slider::make('SNOWFLAKES_SPEED')
                ->label('Speed')
                ->range(minValue: 0.5, maxValue: 3)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Speed of the snowflakes falling.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_ENABLED'))
                ->default(fn () => config('snowflakes.speed')),
            Slider::make('SNOWFLAKES_OPACITY')
                ->label('Opacity')
                ->range(minValue: 0.1, maxValue: 1)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('How well can you see through the snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_ENABLED'))
                ->default(fn () => config('snowflakes.opacity')),
            Slider::make('SNOWFLAKES_DENSITY')
                ->label('Density')
                ->range(minValue: 0.5, maxValue: 10)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Page density of the snowflakes. More density, more snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_ENABLED'))
                ->default(fn () => config('snowflakes.density')),
            Slider::make('SNOWFLAKES_QUALITY')
                ->label('Quality')
                ->range(minValue: 0.1, maxValue: 1)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Higher quality may impact performance on some devices.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_ENABLED'))
                ->default(fn () => config('snowflakes.quality')),
        ];

        return [
            Group::make()
                ->schema($schema)
                ->columns(),
        ];
    }

    public function saveSettings(array $data): void
    {
        $data['SNOWFLAKES_ENABLED'] = $data['SNOWFLAKES_ENABLED'] ? 'true' : 'false';
        $this->writeToEnvironment($data);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
