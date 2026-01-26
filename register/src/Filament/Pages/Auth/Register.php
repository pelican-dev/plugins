<?php

namespace Boy132\Register\Filament\Pages\Auth;

use App\Extensions\Captcha\CaptchaService;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected CaptchaService $captchaService;

    public function boot(CaptchaService $captchaService): void
    {
        $this->captchaService = $captchaService;
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
            ->statePath('username');
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (config('register.enable_email_verification')) {
            if (!is_null($user->email_verified_at) || !method_exists($user, 'notify')) {
                return;
            }

            $notification = app(VerifyEmail::class);
            $notification->url = Filament::getVerifyEmailUrl($user);

            $user->notify($notification);
        }
    }
}
