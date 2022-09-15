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

namespace App\Entity;

use App\Entity\Enums\FieldTypeEnum;
use App\Entity\Enums\StateTypeEnum;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \App\Entity\ListItem
 */
final class ListItemTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::List);

        $item = new ListItem($field);
        self::assertSame($field, $item->getField());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid field type: Number');

        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::Number);

        new ListItem($field);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::List);
        $item  = new ListItem($field);

        $this->setProperty($item, 'id', 1);
        self::assertSame(1, $item->getId());
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::List);
        $item  = new ListItem($field);

        self::assertSame($field, $item->getField());
    }

    /**
     * @covers ::getItemValue
     * @covers ::setItemValue
     */
    public function testValue(): void
    {
        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::List);
        $item  = new ListItem($field);

        $item->setItemValue(7);
        self::assertSame(7, $item->getItemValue());
    }

    /**
     * @covers ::getItemText
     * @covers ::setItemText
     */
    public function testText(): void
    {
        $state = new State(new Template(new Project()), StateTypeEnum::Intermediate);
        $field = new Field($state, FieldTypeEnum::List);
        $item  = new ListItem($field);

        $item->setItemText('July');
        self::assertSame('July', $item->getItemText());
    }
}
