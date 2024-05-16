<?php

namespace Francerz\AccessManager\Tests;

use Francerz\AccessManager\PermissionHelper;
use PHPUnit\Framework\TestCase;
use stdClass;

class PermissionHelperTest extends TestCase
{
    public function testToArray()
    {
        $this->assertEquals([], PermissionHelper::toArray(''));
        $this->assertEquals(['one'], PermissionHelper::toArray('one'));
        $this->assertEquals(['one two'], PermissionHelper::toArray('one two'));
        $this->assertEquals(['one', 'two'], PermissionHelper::toArray('one | two'));
        $this->assertEquals(['one two', 'three'], PermissionHelper::toArray('one two | three'));
        $this->assertEquals(['one two', 'two three'], PermissionHelper::toArray('one two | two three'));
    }

    public function testToString()
    {
        $this->assertEquals('', PermissionHelper::toString(null));
        $this->assertEquals('1', PermissionHelper::toString(1));
        $this->assertEquals('1 | 2', PermissionHelper::toString([1, 2]));
        $this->assertEquals('1 | 2', PermissionHelper::toString(['1', '2']));
        $this->assertEquals('1 | 2', PermissionHelper::toString(['1', ' 2']));
        $this->assertEquals('1 | 2', PermissionHelper::toString(['1 ', '2']));
        $this->assertEquals('1 | 2 | 3', PermissionHelper::toString(['1', '2', 3]));
        $this->assertEquals('1 | 2 | 3', PermissionHelper::toString(['1', '2', 2, 3]));
        $this->assertEquals('', PermissionHelper::toString(new stdClass()));
    }

    public function testMatch()
    {
        $this->assertTrue(PermissionHelper::match('read write delete', 'read write'));
        $this->assertTrue(PermissionHelper::match('read write delete', 'read write read'));
        $this->assertTrue(PermissionHelper::match('read write delete', 'write | execute'));
        $this->assertTrue(PermissionHelper::match('read write delete', 'execute | write'));
        $this->assertFalse(PermissionHelper::match('read write delete', ''));
        $this->assertFalse(PermissionHelper::match('read write delete', 'read create'));
        $this->assertFalse(PermissionHelper::match('read write delete', 'execute | create'));
    }

    public function testMerge()
    {
        $this->assertEquals('read', PermissionHelper::merge('read', ''));
        $this->assertEquals('read write', PermissionHelper::merge('read', 'write'));
        $this->assertEquals('read write', PermissionHelper::merge('read', 'write read'));
        $this->assertEquals('read write execute', PermissionHelper::merge('read', 'write read', 'write execute'));
    }
}
