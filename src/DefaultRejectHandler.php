<?php

namespace Francerz\AccessManager;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Default implementation of RejectHandlerInterface.
 *
 * This handler returns a 403 Forbidden response with a plain text message
 * "Access Forbidden".
 */
class DefaultRejectHandler implements RejectHandlerInterface
{
    /** @var ResponseFactoryInterface The response factory. */
    private $responseFactory;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory The response factory.
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handle the access denied scenario.
     *
     * Generates a 403 Forbidden response with a plain text message "Access Forbidden".
     *
     * @param ServerRequestInterface $request The HTTP request.
     * @return ResponseInterface The HTTP response for access denied.
     */
    public function handleReject(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory
            ->createResponse(StatusCodeInterface::STATUS_FORBIDDEN, 'Access Forbidden')
            ->withHeader('Content-Type', 'text/plain');
        $response->getBody()->write('Access Forbidden');
        return $response;
    }
}
