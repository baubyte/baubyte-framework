<?php

namespace Baubyte\Session;

/**
 * Session Driver interface
 */
interface SessionStorage {
    /**
     * Load session data.
     */
    public function start();

    /**
     * Get the ID of the current session.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Get a specific key from session storage.
     *
     * @param string $key
     * @param string $default Value to be returned if key is not present.
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set new key - value pair.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value);

    /**
     * Check if a specific key exists in the session.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool;

    /**
     * Remove specific key exists in the session.
     *
     * @param string $key
     */
    public function remove(string $key);

    /**
     * Destroy session.
     */
    public function destroy();

    /**
     * Write the session data to make it persistent.
     */
    public function save();
}
