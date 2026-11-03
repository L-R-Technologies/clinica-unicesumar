<?php

use App\Http\Controllers\Agent\AgentCaptureController;
use Illuminate\Support\Facades\Route;

Route::prefix('agent')
    ->middleware(['lab.agent', 'throttle:120,1'])
    ->group(function () {
        Route::get('/ping', [AgentCaptureController::class, 'ping'])->name('agent.ping');
        Route::post('/lab-captures', [AgentCaptureController::class, 'store'])->name('agent.lab-captures.store');
    });
