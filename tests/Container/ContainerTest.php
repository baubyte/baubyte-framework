<?php

namespace Baubyte\Tests\Container;

use Baubyte\Container\Container;
use PHPUnit\Framework\TestCase;

interface MockInterface {
    public function mock(): string;
}

interface AnotherMockInterface {
    public function test(): int;
}

class MockClass implements MockInterface, AnotherMockInterface {
    public function __construct(public string $mock = "test", public int $test = 5) {
        $this->mock = $mock;
        $this->test = $test;
    }

    public function mock(): string {
        return $this->mock;
    }

    public function test(): int {
        return $this->test;
    }
}

class ContainerTest extends TestCase {
    public function test_resolves_basic_object() {
        Container::singleton(MockClass::class);
        $this->assertEquals(new MockClass(), Container::resolve(MockClass::class));
    }

    public function test_resolves_interface() {
        Container::singleton(MockInterface::class, MockClass::class);
        $this->assertEquals(new MockClass(), Container::resolve(MockInterface::class));
    }

    public function test_resolves_callback_built_object() {
        Container::singleton(AnotherMockInterface::class, fn () => new MockClass("value"));
        $this->assertEquals(new MockClass("value"), Container::resolve(AnotherMockInterface::class));
    }
}
