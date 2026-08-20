<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    Berkayk\OneSignal\OneSignalServiceProvider::class,
    App\Providers\ViewServiceProvider::class, // Adicione esta linha aqui
];
