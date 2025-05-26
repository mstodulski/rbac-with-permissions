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
use Override;

/**
 * psalm notice: https://psalm.dev/361
 * wyjaśnienie: psalm nie wie tego, że ta klasa może być nadpisana w projekcie używającym tej biblioteki
 * @psalm-suppress ClassMustBeFinal
 */
class Permission implements PermissionInterface
{
    /** @var ?Permission|?PermissionInterface */
    private self|PermissionInterface|null $parent = null;
    private string $code = '';
    private string $name = '';
    public mixed $children = null;

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    #[Override]
    public function getCode(): string
    {
        return $this->code;
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
    public function getParent(): ?PermissionInterface
    {
        return $this->parent;
    }

    public function setParent(?PermissionInterface $parent): void
    {
        $this->parent = $parent;
    }
}
