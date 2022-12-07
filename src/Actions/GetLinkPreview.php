<?php

namespace Zlt\LaravelNotionViewer\Actions;

use Illuminate\Support\Facades\Http;

class GetLinkPreview
{
    public function __invoke(string $url, array $headers = []): array
    {
        $response = Http::timeout(60)->withHeaders($headers)->get($url);
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response->body());
        $path = new \DOMXPath($dom);
        return [
            'title' => $this->getTitle($path),
            'description' => $this->getDescription($path),
            'url' => $url,
            'icon' => $this->getIcon($path),
            'maskIcon' => $this->getMaskIcon($path),
        ];
    }

    // document.querySelector('title');
    public function getTitle(\DOMXPath $path): ?string
    {
        $elements = $path->query('//title');
        if ($elements->count() > 0) {
            return $elements->item(0)->textContent;
        }
        return null;
    }

    // document.querySelector('meta[name=description]');
    public function getDescription(\DOMXPath $path): ?string
    {
        $elements = $path->query('//meta[@name="description"]');
        if ($elements->count() > 0) {
            return $elements->item(0)->getAttribute('content');
        }
        return null;
    }

    // document.querySelector('link[rel=icon]');
    public function getIcon(\DOMXPath $path): ?string
    {
        $elements = $path->query('//link[@rel="icon"]');
        if ($elements->count() > 0) {
            return $elements->item(0)->getAttribute('href');
        }
        return null;
    }

    // document.querySelector('link[rel=mask-icon]');
    public function getMaskIcon(\DOMXPath $path): ?string
    {
        $elements = $path->query('//link[@rel="mask-icon"]');
        if ($elements->count() > 0) {
            return $elements->item(0)->getAttribute('href');
        }
        return null;
    }
}
