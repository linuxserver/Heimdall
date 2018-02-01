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

use \Exception;
use \RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\ExceptionComparator
 *
 * @uses SebastianBergmann\Comparator\Comparator
 * @uses SebastianBergmann\Comparator\Factory
 * @uses SebastianBergmann\Comparator\ComparisonFailure
 */
class ExceptionComparatorTest extends TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new ExceptionComparator;
        $this->comparator->setFactory(new Factory);
    }

    public function acceptsSucceedsProvider()
    {
        return [
          [new Exception, new Exception],
          [new RuntimeException, new RuntimeException],
          [new Exception, new RuntimeException]
        ];
    }

    public function acceptsFailsProvider()
    {
        return [
          [new Exception, null],
          [null, new Exception],
          [null, null]
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        $exception1 = new Exception;
        $exception2 = new Exception;

        $exception3 = new RuntimeException('Error', 100);
        $exception4 = new RuntimeException('Error', 100);

        return [
          [$exception1, $exception1],
          [$exception1, $exception2],
          [$exception3, $exception3],
          [$exception3, $exception4]
        ];
    }

    public function assertEqualsFailsProvider()
    {
        $typeMessage  = 'not instance of expected class';
        $equalMessage = 'Failed asserting that two objects are equal.';

        $exception1 = new Exception('Error', 100);
        $exception2 = new Exception('Error', 101);
        $exception3 = new Exception('Errors', 101);

        $exception4 = new RuntimeException('Error', 100);
        $exception5 = new RuntimeException('Error', 101);

        return [
          [$exception1, $exception2, $equalMessage],
          [$exception1, $exception3, $equalMessage],
          [$exception1, $exception4, $typeMessage],
          [$exception2, $exception3, $equalMessage],
          [$exception4, $exception5, $equalMessage]
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
    public function testAssertEqualsFails($expected, $actual, $message)
    {
        $this->expectException(ComparisonFailure::class);
        $this->expectExceptionMessage($message);

        $this->comparator->assertEquals($expected, $actual);
    }
}
