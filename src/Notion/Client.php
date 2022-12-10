<?php

namespace Zlt\LaravelNotionViewer\Notion;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Zlt\LaravelNotionViewer\Notion\Blocks\Blocks;

class Client
{
    private ?string $apiKey = null;

    private ?string $apiVersion;

    private static ?Client $instance = null;

    private ?bool $shouldCache;

    private ?int $cacheInSeconds;

    const BASE_URL = "https://api.notion.com/v1";

    private function __construct()
    {
        $this->apiKey = config('laravel-notion-viewer.API_KEY');

        $this->apiVersion = config('laravel-notion-viewer.API_VERSION', '2022-06-28');

        $this->shouldCache = config('laravel-notion-viewer.cache.enabled', false);

        $this->cacheInSeconds = config('laravel-notion-viewer.cache.time', 60);
    }

    private static function getInstance(): Client
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function blocks(): Blocks
    {
        $instance = static::getInstance();
        return new Blocks($instance->apiKey, $instance->apiVersion);
    }

    public static function getPage(string $id)
    {
        $instance = static::getInstance();
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $instance->apiKey,
            'Notion-Version' => $instance->apiVersion,
        ])
            ->get(static::BASE_URL . "/pages/{$id}")
            ->json();
    }
}
