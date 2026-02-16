<?php

namespace Francerz\AccessManager\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Francerz\AccessManager\AccessMiddleware;
use Francerz\AccessManager\Dev\Handlers\BasicHandler;
use Francerz\AccessManager\Dev\Providers\UserPermissionProvider;
use Francerz\Http\HttpFactory;
use PHPUnit\Framework\TestCase;

class AccessMiddlewareTest extends TestCase
{
    public function testProcess()
    {
        $httpFactory = new HttpFactory();

        $serverRequest = $httpFactory->createServerRequest('GET', 'http://example.com/test');
        $handler = new BasicHandler($httpFactory);
        $permissionProvider = new UserPermissionProvider('read write delete');

        $accessMiddleware = new AccessMiddleware($permissionProvider, $httpFactory);

        // Permission 'read' AND 'execute'
        $accessMiddleware = $accessMiddleware->allow('read execute');
        $response = $accessMiddleware->process($serverRequest, $handler);
        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());

        // Permission 'read' OR 'execute'
        $accessMiddleware = $accessMiddleware->allow('read | execute');
        $response = $accessMiddleware->process($serverRequest, $handler);
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        // Permission 'execute' OR 'read'
        $accessMiddleware = $accessMiddleware->allow('execute | read');
        $response = $accessMiddleware->process($serverRequest, $handler);
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        // Permission ('read' AND 'write') OR ('create' AND 'execute')
        $accessMiddleware = $accessMiddleware->allow('read write | create execute');
        $response = $accessMiddleware->process($serverRequest, $handler);
        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        // Permission ('read' AND 'execute') OR ('create' AND 'write')
        $accessMiddleware = $accessMiddleware->allow('read execute | create write');
        $response = $accessMiddleware->process($serverRequest, $handler);
        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }

    public function testAllow()
    {
        $httpFactory = new HttpFactory();
        $permissionProvider = new UserPermissionProvider('read write delete');
        $accessMiddleware = new AccessMiddleware($permissionProvider, $httpFactory);

        $this->assertEquals('read', (string)$accessMiddleware->allow('read'));
        $this->assertEquals('read write', (string)$accessMiddleware->allow(['read', 'write']));
        $this->assertEquals('read | write', (string)$accessMiddleware->allow('read', 'write'));
        $this->assertEquals('read | write execute', (string)$accessMiddleware->allow('read', ['write', 'execute']));
        $this->assertEquals('read write | execute', (string)$accessMiddleware->allow(['read', 'write'], 'execute'));
        $this->assertEquals('read write | execute', (string)$accessMiddleware->allow('read write | execute'));
        $this->assertEquals('read write | write execute', (string)$accessMiddleware->allow('read write', ['write', 'execute']));

    }
}
