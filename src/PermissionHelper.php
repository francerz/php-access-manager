<?php

namespace Francerz\AccessManager;

abstract class PermissionHelper
{
    /**
     * Convert a permission to an array representation.
     *
     * If the permission is empty, an empty array will be returned.
     * If the permission is a string, it will be split by white space into an
     * array.
     * If the permission is an object, it will be converted to a string.
     *
     * @param mixed $permission The permission to convert.
     * @return string[] The array representation of the permission, or an empty
     * array if unable to convert.
     */
    public static function toArray($permission): array
    {
        if (empty($permission)) {
            return [];
        }
        if (is_object($permission)) {
            $permission = (string)$permission;
        }
        if (is_string($permission)) {
            $permission = preg_replace('/\s+/', ' ', $permission);
            $permission = explode('|', $permission);
        }
        if (!is_array($permission)) {
            return [];
        }
        $permission = array_map('trim', $permission);
        return array_values(array_unique($permission));
    }

    /**
     * Convert a permission to a string representation.
     *
     * If the permission is an array, each element will be separated by white
     * space.
     *
     * @param mixed $permission The permission to convert.
     * @return string The string representation of the permission, or an empty
     * string if unable to convert.
     */
    public static function toString($permission): string
    {
        if (is_object($permission) && method_exists($permission, '__toString')) {
            $permission = (string)$permission;
        }
        if (is_scalar($permission)) {
            $permission = (string)$permission;
        }
        if (is_array($permission)) {
            $permission = implode(' | ', array_unique($permission));
        }
        if (!is_string($permission)) {
            return '';
        }
        return preg_replace('/\s+/', ' ', trim($permission));
    }

    public static function join($permission): string
    {
        if (is_object($permission) && method_exists($permission, '__toString')) {
            $permission = (string)$permission;
        }
        if (is_scalar($permission)) {
            $permission = (string)$permission;
        }
        if (is_array($permission)) {
            $permission = implode(' ', array_unique($permission));
        }
        if (!is_string($permission)) {
            return '';
        }
        return preg_replace('/\s+/', ' ', trim($permission));
    }

    /**
     * Undocumented function
     *
     * @param string|array|object $permission
     * @return array
     */
    private static function splitPermission($permission): array
    {
        $permission = static::toString($permission);

        $permission = preg_replace('/\s+/', ' ', $permission);
        $permission = explode(' ', $permission);
        $permission = array_map('trim', $permission);

        return array_values(array_unique($permission));
    }

    /**
     * Check if all of the user permissions match the provided match permissions.
     *
     * @param string|array|object $userPermissions The user's permissions.
     * @param string|array|object $matchPermissions The permissions to match
     * against.
     * @return bool True if all user permissions match, false otherwise.
     */
    public static function matchAll($userPermissions, $matchPermissions)
    {
        $userPermissions = static::splitPermission($userPermissions);
        $matchPermissions = static::splitPermission($matchPermissions);

        $matching = array_intersect($userPermissions, $matchPermissions);
        return count($matching) === count($matchPermissions);
    }

    /**
     * Undocumented function
     *
     * @param string|array|object $userPermissions
     * @param string|array|object $allowPermissions
     * @return bool
     */
    public static function match($userPermissions, $allowPermissions)
    {
        $userPermissions = static::toString($userPermissions);
        $allowPermissions = static::toArray($allowPermissions);

        foreach ($allowPermissions as $perm) {
            if (static::matchAll($userPermissions, $perm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Merge current permissions with new permissions.
     *
     * This function takes current permissions and new permissions, converts
     * them to arrays, merges them together, and returns the unique merged array
     * of permissions.
     *
     * @param string|array|object $current Current permissions to merge.
     * @param string|array|object $new New permissions to merge.
     * @return string The merged string of permissions.
     */
    public static function merge($current, ...$new)
    {
        $current = static::splitPermission($current);
        $new = array_map([static::class, 'splitPermission'], $new);

        return static::join(array_unique(array_merge($current, ...$new)));
    }
}
