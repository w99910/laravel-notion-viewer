<?php

namespace Zlt\LaravelNotionViewer\Notion;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Notion-Version' => $this->apiVersion,
        ];
    }

    public static function getBlocks(string $id)
    {
        $instance = static::getInstance();
        $get = fn() => Http::withHeaders($instance->headers())
            ->get(static::BASE_URL . "/blocks/{$id}/children")
            ->json();
        if ($instance->shouldCache) {
            return Cache::remember("notion-blocks-{$id}", $instance->cacheInSeconds, $get);
        }
        return $get();
    }


    public static function getPage(string $id)
    {
        $instance = static::getInstance();
        $get = fn() => Http::withHeaders($instance->headers())
            ->get(static::BASE_URL . "/pages/{$id}")
            ->json();
        if ($instance->shouldCache) {
            return Cache::remember("notion-page-{$id}", $instance->cacheInSeconds, $get);
        }
        return $get();
    }

    public static function getRecursiveBlocks(string $id): array
    {
        $response = static::getBlocks($id);
        if (!isset($response['results'])) {
            return $response;
        }
        $blocks = $response['results'];
        return array_map(function ($block) {
            if ($block['has_children']) {
                $block['children'] = static::getRecursiveBlocks($block['id']);
            }
            return $block;
        }, $blocks);
    }


    public static function getPageWithBlocks(string $id): array
    {
        $page = static::getPage($id);
        $blocks = static::getBlocks($id);
        return [
            'page' => $page,
            'blocks' => $blocks,
        ];
    }

    public static function getPageWithRecursiveBlocks(string $id): array
    {
        $page = static::getPage($id);
        $blocks = static::getRecursiveBlocks($id);
        return [
            'page' => $page,
            'blocks' => $blocks,
        ];
    }
}
