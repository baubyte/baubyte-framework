<?php

namespace Baubyte\Tests\Http;

use Baubyte\Http\HttpMethod;
use Baubyte\Http\Request;
use Baubyte\Routing\Route;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {
    public function test_request_returns_data_obtained_from_server_correctly() {
        $uri = '/ruta/test';
        $queryParams = ['a' => 1, 'b' => 2, 'test' => 'foo'];
        $postData = ['post' => 'test', 'foo' => 'bar'];

        $request = (new Request())
        ->setUri($uri)
        ->setMethod(HttpMethod::POST())
        ->setQueryParameters($queryParams)
        ->setPostData($postData);

        $this->assertEquals($uri, $request->uri());
        $this->assertEquals($queryParams, $request->query());
        $this->assertEquals($postData, $request->data());
        $this->assertEquals(HttpMethod::POST(), $request->method());
    }

    public function test_data_returns_value_if_key_is_given() {
        $data = ['test' => 5, 'bar' => 50, 'foo' => 30];
        $request = (new Request())->setPostData($data);

        $this->assertEquals($request->data('test'), 5);
        $this->assertEquals($request->data('bar'), 50);
        $this->assertNull($request->data("not exists"));
    }

    public function test_query_returns_value_if_key_is_given() {
        $data = ['test' => 5, 'bar' => 50, 'foo' => 30];
        $request = (new Request())->setQueryParameters($data);

        $this->assertEquals($request->query('test'), 5);
        $this->assertEquals($request->query('bar'), 50);
        $this->assertNull($request->query("not exists"));
    }

    public function test_route_parameters_returns_value_if_key_is_given() {
        $route = new Route('/test/{param}/foo/{bar}', fn () => "test");
        $request = (new Request())
            ->setRoute($route)
            ->setUri('/test/50/foo/20');

        $this->assertEquals($request->routeParameters('param'), 50);
        $this->assertEquals($request->routeParameters('bar'), 20);
        $this->assertNull($request->routeParameters("not exists"));
    }
}
