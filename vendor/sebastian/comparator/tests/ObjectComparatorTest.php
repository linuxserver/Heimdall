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
 * @coversDefaultClass SebastianBergmann\Comparator\ObjectComparator
 *
 * @uses SebastianBergmann\Comparator\Comparator
 * @uses SebastianBergmann\Comparator\Factory
 * @uses SebastianBergmann\Comparator\ComparisonFailure
 */
class ObjectComparatorTest extends TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new ObjectComparator;
        $this->comparator->setFactory(new Factory);
    }

    public function acceptsSucceedsProvider()
    {
        return [
          [new TestClass, new TestClass],
          [new stdClass, new stdClass],
          [new stdClass, new TestClass]
        ];
    }

    public function acceptsFailsProvider()
    {
        return [
          [new stdClass, null],
          [null, new stdClass],
          [null, null]
        ];
    }

    public function assertEqualsSucceedsProvider()
    {
        // cyclic dependencies
        $book1                  = new Book;
        $book1->author          = new Author('Terry Pratchett');
        $book1->author->books[] = $book1;
        $book2                  = new Book;
        $book2->author          = new Author('Terry Pratchett');
        $book2->author->books[] = $book2;

        $object1 = new SampleClass(4, 8, 15);
        $object2 = new SampleClass(4, 8, 15);

        return [
          [$object1, $object1],
          [$object1, $object2],
          [$book1, $book1],
          [$book1, $book2],
          [new Struct(2.3), new Struct(2.5), 0.5]
        ];
    }

    public function assertEqualsFailsProvider()
    {
        $typeMessage  = 'is not instance of expected class';
        $equalMessage = 'Failed asserting that two objects are equal.';

        // cyclic dependencies
        $book1                  = new Book;
        $book1->author          = new Author('Terry Pratchett');
        $book1->author->books[] = $book1;
        $book2                  = new Book;
        $book2->author          = new Author('Terry Pratch');
        $book2->author->books[] = $book2;

        $book3         = new Book;
        $book3->author = 'Terry Pratchett';
        $book4         = new stdClass;
        $book4->author = 'Terry Pratchett';

        $object1 = new SampleClass(4, 8, 15);
        $object2 = new SampleClass(16, 23, 42);

        return [
          [new SampleClass(4, 8, 15), new SampleClass(16, 23, 42), $equalMessage],
          [$object1, $object2, $equalMessage],
          [$book1, $book2, $equalMessage],
          [$book3, $book4, $typeMessage],
          [new Struct(2.3), new Struct(4.2), $equalMessage, 0.5]
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
    public function testAssertEqualsSucceeds($expected, $actual, $delta = 0.0)
    {
        $exception = null;

        try {
            $this->comparator->assertEquals($expected, $actual, $delta);
        } catch (ComparisonFailure $exception) {
        }

        $this->assertNull($exception, 'Unexpected ComparisonFailure');
    }

    /**
     * @covers       ::assertEquals
     * @dataProvider assertEqualsFailsProvider
     */
    public function testAssertEqualsFails($expected, $actual, $message, $delta = 0.0)
    {
        $this->expectException(ComparisonFailure::class);
        $this->expectExceptionMessage($message);

        $this->comparator->assertEquals($expected, $actual, $delta);
    }
}
