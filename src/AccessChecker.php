<?php

namespace Francerz\AccessManager;

/**
 * Class AccessChecker
 *
 * A class for checking if user is allowed a specific permission.
 */
class AccessChecker
{
    /**
     * The user grants provider.
     *
     * @var UserGrantsProviderInterface
     */
    private $userGrantsProvider;

    /**
     * Constructor.
     *
     * @param UserGrantsProviderInterface $userGrantsProvider The user grants provider.
     */
    public function __construct(UserGrantsProviderInterface $userGrantsProvider)
    {
        $this->userGrantsProvider = $userGrantsProvider;
    }

    /**
     * Check if the user is allowed a specific permission.
     *
     * @param string $allowedPermission The permission to check.
     * @return bool True if the user is allowed the permission. false otherwise.
     */
    public function isAllowed(string $allowedPermission): bool
    {
        return PermissionHelper::match(
            $this->userGrantsProvider->getUserGrants(),
            $allowedPermission
        );
    }
}
