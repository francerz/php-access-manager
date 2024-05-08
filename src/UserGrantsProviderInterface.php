<?php

namespace Francerz\AccessManager;

/**
 * Interface UserGrantsProviderInterface
 *
 * Defines a contract for classes tha provide user grants.
 */
interface UserGrantsProviderInterface
{
    /**
     * Get the user grants as a white space separated string.
     *
     * @return string A white space separated string containing the user
     * grants keywords.
     */
    public function getUserGrants(): string;
}
