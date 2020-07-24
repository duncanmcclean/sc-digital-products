<?php

use DoubleThreeDigital\DigitalDownloads\Http\Controllers\DownloadController;

Route::get('/download/{order_id}/{item_id}', [DownloadController::class, 'show'])
    ->name('digital-downloads.download');