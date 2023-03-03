<?php

namespace Baubyte\Providers;

use Baubyte\View\BaubyteEngine;
use Baubyte\View\View;

class ViewServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("view.engine", "baubyte")) {
            "baubyte" => singleton(View::class, fn () => new BaubyteEngine(config("view.path"))),
        };
    }
}
