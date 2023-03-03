<?php

namespace Baubyte\Auth\Authenticators;

use Baubyte\Auth\Authenticatable;

/**
 * Authentication method.
 */
class SessionAuthenticator implements Authenticator {
    /**
     * {@inheritdoc}
     */
    public function resolve(): ?Authenticatable {
        return session()->get("_auth");
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated(Authenticatable $authenticatable): bool {
        return session()->get("_auth")?->id() === $authenticatable->id();
    }

    /**
     * {@inheritdoc}
     */
    public function login(Authenticatable $authenticatable) {
        session()->set("_auth", $authenticatable);
    }

    /**
     * {@inheritdoc}
     */
    public function logout(Authenticatable $authenticatable) {
        session()->remove("_auth");
    }
}
