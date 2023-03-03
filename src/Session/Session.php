<?php

namespace Baubyte\Session;

/**
 * HTTP Session
 */
class Session {
    /**
     * Storage controller.
     *
     * @var SessionStorage
     */
    protected SessionStorage $storage;
    /**
     * Flash Key
     */
    public const FLASH_KEY = '_flash';

    /**
     * Constructor
     *
     * @param SessionStorage $storage
     */
    public function __construct(SessionStorage $storage) {
        $this->storage = $storage;
        $this->storage->start();
        if (!$this->storage->has(self::FLASH_KEY)) {
            $this->storage->set(self::FLASH_KEY, ['old' => [], 'new' => []]);
        }
    }

    /**
     * Handle flash data before destroying session.
     */
    public function __destruct() {
        foreach ($this->storage->get(self::FLASH_KEY)['old'] as $key) {
            $this->storage->remove($key);
        }
        $this->ageFlashData();
        $this->storage->save();
    }
    /**
     * Prepare session data to be removed for the next request.
     */
    public function ageFlashData() {
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['old'] = $flash['new'];
        $flash['new'] = [];
        $this->storage->set(self::FLASH_KEY, $flash);
    }

    /**
     * Flash key - value to current session.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function flash(string $key, mixed $value) {
        $this->storage->set($key, $value);
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['new'][] = $key;
        $this->storage->set(self::FLASH_KEY, $flash);
        return $this;
    }

    /**
     * Get the ID of the current session.
     *
     * @return string
     */
    public function id(): string {
        return $this->storage->id();
    }

    /**
     * Get a specific key from session storage.
     *
     * @param string $key
     * @param string $default Value to be returned if key is not present.
     * @return mixed
     */
    public function get(string $key, $default = null) {
        return $this->storage->get($key, $default);
    }

    /**
     * Set new key - value pair.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value) {
        $this->storage->set($key, $value);
    }

    /**
     * Check if a specific key exists in the session.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool {
        return $this->storage->has($key);
    }

    /**
     * Remove specific key exists in the session.
     *
     * @param string $key
     */
    public function remove(string $key) {
        $this->storage->remove($key);
    }

    /**
     * Destroy session.
     */
    public function destroy() {
        $this->storage->destroy();
    }
}
