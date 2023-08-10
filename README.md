# Laravel Api Response
Laravel Api Response

## Features

- Simple API Response

## Requirements

- Laravel 5+

## Installation
api-response is available via Composer

```bash
$ composer require miladev/api-response
```

## Usage

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
            'job' => 'dev'
        ];
        return $this->successResponse(data: $data,statusCode: 201);
    }
    
    public function failResponseTest()
    {
        return $this->failResponse(message: "Not found",statusCode: 404);
    }
}

```
