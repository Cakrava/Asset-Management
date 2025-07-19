<?php

if (!function_exists('flexible_asset')) {
    function flexible_asset($path)
    {
        return env('APP_SECURE', false) ? secure_asset($path) : asset($path);
    }
}