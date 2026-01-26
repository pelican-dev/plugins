<?php

use App\Http\Middleware\Activity\ServerSubject;
use App\Http\Middleware\Api\Client\Server\AuthenticateServerAccess;
use App\Http\Middleware\Api\Client\Server\ResourceBelongsToServer;
use Boy132\PlayerCounter\Http\Controllers\Api\Client\Servers\PlayerCounterController;
use Boy132\PlayerCounter\Providers\PlayerCounterRoutesProvider;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('/servers/{server:uuid}')->middleware([ServerSubject::class, AuthenticateServerAccess::class, ResourceBelongsToServer::class])->group(function () {
    Route::prefix('/query')->middleware(ThrottleRequests::using(PlayerCounterRoutesProvider::QUERY_THROTTLE_KEY))->group(function () {
        Route::get('/', [PlayerCounterController::class, 'query']);
        Route::get('/players', [PlayerCounterController::class, 'players']);
    });
});
