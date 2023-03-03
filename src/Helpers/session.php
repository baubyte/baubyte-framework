<?php

use Baubyte\Session\Session;

/**
 * Get session instance.
 */
function session(): Session {
    return app()->session;
}

/**
 * Get flashed error.
 *
 * @param string $field
 * @return string|null
 */
function error(string $field) {
    $errors = session()->get('_errors', [])[$field] ?? [];
    $keys = array_keys($errors);
    if (count($keys) > 0) {
        return $errors[$keys[0]];
    }

    return null;
}

/**
 * Old submitted data.
 *
 * @param string $key
 * @return string|null
 */
function old(string $field) {
    return session()->get('_old', [])[$field] ?? null;
}
