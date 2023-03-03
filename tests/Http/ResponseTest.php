<?php

namespace Baubyte\Tests\Http;

use Baubyte\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
    public function test_json_response_is_constructed_correctly() {
        $content = ["bau" => "byte", "year" => 2022];
        $response = Response::json($content);

        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode($content), $response->content());
        $this->assertEquals(["content-type" => "application/json"], $response->headers());
    }

    public function test_text_response_is_constructed_correctly() {
        $content = "baubyte";
        $response = Response::text($content);

        $this->assertEquals(200, $response->status());
        $this->assertEquals($content, $response->content());
        $this->assertEquals(["content-type" => "text/plain"], $response->headers());
    }

    public function test_redirect_response_is_constructed_correctly() {
        $uri = "/test/redirect";
        $response = Response::redirect($uri);
        $this->assertEquals(302, $response->status());
        $this->assertNull($response->content());
        $this->assertEquals(["location" => $uri], $response->headers());
    }

    public function test_prepare_method_removes_content_headers_if_there_is_no_content() {
        $response = new Response();
        $response->setContentType("Baubyte");
        $response->setHeader("Content-Length", 10);
        $response->prepare();
        $this->assertEmpty($response->headers());
    }

    public function test_prepare_method_adds_content_length_header_if_there_is_content() {
        $content = "baubyte";
        $response = Response::text($content);
        $response->prepare();

        $this->assertEquals(strlen($content), $response->headers()["content-length"]);
    }
}
