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

namespace App\Message\Groups;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \App\Message\Groups\RemoveMembersCommand
 */
final class RemoveMembersCommandTest extends TestCase
{
    /**
     * @covers ::getGroup
     * @covers ::getUsers
     */
    public function testConstructor(): void
    {
        $users = [1, 2, 3];

        $command = new RemoveMembersCommand(1, $users);

        self::assertSame(1, $command->getGroup());
        self::assertSame($users, $command->getUsers());
    }
}
