<?php

namespace notCharles\SnowflakesJs;

use App\Contracts\Plugins\HasPluginSettings;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Group;
use App\Traits\EnvironmentWriterTrait;

class SnowflakesJsPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'snowflakesjs';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        $schema = [
            Toggle::make('SNOWFLAKES_JS_ENABLED')
                ->label('Enable Snowflakes')
                ->columnSpanFull()
                ->live()
                ->default(fn () => config('snowflakesjs.enabled')),
            Slider::make('SNOWFLAKES_JS_SIZE')
                ->range(minValue: 0.5, maxValue: 4)
                ->decimalPlaces(1)
                ->step(0.1)
                ->label('Size')
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Size of the snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_JS_ENABLED'))
                ->default(fn () => config('snowflakesjs.size')),
            Slider::make('SNOWFLAKES_JS_SPEED')
                ->label('Speed')
                ->range(minValue: 0.5, maxValue: 3)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Speed of the snowflakes falling.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_JS_ENABLED'))
                ->default(fn () => config('snowflakesjs.speed')),
            Slider::make('SNOWFLAKES_JS_OPACITY')
                ->label('Opacity')
                ->range(minValue: 0.1, maxValue: 1)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('How well can you see through the snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_JS_ENABLED'))
                ->default(fn () => config('snowflakesjs.opacity')),
            Slider::make('SNOWFLAKES_JS_DENSITY')
                ->label('Density')
                ->range(minValue: 0.5, maxValue: 10)
                ->decimalPlaces(1)
                ->step(0.1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Page Density of the snowflakes. More densitiy, more snowflakes.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_JS_ENABLED'))
                ->default(fn () => config('snowflakesjs.density')),
            Slider::make('SNOWFLAKES_JS_QUALITY')
                ->label('Quality')
                ->range(minValue: 0.1, maxValue: 1)
                ->decimalPlaces(1)
                ->tooltips()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip('Higher quality may impact performance on some devices.')
                ->disabled(fn ($get) => !$get('SNOWFLAKES_JS_ENABLED'))
                ->default(fn () => config('snowflakesjs.quality')),
        ];

        return [
            Group::make()
                ->schema($schema)
                ->columns(2),
        ];
    }

    public function saveSettings(array $data): void
    {
        $data['SNOWFLAKES_JS_ENABLED'] = $data['SNOWFLAKES_JS_ENABLED'] ? 'true' : 'false';
        $this->writeToEnvironment($data);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
