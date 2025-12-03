<?php
// tests/bootstrap.php

// Try to load composer's autoload
$autoloadFiles = [__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../autoload.php'];
foreach ($autoloadFiles as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

// If autoload not found, register a minimal PSR-4 autoloader for Miladev\\ApiResponse\\
if (!class_exists('Miladev\\ApiResponse\\ApiResponse')) {
    spl_autoload_register(function ($class) {
        $prefix = 'Miladev\\ApiResponse\\';
        $base_dir = __DIR__ . '/../src/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

// Minimal Response object and response() helper for non-Laravel environment
if (!function_exists('response')) {
    class TestResponse
    {
        public $content;
        public $status;
        public $headers;

        public function __construct($content = null, $status = 200, $headers = [])
        {
            $this->content = $content;
            $this->status = $status;
            $this->headers = $headers;
        }

        public function getStatusCode()
        {
            return $this->status;
        }

        public function getContent()
        {
            // emulate Laravel response -> getContent returning JSON string
            if (is_array($this->content) || is_object($this->content)) {
                return json_encode($this->content);
            }
            return (string)$this->content;
        }

        public function headers()
        {
            return $this->headers;
        }
    }

    function response($content = null, $status = 200, $headers = [])
    {
        return new TestResponse($content, $status, $headers);
    }
}
