<?php

declare(strict_types=1);

namespace Miladev\ApiResponse;

/**
 * Trait ApiResponse
 *
 * Provides a small, consistent way to return JSON API responses.
 * Designed to be lightweight and easy to override in consuming applications.
 */
trait ApiResponse
{
    // Status labels
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    /**
     * Return a standardized success response.
     *
     * @param mixed  $data       The response payload (array, object, scalar)
     * @param string $message    Optional human-readable message
     * @param int    $statusCode HTTP status code (default 200)
     * @param array  $headers    Additional HTTP headers to send
     *
     * @return mixed
     */
    public function successResponse($data = [], string $message = '', int $statusCode = 200, array $headers = [])
    {
        $payload = $this->formatSuccessPayload($data, $message);
        $headers = $this->prepareHeaders($headers);

        return $this->createResponse($payload, $statusCode, $headers);
    }

    /**
     * Return a standardized error response.
     *
     * @param string $message    Human-readable error message
     * @param int    $statusCode HTTP status code (default 400)
     * @param array  $headers    Additional HTTP headers to send
     *
     * @return mixed
     */
    public function failResponse(string $message = '', int $statusCode = 400, array $headers = [])
    {
        $payload = $this->formatErrorPayload($message);
        $headers = $this->prepareHeaders($headers);

        return $this->createResponse($payload, $statusCode, $headers);
    }

    /**
     * Format the success payload. Override this in your class if you want custom structure.
     *
     * @param mixed  $data
     * @param string $message
     *
     * @return array
     */
    protected function formatSuccessPayload($data, string $message): array
    {
        return [
            'status' => self::STATUS_SUCCESS,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Format the error payload. Override this in your class if you want custom structure.
     *
     * @param string $message
     *
     * @return array
     */
    protected function formatErrorPayload(string $message): array
    {
        return [
            'status' => self::STATUS_ERROR,
            'message' => $message,
        ];
    }

    /**
     * Prepare headers ensuring we have a valid array and a sensible Content-Type.
     * Override to customize header handling.
     *
     * @param array $headers
     *
     * @return array
     */
    protected function prepareHeaders(array $headers): array
    {
        // Normalize header keys to strings and values to strings where possible
        $normalized = [];
        foreach ($headers as $k => $v) {
            // preserve numeric keys (rare) but cast key to string
            $key = (string)$k;
            if (is_array($v)) {
                // For multiple header values, join by comma
                $value = implode(', ', $v);
            } else {
                $value = (string)$v;
            }
            $normalized[$key] = $value;
        }

        // Ensure JSON content type if not provided
        $hasContentType = false;
        foreach ($normalized as $k => $v) {
            if (strcasecmp($k, 'content-type') === 0) {
                $hasContentType = true;
                break;
            }
        }

        if (! $hasContentType) {
            $normalized['Content-Type'] = 'application/json';
        }

        return $normalized;
    }

    /**
     * Create a response using the framework response helpers when available.
     * This centralizes response creation and handles JSON encoding fallbacks.
     *
     * @param mixed $payload
     * @param int   $statusCode
     * @param array $headers
     *
     * @return mixed
     */
    protected function createResponse($payload, int $statusCode, array $headers)
    {
        // If a framework response factory is present, prefer its JSON helper.
        if (function_exists('response')) {
            $responseFactory = response();
            if (is_object($responseFactory)) {
                if (method_exists($responseFactory, 'json')) {
                    return $responseFactory->json($payload, $statusCode, $headers);
                }

                if (method_exists($responseFactory, 'make')) {
                    // Some frameworks offer make() to create responses
                    return $responseFactory->make($payload, $statusCode, $headers);
                }
            }
        }

        // Ensure payload is a JSON string for plain response() fallback.
        if (!is_string($payload)) {
            try {
                $payload = json_encode(
                    $payload,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
            } catch (\JsonException $e) {
                // If encoding fails, return a minimal error JSON and 500 status
                $payload = json_encode([
                    'status' => self::STATUS_ERROR,
                    'message' => 'Failed to encode response payload',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $statusCode = 500;
            }
        }

        return response($payload, $statusCode, $headers);
    }
}