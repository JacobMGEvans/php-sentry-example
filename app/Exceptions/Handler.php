<?php

use Sentry\Laravel\Integration;
function register(): void
{
    $this->reportable(function (Throwable $e) {
        Integration::captureUnhandledException($e);
    });
}

