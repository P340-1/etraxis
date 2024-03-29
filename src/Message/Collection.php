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

namespace App\Message;

/**
 * A collection of items.
 */
class Collection
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(protected int $total, protected array $items)
    {
    }

    /**
     * @return int Total number of all available items (not only the retrieved ones)
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return array Retrieved subset of available items
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
