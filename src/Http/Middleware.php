<?php

namespace Baubyte\Http;

use Closure;

interface Middleware {
    /**
     *  Handle the request and return a response, or call the next middleware.
     *
     * @param \Baubyte\Http\Request $request
     * @param Closure $next
     * @return \Baubyte\Http\Response
     */
    public function handle(Request $request, Closure $next): Response;
}
