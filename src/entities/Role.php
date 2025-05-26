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
use Override;

/**
 * psalm notice: https://psalm.dev/361
 * wyjaśnienie: psalm nie wie tego, że ta klasa może być nadpisana w projekcie używającym tej biblioteki
 * @psalm-suppress ClassMustBeFinal
 */
class Role implements RoleInterface
{
    private string $code = '';
    /** @var ?Role|?RoleInterface */
    private self|RoleInterface|null $parent = null;
    private string $name = '';
    /** @var PermissionInterface[] $permissions */
    private array $permissions = [];
    private bool $hasAllPermissions = false;
    public mixed $children = null;

    #[Override]
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

    #[Override]
    public function getParent(): ?RoleInterface
    {
        return $this->parent;
    }

    public function setParent(?RoleInterface $parent): void
    {
        $this->parent = $parent;
    }

    #[Override]
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

    #[Override]
    public function isHasAllPermissions(): bool
    {
        return $this->hasAllPermissions;
    }

    public function setHasAllPermissions(bool $hasAllPermissions): void
    {
        $this->hasAllPermissions = $hasAllPermissions;
    }
}
