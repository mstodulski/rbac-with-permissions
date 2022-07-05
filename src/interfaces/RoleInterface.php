<?php
/**
 * This file is part of the EasySoft package.
 *
 * (c) Marcin Stodulski <marcin.stodulski@devsprint.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mstodulski\RbacWithPermissions\interfaces;

interface RoleInterface
{
    public function getParent(): ?RoleInterface;
    public function getCode(): string;
    public function getPermissions(): array;
    public function isHasAllPermissions(): bool;
}
