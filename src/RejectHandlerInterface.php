<?php

namespace Francerz\AccessManager;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RejectHandlerInterface
{
    /**
     * Handle the response when access is denied due to no allowed permissions matching.
     *
     * @param ServerRequestInterface $request The HTTP request.
     * @return ResponseInterface The HTTP response for access denied.
     */
    public function handleReject(ServerRequestInterface $request): ResponseInterface;
}
