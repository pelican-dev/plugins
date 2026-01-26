<?php

namespace Boy132\LegalPages\Providers;

use Boy132\LegalPages\Enums\LegalPageType;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class LegalPagesRouteProvider extends RouteServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            foreach (LegalPageType::cases() as $legalPageType) {
                Route::get($legalPageType->getId(), $legalPageType->getClass())->name('legal-pages.' . $legalPageType->getId())->withoutMiddleware(['auth']);
            }
        });
    }
}
