<?php

return array(
    'locale'    => env('APP_LOCALE_COLUMN', 'locale'),
    'remember'  => env('DB_REMEMBER_COL', 'remember_token'),
    'secret'    => env('APP_KEY'),
    'session'   => 'user',
    'table'     => 'users',
    'emailColumn'     => 'email',
    'passwordColumn'     => 'password',
    'passwordTokenColumn'     => 'token',
    'statusColumn'     => 'status'
);