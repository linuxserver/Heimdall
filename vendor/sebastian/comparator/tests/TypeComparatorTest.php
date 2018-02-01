<?php
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Comparator;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\TypeComparator
 *
 * @uses SebastianBergmann\Comparator\Comparator
 * @uses SebastianBergmann\Comparator\Factory
 * @uses SebastianBergmann\Comparator\ComparisonFailure
 */
class TypeComparatorTest extends TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new TypeComparator;
    }

    public function acceptsSucceedsProvider()
    {
        return [
          [true, 1],
          [false, [1]],
          [null, new stdClass],
          [1.0, 5],
          ['', '']
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        return [
          [true, true],
          [true, false],
          [false, false],
          [null, null],
          [new stdClass, new stdClass],
          [0, 0],
          [1.0, 2.0],
          ['hello', 'world'],
          ['', ''],
          [[], [1,2,3]]
        ];
    }

    public function assertEqualsFailsProvider()
    {
        return [
          [true, null],
          [null, false],
          [1.0, 0],
          [new stdClass, []],
          ['1', 1]
        ];
    }

    /**
     * @covers       ::accepts
     * @dataProvider acceptsSucceedsProvider
     */
    public function testAcceptsSucceeds($expected, $actual)
    {
        $this->assertTrue(
          $this->comparator->accepts($expected, $actual)
        );
    }

    /**
     * @covers       ::assertEquals
     * @dataProvider assertEqualsSucceedsProvider
     */
    public function testAssertEqualsSucceeds($expected, $actual)
    {
        $exception = null;

        try {
            $this->comparator->assertEquals($expected, $actual);
        } catch (ComparisonFailure $exception) {
        }

        $this->assertNull($exception, 'Unexpected ComparisonFailure');
    }

    /**
     * @covers       ::assertEquals
     * @dataProvider assertEqualsFailsProvider
     */
    public function testAssertEqualsFails($expected, $actual)
    {
        $this->expectException(ComparisonFailure::class);
        $this->expectExceptionMessage('does not match expected type');

        $this->comparator->assertEquals($expected, $actual);
    }
}
