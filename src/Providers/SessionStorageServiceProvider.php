<?php

namespace Baubyte\Providers;

use Baubyte\Session\PhpNativeSessionStorage;
use Baubyte\Session\SessionStorage;

class SessionStorageServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("session.storage", "native")) {
            "native" => singleton(SessionStorage::class, fn () => new PhpNativeSessionStorage()),
        };
    }
}
