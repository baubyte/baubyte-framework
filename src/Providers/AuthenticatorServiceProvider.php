<?php

namespace Baubyte\Providers;

use Baubyte\Auth\Authenticators\Authenticator;
use Baubyte\Auth\Authenticators\SessionAuthenticator;

class AuthenticatorServiceProvider {
    public function registerServices() {
        match (config("auth.method", "session")) {
            "session" => singleton(Authenticator::class, SessionAuthenticator::class),
        };
    }
}
