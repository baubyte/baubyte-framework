<?php

namespace Baubyte\Providers;

use Baubyte\Server\PhpNativeServer;
use Baubyte\Server\Server;

class ServerServiceProvider implements ServiceProvider {
    public function registerServices() {
        singleton(Server::class, PhpNativeServer::class);
    }
}
