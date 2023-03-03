<?php

namespace Baubyte\Http;

use Baubyte\Contracts\Enum;

/**
 * HTTP verb.
 */
final class HttpMethod extends Enum {
    /**
     * Verb GET.
     */
    private const GET = "GET";
    /**
     * Verb POST.
     */
    private const POST = "POST";
    /**
     * Verb PATCH.
     */
    private const PUT = "PUT";
    /**
     * Verb PATCH.
     */
    private const PATCH = "PATCH";
    /**
     * Verb DELETE.
     */
    private const DELETE = "DELETE";
}
