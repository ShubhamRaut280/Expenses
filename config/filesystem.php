<?php

return [
    'default'   => 'uploads',
    'disks'     => [
        'language'  => str_replace('config', 'lang', __DIR__),
        'storage'   => str_replace('config', 'uploads', __DIR__),
        'views'     => str_replace('config', 'views', __DIR__),
        'env'       => str_replace('config','',__DIR__.'.env')
    ]
];
