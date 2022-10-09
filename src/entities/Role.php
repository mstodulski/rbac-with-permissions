<?php
/**
 * This file is part of the EasySoft package.
 *
 * (c) Marcin Stodulski <marcin.stodulski@devsprint.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mstodulski\RbacWithPermissions\entities;

use mstodulski\RbacWithPermissions\interfaces\PermissionInterface;
use mstodulski\RbacWithPermissions\interfaces\RoleInterface;

class Role implements RoleInterface
{
    private string $code = '';
    private ?self $parent = null;
    private string $name = '';
    /** @var PermissionInterface[] $permissions */
    private array $permissions = [];
    private bool $hasAllPermissions = false;
    public ?array $children = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getParent(): ?RoleInterface
    {
        return $this->parent;
    }

    public function setParent(?Role $parent): void
    {
        $this->parent = $parent;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function addPermission(PermissionInterface $permission): void
    {
        $this->permissions[$permission->getCode()] = $permission;
    }

    public function isHasAllPermissions(): bool
    {
        return $this->hasAllPermissions;
    }

    public function setHasAllPermissions(bool $hasAllPermissions): void
    {
        $this->hasAllPermissions = $hasAllPermissions;
    }
}
