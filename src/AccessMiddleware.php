<?php

namespace Francerz\AccessManager;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for access control based on user permissions.
 */
class AccessMiddleware implements MiddlewareInterface
{
    /** @var UserGrantsProviderInterface The user permission provider. */
    private $userGrantsProvider;
    /** @var string The allowed permissions. */
    private $allowedPermissions;
    /** @var RejectHandlerInterface The access denied handler. */
    private $rejectHandler;

    /**
     * Constructor.
     *
     * @param UserGrantsProviderInterface $userGrantsProvider The user permission provider.
     */
    public function __construct(
        UserGrantsProviderInterface $userGrantsProvider,
        ResponseFactoryInterface $responseFactoryInterface
    ) {
        $this->userGrantsProvider = $userGrantsProvider;
        $this->allowedPermissions = '';
        $this->rejectHandler = new DefaultRejectHandler($responseFactoryInterface);
    }

    public function __toString()
    {
        return $this->getAllowedPermissions();
    }

    /**
     * Specify the permissions allowed by this middleware.
     *
     * @param string|string[] $permissions The allowed permissions.
     * @return self A clone of this middleware with the specified permissions.
     */
    public function allow(...$permissions)
    {
        foreach ($permissions as &$p) {
            $p = PermissionHelper::join($p);
        }
        $permissions = PermissionHelper::toString($permissions);
        $clone = clone $this;
        $clone->allowedPermissions = $permissions;
        return $clone;
    }

    /**
     * @param RejectHandlerInterface $rejectHandler
     * @return void
     */
    public function withRejectHandler(RejectHandlerInterface $rejectHandler)
    {
        $clone = clone $this;
        $clone->rejectHandler = $rejectHandler;
        return $clone;
    }

    public function getAllowedPermissions(): string
    {
        return $this->allowedPermissions;
    }

    /**
     * Process the HTTP request.
     *
     * This methods checks if the user permissions match any of the allowed permissions.
     * If a match is found, it delegates handling of the request to the provided handler.
     * If not match is found, it returns a response indicating access is forbidden.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userGrants = $this->userGrantsProvider->getUserGrants();
        if (PermissionHelper::match($userGrants, $this->allowedPermissions)) {
            return $handler->handle($request);
        }
        return $this->rejectHandler->handleReject($request);
    }
}
