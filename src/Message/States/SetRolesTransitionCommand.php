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

namespace App\Message\States;

/**
 * Sets state transition for the specified roles.
 */
final class SetRolesTransitionCommand
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(
        private readonly int $fromState,
        private readonly int $toState,
        private readonly array $roles
    ) {
    }

    /**
     * @return int State ID the transition goes from
     */
    public function getFromState(): int
    {
        return $this->fromState;
    }

    /**
     * @return int State ID the transition goes to
     */
    public function getToState(): int
    {
        return $this->toState;
    }

    /**
     * @return \App\Entity\Enums\SystemRoleEnum[] Granted system roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
