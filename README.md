# Laravel Api Response

A small, framework-friendly trait that standardizes JSON API responses (success and error). The trait is lightweight and designed to be easy to override in consuming applications.

## Features

- Standardized JSON success and error responses
- Small and framework-agnostic: prefers framework response helpers when available
- Extensible: override payload shape or header handling

## Requirements

- PHP >= 8.1
- ext-json

## Installation

Install via Composer:

```bash
composer require miladev/api-response
```

## Quick usage (Laravel)

```php
<?php

namespace App\Http\Controllers;

use Miladev\ApiResponse\ApiResponse;

class TestController extends Controller
{
    use ApiResponse;

    public function successResponseTest()
    {
        $data = [
            'name' => 'milad',
            'job' => 'dev',
        ];

        // returns JSON with status=200 by default
        return $this->successResponse(data: $data, message: 'OK', statusCode: 200);
    }

    public function failResponseTest()
    {
        // returns JSON error with 404 status
        return $this->failResponse(message: 'Not found', statusCode: 404);
    }
}
```

## Overriding payload structure

If you want a different JSON structure (for example to include `meta` or to follow a specification), override `formatSuccessPayload` or `formatErrorPayload` in your class. Below is a fuller example showing a custom success payload and preserving type hints.

```php
<?php

use Miladev\ApiResponse\ApiResponse;

class MyApiController
{
    use ApiResponse;

    // Example: include a `meta` block and always wrap data under `result`
    protected function formatSuccessPayload($data, string $message): array
    {
        return [
            'status' => self::STATUS_SUCCESS,
            'message' => $message,
            'meta' => [
                'version' => '1.0',
                'timestamp' => time(),
            ],
            'result' => $data,
        ];
    }

    // Optionally override formatErrorPayload similarly to include error codes or details
}
```

This keeps the trait's public API (`successResponse`) the same while changing only the response shape.

## Overriding headers

If you need to inject or normalize headers (for example, pagination headers), override `prepareHeaders`. The example below calls the parent to keep the default Content-Type normalization and then adds pagination headers.

```php
<?php

use Miladev\ApiResponse\ApiResponse;

class PaginatedController
{
    use ApiResponse;

    protected function prepareHeaders(array $headers): array
    {
        // Call parent to normalize and ensure Content-Type
        $headers = parent::prepareHeaders($headers);

        // Add pagination metadata into headers
        $headers['X-Total-Count'] = '123';
        $headers['X-Per-Page'] = '25';

        return $headers;
    }
}
```

Note: `prepareHeaders` normalizes header values and ensures `Content-Type: application/json` if not provided.

## Non-Laravel / Testing

The trait prefers a `response()` factory when available (Laravel). For testing or non-framework usage you can provide a small response helper. The test bootstrap included in the project demonstrates this approach (it provides a minimal `response()` helper and a `TestResponse` object). That keeps unit tests fast and framework-independent.

## API reference

Public methods

- `successResponse($data = [], string $message = '', int $statusCode = 200, array $headers = [])`
  - Returns a standardized success JSON response. You can pass any payload as `$data`.

- `failResponse(string $message = '', int $statusCode = 400, array $headers = [])`
  - Returns a standardized error JSON response.

Protected / overridable helpers

- `formatSuccessPayload($data, string $message): array` — Customize success payload shape.
- `formatErrorPayload(string $message): array` — Customize error payload shape.
- `prepareHeaders(array $headers): array` — Normalize and add default headers.
- `createResponse($payload, int $statusCode, array $headers)` — Central response factory (you can override to integrate with custom response objects).

## Testing locally

To run tests locally (from project root):

```bash
composer install --no-interaction --prefer-dist
./vendor/bin/phpunit --configuration phpunit.xml
```

The tests use `tests/bootstrap.php` which provides a minimal `response()` helper so tests can run without Laravel.
