<?php

return [
    'locale'    => [
        // The application's default localization/language
        'default'   => env('APP_LOCALE_DEFAULT'),
        // The locale used by the app in case the default is left out
        'fallback'   => env('APP_LOCALE_FALLBACK'),
    ],
    'storage' => str_replace("config", "uploads/", dirname(__FILE__))
];
