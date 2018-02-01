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

/**
 * @coversDefaultClass SebastianBergmann\Comparator\ResourceComparator
 *
 * @uses SebastianBergmann\Comparator\Comparator
 * @uses SebastianBergmann\Comparator\Factory
 * @uses SebastianBergmann\Comparator\ComparisonFailure
 */
class ResourceComparatorTest extends TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new ResourceComparator;
    }

    public function acceptsSucceedsProvider()
    {
        $tmpfile1 = \tmpfile();
        $tmpfile2 = \tmpfile();

        return [
          [$tmpfile1, $tmpfile1],
          [$tmpfile2, $tmpfile2],
          [$tmpfile1, $tmpfile2]
        ];
    }

    public function acceptsFailsProvider()
    {
        $tmpfile1 = \tmpfile();

        return [
          [$tmpfile1, null],
          [null, $tmpfile1],
          [null, null]
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        $tmpfile1 = \tmpfile();
        $tmpfile2 = \tmpfile();

        return [
          [$tmpfile1, $tmpfile1],
          [$tmpfile2, $tmpfile2]
        ];
    }

    public function assertEqualsFailsProvider()
    {
        $tmpfile1 = \tmpfile();
        $tmpfile2 = \tmpfile();

        return [
          [$tmpfile1, $tmpfile2],
          [$tmpfile2, $tmpfile1]
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
     * @covers       ::accepts
     * @dataProvider acceptsFailsProvider
     */
    public function testAcceptsFails($expected, $actual)
    {
        $this->assertFalse(
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

        $this->comparator->assertEquals($expected, $actual);
    }
}
