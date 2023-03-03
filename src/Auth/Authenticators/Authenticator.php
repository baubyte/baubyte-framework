<?php

namespace Baubyte\Auth\Authenticators;

use Baubyte\Auth\Authenticatable;

interface Authenticator {
    /**
     * Log authenticatable model in.
     *
     * @param Authenticatable $authenticatable
     * @return void
     */
    public function login(Authenticatable $authenticatable);

    /**
     * Log authenticatable model out.
     *
     * @param Authenticatable $authenticatable
     * @return void
     */
    public function logout(Authenticatable $authenticatable);

    /**
     * Determines if the `$authenticatable` is authenticated.
     *
     * @param Authenticatable $authenticatable
     * @return boolean
     */
    public function isAuthenticated(Authenticatable $authenticatable): bool;

    /**
     * Get authenticatable from current request.
     *
     * @return Authenticatable|null
     */
    public function resolve(): ?Authenticatable;
}
