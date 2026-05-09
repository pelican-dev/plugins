<?php

namespace Boy132\Register\Filament\Pages\Auth;

use App\Extensions\Captcha\CaptchaService;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Illuminate\Validation\ValidationException;

class Register extends BaseRegister
{
    protected CaptchaService $captchaService;

    public function boot(CaptchaService $captchaService): void
    {
        $this->captchaService = $captchaService;
    }

    public function mount(): void
    {
        $this->abortIfRegistrationLimitReached();

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        $components = [
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ];

        if ($captchaComponent = $this->getCaptchaComponent()) {
            $components[] = $captchaComponent;
        }

        return $schema
            ->components($components);
    }

    private function getCaptchaComponent(): ?Component
    {
        return $this->captchaService->getActiveSchema()?->getFormComponent();
    }

    protected function getNameFormComponent(): Component
    {
        /** @var TextInput $parent */
        $parent = parent::getNameFormComponent();

        return $parent
            ->name('username')
            ->statePath('username')
            ->label(trans('profile.username'))
            ->unique($this->getUserModel(), 'username');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $this->throwIfRegistrationLimitReached(true);

            $user = parent::handleRegistration($data);

            $this->createDefaultUserResourceLimits($user);

            return $user;
        });
    }

    private function abortIfRegistrationLimitReached(): void
    {
        $maxUsers = (int) config('register.max_users', 0);

        if ($maxUsers > 0 && $this->getUserModel()::query()->count() >= $maxUsers) {
            abort(403, trans('register::messages.registration_closed'));
        }
    }

    private function throwIfRegistrationLimitReached(bool $lockForUpdate = false): void
    {
        $maxUsers = (int) config('register.max_users', 0);

        $query = $this->getUserModel()::query();

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        if ($maxUsers > 0 && $query->count() >= $maxUsers) {
            throw ValidationException::withMessages([
                'email' => trans('register::messages.registration_closed'),
            ]);
        }
    }

    private function createDefaultUserResourceLimits(Model $user): void
    {
        if (!class_exists(UserResourceLimits::class) || !SchemaFacade::hasTable('user_resource_limits')) {
            return;
        }

        UserResourceLimits::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'cpu' => (int) config('register.default_cpu', 0),
                'memory' => (int) config('register.default_memory', 0),
                'disk' => (int) config('register.default_disk', 0),
                'server_limit' => ((int) config('register.default_server_limit', 0)) ?: null,
            ],
        );
    }
}
