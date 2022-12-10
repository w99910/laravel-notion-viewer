<?php

namespace Zlt\LaravelNotionViewer\Notion\Blocks;

class BlockResponse
{
    public function __construct(public array $blocks, public ?string $nextCursor = null, public ?bool $hasMore = null)
    {
    }
}
