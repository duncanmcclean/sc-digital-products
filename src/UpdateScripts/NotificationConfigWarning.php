<?php

namespace DoubleThreeDigital\DigitalProducts\UpdateScripts;

use Illuminate\Support\Facades\Log;
use Statamic\UpdateScripts\UpdateScript;

class NotificationConfigWarning extends UpdateScript
{
    public function shouldUpdate($newVersion, $oldVersion)
    {
        return $this->isUpdatingTo('2.2.0');
    }

    public function update()
    {
        $message = "[SC Digital Products] BREAKING CHANGE: Please review breaking changes introduced in v2.2.0 https://github.com/doublethreedigital/sc-digital-products/releases/tag/v2.2.0";

        dump($message);
        Log::warning($message);
    }
}
