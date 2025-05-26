<?php
/**
 * This file is part of the EasySoft package.
 *
 * (c) Marcin Stodulski <marcin.stodulski@devsprint.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mstodulski\RbacWithPermissions\services;

use mstodulski\RbacWithPermissions\entities\Permission;
use mstodulski\RbacWithPermissions\entities\Role;
use mstodulski\RbacWithPermissions\interfaces\PermissionInterface;
use mstodulski\RbacWithPermissions\interfaces\RoleInterface;

/**
 * psalm notice: https://psalm.dev/361
 * wyjaśnienie: psalm nie wie tego, że ta klasa może być nadpisana w projekcie używającym tej biblioteki
 * @psalm-suppress ClassMustBeFinal
 */
class Authorization {

    private array $roles = [];
    private array $permissions = [];
    private array $rolesTree = [];
    private array $permissionsTree = [];
    private array $permissionsForRoles = [];

    public function defineRoles(RoleInterface ...$roles) : void
    {
        $this->roles = $roles;
    }

    public function definePermissions(PermissionInterface ...$permissions) : void
    {
        $this->permissions = $permissions;
    }

    public function processRolesAndPermissions() : void
    {
        $this->rolesTree = $this->buildTree($this->roles);
        $this->permissionsTree = $this->buildTree($this->permissions);
    }

    public function rolesHasPermission(array $roles, string $permissionCode) : bool
    {
        /** @var RoleInterface $role */
        foreach ($roles as $role)  {
            if ($this->roleHasPermission($role, $permissionCode)) {
                return true;
            }
        }
        return false;
    }

    public function roleHasPermission(RoleInterface $role, string $permissionCode): bool
    {
        if ($role->isHasAllPermissions()) {
            return true;
        } else {
            if (isset($this->permissionsForRoles[$role->getCode()])) {
                $permissionsArray = $this->permissionsForRoles[$role->getCode()];
            } else {
                [, $permissionsArray] = $this->getAllSubRolesAndPermissionsForRole($role);
                $this->permissionsForRoles[$role->getCode()] = $permissionsArray;
            }

            return in_array($permissionCode, $permissionsArray);
        }
    }

    public function setPermissionsTree(array $permissionsTree): void
    {
        $this->permissionsTree = $permissionsTree;
    }

    public function getPermissionsTree(): array
    {
        return $this->permissionsTree;
    }

    public function setRolesTree(array $rolesTree): void
    {
        $this->rolesTree = $rolesTree;
    }

    public function getRolesTree(): array
    {
        return $this->rolesTree;
    }

    public function getPermissionsForRoles(): array
    {
        return $this->permissionsForRoles;
    }

    public function setPermissionsForRoles(array $permissionsForRoles): void
    {
        $this->permissionsForRoles = $permissionsForRoles;
    }

    public function getAllSubRolesAndPermissionsForRole(RoleInterface $role): array
    {
        $rolesArray = [$role->getCode()];
        $permissionsArray = [];
        $this->getSubRolesCodes($rolesArray, $permissionsArray, $this->rolesTree);
        $this->getSubPermissionsForPermissions($permissionsArray, $this->permissionsTree);

        return [$rolesArray, $permissionsArray];
    }

    /**
     * @param array<array-key, string> $permissionsArray
     * @param null|array<array-key, PermissionInterface> $permissionsTree
     * @return void
     */
    private function getSubPermissionsForPermissions(array &$permissionsArray, ?array $permissionsTree = null) : void
    {
        if (is_array($permissionsTree) && count($permissionsTree) > 0) {
            foreach ($permissionsTree as $permission) {
                if (in_array($permission->getCode(), $permissionsArray)) {
                    /** @var Permission $permission */
                    if (isset($permission->children)) {
                        /** @var PermissionInterface $childPermission */
                        foreach ($permission->children as $childPermission) {
                            $permissionsArray[$childPermission->getCode()] = $childPermission->getCode();
                        }
                    }
                }

                /** @var Permission $permission */
                if (isset($permission->children)) {
                    $this->getSubPermissionsForPermissions($permissionsArray, $permission->children);
                }
            }
        }
    }

    /**
     * @param array<array-key, string> $rolesArray
     * @param array<array-key, mixed|string> $permissionsArray
     * @param null|array<array-key, RoleInterface> $rolesTree
     * @return void
     */
    private function getSubRolesCodes(array &$rolesArray, array &$permissionsArray, ?array $rolesTree = null) : void
    {
        if (is_array($rolesTree) && count($rolesTree) > 0) {
            foreach ($rolesTree as $role) {
                if (in_array($role->getCode(), $rolesArray)) {
                    foreach ($role->getPermissions() as $permission) {
                        $permissionsArray[$permission->getCode()] = $permission->getCode();
                    }

                    /** @var Role $role */
                    if (isset($role->children)) {
                        /** @var RoleInterface $childRole */
                        foreach ($role->children as $childRole) {
                            $rolesArray[] = $childRole->getCode();
                            foreach ($childRole->getPermissions() as $permission) {
                                $permissionsArray[$permission->getCode()] = $permission->getCode();
                            }
                        }
                    }
                }

                /** @var Role $role */
                if (isset($role->children)) {
                    $this->getSubRolesCodes($rolesArray, $permissionsArray, $role->children);
                }
            }
        }
    }

    private function buildTree(array $elements, ?string $parentId = null): array
    {
        $branch = array();

        /** @var RoleInterface|PermissionInterface $element */
        foreach ($elements as $element) {
            /** @var ?Role|?Permission $elementParent */
            $elementParent = $element->getParent();
            $elementParentId = ($elementParent !== null) ? $elementParent->getCode() : null;
            if ($elementParentId == $parentId) {
                $children = $this->buildTree($elements, $element->getCode());
                if ($children) {
                    /** @var Role|Permission $element */
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
