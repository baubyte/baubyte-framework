<?php

namespace Baubyte\Server;

use Baubyte\Http\HttpMethod;
use Baubyte\Http\Request;
use Baubyte\Http\Response;

/**
 * Similar to PHP `$_SERVER` but having an interface allows us to mockup these
 * global variables, useful for testing.
 */
interface Server {
    /**
     * Get request sent by the client.
     *
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * Send the response to the client.
     *
     * @param Response $response
     * @return void
     */
    public function sendResponse(Response $response);
}
