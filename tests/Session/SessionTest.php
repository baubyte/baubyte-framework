<?php

namespace Baubyte\Tests\Session;

use Baubyte\Session\Session;
use Baubyte\Session\SessionStorage;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase {
    private function createMockSessionStorage() {
        $mock = $this->getMockBuilder(SessionStorage::class)->getMock();
        $mock->method("id")->willReturn("id");
        $mock->storage = [];
        $mock->method("has")->willReturnCallback(fn ($key) => isset($mock->storage[$key]));
        $mock->method("get")->willReturnCallback(fn ($key) => $mock->storage[$key] ?? null);
        $mock->method("set")->willReturnCallback(fn ($key, $value) => $mock->storage[$key] = $value);
        $mock->method("remove")->willReturnCallback(function ($key) use ($mock) {
            unset($mock->storage[$key]);
        });

        return $mock;
    }

    public function test_age_flash_data() {
        $mock = $this->createMockSessionStorage();

        $sessionOptions1 = new Session($mock);

        $sessionOptions1->set("test", "hello");

        $this->assertTrue(isset($mock->storage["test"]));

        // Comprobar datos flash
        $this->assertEquals(["old" => [], "new" => []], $mock->storage[$sessionOptions1::FLASH_KEY]);
        $sessionOptions1->flash("alert", "success");
        $this->assertEquals(["old" => [], "new" => ["alert"]], $mock->storage[$sessionOptions1::FLASH_KEY]);

        // Verifique que los datos flash aún estén configurados y que las claves estén envejecidas
        $sessionOptions1->__destruct();
        $this->assertTrue(isset($mock->storage["alert"]));
        $this->assertEquals(["old" => ["alert"], "new" => []], $mock->storage[$sessionOptions1::FLASH_KEY]);

        // Cree una nueva sesión y verifique los datos flash de la sesión anterior
        $sessionOptions2 = new Session($mock);
        $this->assertEquals(["old" => ["alert"], "new" => []], $mock->storage[$sessionOptions2::FLASH_KEY]);
        $this->assertTrue(isset($mock->storage["alert"]));

        // Destruya la sesión y verifique que se eliminen las claves flash
        $sessionOptions2->__destruct();
        $this->assertEquals(["old" => [], "new" => []], $mock->storage[$sessionOptions2::FLASH_KEY]);
        $this->assertFalse(isset($mock->storage["alert"]));
    }
}
