<?php

namespace Baubyte\Config;

/**
 * Application runtime configuration.
 */
class Config {
    /**
     * Configurations container.
     *
     * @var array<string, mixed>
     */
    private static array $config = [];

    /**
     * Load configurations from a directory containing Lune config files.
     *
     * @param string $path Full path to config directory.
     * @return void
     */
    public static function load(string $path) {
        foreach (glob("$path".DIRECTORY_SEPARATOR."*.php") as $config) {
            $key = explode(".", basename($config))[0];
            $values = require_once $config;
            self::$config[$key] = $values;
        }
    }

    /**
     * Get configuration key.
     *
     * @param string $configuration path to final key: `"app.custom.key"`.
     * @param mixed $default Default value to return if key not found.
     * @return mixed
     */
    public static function get(string $configuration, $default = null) {
        $keys = explode(".", $configuration);
        $finalKey = array_pop($keys);
        $array = self::$config;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return $default;
            }
            $array = $array[$key];
        }

        return $array[$finalKey] ?? $default;
    }
}
