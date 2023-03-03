<?php

namespace Baubyte\Http;

class Controller {
    /**
     * View layout.
     */
    public ?string $layout = null;

    /**
     * Middleware behind this controller.
     *
     * @var \Baubyte\Http\Middleware[]
     */
    public array $middlewares = [];

    /**
     * Register middlewares.
     *
     * @return \Baubyte\Http\Middleware[]
     */
    public function middlewares(): array {
        return $this->middlewares;
    }

    /**
     * Run Middlewares.
     *
     * @param array $middlewares
     * @return self
     */
    public function setMiddlewares(array $middlewares): self {
        $this->middlewares = array_map(fn ($middleware) => new $middleware(), $middlewares);
        return $this;
    }
}
