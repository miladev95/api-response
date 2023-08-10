<?php
namespace Miladev\ApiResponse;

use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ApiResponse', function () {
            return 0;
        });
    }
}
