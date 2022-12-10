<?php

namespace Zlt\LaravelNotionViewer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Zlt\LaravelNotionViewer\Actions\GetLinkPreview;
use Zlt\LaravelNotionViewer\Notion\Client;

class LaravelNotionViewerController
{
    public static function routes(): void
    {
        Route::post('/laravel-notion-viewer/data/{id}', [static::class, 'getData']);
        Route::post('/laravel-notion-viewer/link-preview', [static::class, 'getLinkPreview']);
    }

    public function getData(string $id): \Illuminate\Http\JsonResponse
    {
        $page = Client::getPage($id);
        $getBlocks = fn () => Client::blocks()->full()->recursive()->get($id);
        $blocks = config('laravel-notion-viewer.cache.enabled') ?
            Cache::remember(
                'laravel-notion-viewer-blocks-' . $id . $page['last_edited_time'],
                config('laravel-notion-viewer.cache.time'),
                $getBlocks
            )
            : $getBlocks();
        return response()->json([
            'page' => $page,
            'blocks' => $blocks,
        ]);
    }

    public function getLinkPreview(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->get('url')) {
            return response()->json('Please include url parameter.', 422);
        }
        return response()->json((new GetLinkPreview)(urldecode($request->get('url'))));
    }
}
