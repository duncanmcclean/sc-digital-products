<?php

use DoubleThreeDigital\DigitalProducts\Http\Controllers\VerificationController;

Route::post('/verification', [VerificationController::class, 'index'])
    ->name('verification.index');