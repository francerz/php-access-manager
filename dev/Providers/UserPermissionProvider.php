<?php

namespace Francerz\AccessManager\Dev\Providers;

use Francerz\AccessManager\UserGrantsProviderInterface;

class UserPermissionProvider implements UserGrantsProviderInterface
{
    private $grants;

    public function __construct(string $grants)
    {
        $this->grants = $grants;
    }

    public function getUserGrants(): string
    {
        return $this->grants;
    }
}
