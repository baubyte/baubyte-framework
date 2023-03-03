
<?php

use Baubyte\App;
use Baubyte\Config\Config;
use Baubyte\Container\Container;

/**
 * Easy access to singletons.
 */
function app(string $class = App::class) {
    return Container::resolve($class);
}

/**
 * Create singleton.
 *
 * @param string $class
 * @param callable|string|null $build
 */
function singleton(string $class, callable|string|null $build = null) {
    return Container::singleton($class, $build);
}

/**
 * Resources directory containing
 * views, css, and other static files.
 *
 * @return string
 */
function resourcesDirectory(): string {
    return App::$root.DIRECTORY_SEPARATOR."resources";
}

/**
 * Get environment variable value.
 *
 * @param string $key
 * @param string $default Value to return if env variable does not exits.
 */
function env(string $variable, $default = null) {
    return $_ENV[$variable] ?? $default;
}

/**
 * Get configuration value.
 *
 * @param string $configuration Path to final key.
 * @param mixed $default Value to be returned if key does not exist.
 * @return mixed
 */
function config(string $configuration, $default = null) {
    return Config::get($configuration, $default);
}

/**
 * Get template as string from /resources/templates.
 *
 * @param string $name
 * @param string|null $directory
 * @return string
 */
function template(string $name, ?string $directory = null): string {
    $directory ??= resourcesDirectory().DIRECTORY_SEPARATOR."/templates";

    $file = "{$directory}".DIRECTORY_SEPARATOR."{$name}.php";

    if (!file_exists($file)) {
        return null;
    }

    return file_get_contents($file);
}

/**
 * Dump variables and exit.
 *
 * @param array ...$args
 * @return \Baubyte\Http\Response $response
 */
function debug(...$args) {
    app()->abort(view("baubyte/debug", compact('args'), "error"));
}
