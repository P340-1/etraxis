<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017-2022 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Message\Fields;

use App\Entity\Enums\FieldPermissionEnum;

/**
 * Sets specified groups permission for the field.
 */
final class SetGroupsPermissionCommand
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(
        private readonly int $field,
        private readonly FieldPermissionEnum $permission,
        private readonly array $groups
    ) {
    }

    /**
     * @return int Field ID
     */
    public function getField(): int
    {
        return $this->field;
    }

    /**
     * @return FieldPermissionEnum Field permission
     */
    public function getPermission(): FieldPermissionEnum
    {
        return $this->permission;
    }

    /**
     * @return array Granted group IDs
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
