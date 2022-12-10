<?php

namespace Zlt\LaravelNotionViewer\Notion\Blocks;

class Blocks
{
    protected bool $isRecursive = false;

    protected bool $shouldGetFullData = false;

    public function __construct(readonly protected string $token, readonly protected string $apiVersion)
    {
    }

    /**
     * @throws \Exception
     */
    public function get(string $blockId): array
    {
        $startCursor = null;
        $data = [];
        do {
            $blockResponse = $this->isRecursive ?
                $this->callRecursive($blockId, $startCursor) :
                $this->call($blockId, $startCursor);
            if (empty($blockResponse->blocks)) {
                break;
            }
            $data[] = $blockResponse->blocks;
            $startCursor = $this->shouldGetFullData && $blockResponse->hasMore ?
                $blockResponse->nextCursor : null;
            if (!$startCursor) {
                break;
            }
        } while (true);
        return array_merge(...$data);
    }

    /**
     * @throws \Exception
     */
    protected function callRecursive(string $blockId, string $startCursor = null, int $pageSize = 100): BlockResponse
    {
        $blockResponse = $this->call($blockId, $startCursor, $pageSize);
        $data = [];
        foreach ($blockResponse->blocks as $block) {
            if ($block->has_children) {
                $block->children = $this->callRecursive($block->id)->blocks;
            }
            $data[] = $block;
        }
        $blockResponse->blocks = $data;
        return $blockResponse;
    }

    /**
     * @throws \Exception
     */
    protected function call(string $blockId, string $startCursor = null, int $pageSize = 100): BlockResponse
    {
        $parameters = [
            'page_size' => $pageSize,
        ];
        if ($startCursor) {
            $parameters['start_cursor'] = $startCursor;
        }
        $ch = curl_init();
        $queryParameters = http_build_query($parameters);
        curl_setopt($ch, CURLOPT_URL, "https://api.notion.com/v1/blocks/$blockId/children?$queryParameters");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Notion-Version: ' . $this->apiVersion]);
        $result = curl_exec($ch);
        if (!$result) {
            throw new \Exception(curl_error($ch));
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($result, $header_size);
        $response = json_decode($body);
        if (empty($response->results)) {
            return new BlockResponse([]);
        }
        return new BlockResponse($response->results, $response->next_cursor, $response->has_more);
    }

    public function recursive(bool $shouldRecursive = true): static
    {
        $this->isRecursive = $shouldRecursive;
        return $this;
    }

    public function full(bool $shouldGetFullBlocks = true): static
    {
        $this->shouldGetFullData = $shouldGetFullBlocks;
        return $this;
    }
}
