<?php

use PHPUnit\Framework\TestCase;
use Miladev\ApiResponse\ApiResponse;

class DummyClass
{
    use ApiResponse;
}

class ApiResponseTest extends TestCase
{
    public function testSuccessResponseDefault()
    {
        $d = new DummyClass();
        $resp = $d->successResponse(['foo' => 'bar'], 'ok');

        $this->assertEquals(200, $resp->getStatusCode());

        $content = json_decode($resp->getContent(), true);
        $this->assertIsArray($content);
        $this->assertEquals('success', $content['status']);
        $this->assertEquals('ok', $content['message']);
        $this->assertEquals(['foo' => 'bar'], $content['data']);
    }

    public function testFailResponseDefault()
    {
        $d = new DummyClass();
        $resp = $d->failResponse('bad', 422);

        $this->assertEquals(422, $resp->getStatusCode());

        $content = json_decode($resp->getContent(), true);
        $this->assertIsArray($content);
        $this->assertEquals('error', $content['status']);
        $this->assertEquals('bad', $content['message']);
    }

    public function testHeadersArePassedThrough()
    {
        $d = new DummyClass();
        $headers = ['X-Test' => 'value'];
        $resp = $d->successResponse([], '', 200, $headers);

        // ensure the custom header exists and that Content-Type is set
        $respHeaders = $resp->headers();
        $this->assertArrayHasKey('X-Test', $respHeaders);
        $this->assertEquals('value', $respHeaders['X-Test']);

        $this->assertArrayHasKey('Content-Type', $respHeaders);
        $this->assertStringContainsString('application/json', $respHeaders['Content-Type']);
    }
}
