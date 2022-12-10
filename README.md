# Laravel Notion Viewer

This is a server side package for getting Notion page and blocks.

In order to render notion blocks in your application, please consider using npm
package [notion-viewer-client](https://www.npmjs.com/package/notion-viewer-client).

## Table Of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [TroubleShooting](#troubleshooting)

## Prerequisites

Requirements are:

```text
"illuminate/support": "^8.0|^9.0",
"php": "^8.1",
"illuminate/http": "^8.0|^9.0"
```

You need to provide API key for getting Notion API.

> If you don't know how to get API key, please consult [this documentation](Getting-Notion-API-Key.md).

## Installation

- Install package via composer

```bash
composer require laravel-notion-viewer
```

- Publish config file

```bash
php artisan vendor:publish --tag=laravel-notion-viewer
```

The following routes will be auto registered.

```php
/laravel-notion-viewer/data/{id}
/laravel-notion-viewer/link-preview
```

Config file will be

```php
'API_KEY' => env('NOTION_API_KEY'),

'API_VERSION' => env('NOTION_API_VERSION'), // default '2022-06-28'

'cache' => [
   // If you want to cache the response, set this to true
   'enabled' => false,

   // specify cache time in seconds
   'time' => 60,
]
```

## Usage

```php
use Zlt\LaravelNotionViewer\Notion\Client;
```

- ### Getting Notion Page

  ```php
  Client::getPage('page-id');
  ```

- ### Getting Blocks

  ```php
  Client::getBlocks('block-id');
  ```

- ### Getting Notion Page With Blocks

  ```php
  Client::getPageWithBlocks('page-id');
  ```

- ### Getting Recursive Blocks

  Some blocks have children blocks. In order to get all children blocks, use this method.

  ```php
  Client::getRecursiveBlocks('block-id');
  ```

- ### Getting Page with Recursive Blocks

  ```php
  Client::getPageWithRecursiveBlocks('page-id');
  ```

## TroubleShooting

- ### object_not_found error

  If you get `object_not_found` error, please check if you have given your integration access to your Notion Page.
  See [here](Getting-Notion-API-Key.md#giving-your-integration-access-to-your-notion-page).

## Support me

If you want to support me, buy me a coffee via **Binance**.

<img src="https://zawlintun.me/BinancePayQR.png" alt="binancePayQR" width="200"/>

## TODO

- [ ] Add Tests
- [ ] Add more features to cover all Notion API endpoints
