<?php

namespace Zlt\LaravelNotionViewer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zlt\LaravelNotionViewer\Actions\GetLinkPreview;
use Zlt\LaravelNotionViewer\Notion\Client;

class LaravelNotionViewerController
{
    public static function routes()
    {
        Route::post('/laravel-notion-viewer/data/{id}', [static::class, 'getData']);
        Route::post('/laravel-notion-viewer/page/{id}', [static::class, 'getPage']);
        Route::post('/laravel-notion-viewer/blocks/{id}', [static::class, 'getBlocks']);
        Route::post('/laravel-notion-viewer/link-preview', [static::class, 'getLinkPreview']);
    }

    public function getData(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(Client::getPageWithRecursiveBlocks($id));
    }

    public function getPage(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(Client::getPage($id));
    }

    public function getBlocks(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(Client::getBlocks($id));
    }

    public function getLinkPreview(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->get('url')) {
            return response()->json('Please include url parameter.', 422);
        }
        return response()->json((new GetLinkPreview)(urldecode($request->get('url'))));
    }
}
