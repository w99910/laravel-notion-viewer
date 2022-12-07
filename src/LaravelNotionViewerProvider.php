<?php

namespace Zlt\LaravelNotionViewer;

use Illuminate\Support\ServiceProvider;

class LaravelNotionViewerProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->publishes([
            __DIR__ . '/../config/laravel-notion-viewer.php' => config_path('laravel-notion-viewer.php'),
        ], 'laravel-notion-viewer');
    }
}
