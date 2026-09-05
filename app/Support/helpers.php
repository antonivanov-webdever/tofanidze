<?php

use App\Support\Site;

if (! function_exists('site')) {
    /**
     * Resolve a site setting, or the Site instance itself when called without arguments.
     */
    function site(?string $key = null, mixed $default = null): mixed
    {
        $site = app(Site::class);

        return $key === null ? $site : $site->get($key, $default);
    }
}
