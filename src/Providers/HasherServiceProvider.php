<?php

namespace Baubyte\Providers;

use Baubyte\Crypto\Bcrypt;
use Baubyte\Crypto\Hasher;

class HasherServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("hashing.hasher", "bcrypt")) {
            "bcrypt" => singleton(Hasher::class, Bcrypt::class),
        };
    }
}
