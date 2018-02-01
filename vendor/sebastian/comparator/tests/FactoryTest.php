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
 * @coversDefaultClass SebastianBergmann\Comparator\Factory
 *
 * @uses SebastianBergmann\Comparator\Comparator
 * @uses SebastianBergmann\Comparator\Factory
 * @uses SebastianBergmann\Comparator\ComparisonFailure
 */
class FactoryTest extends TestCase
{
    public function instanceProvider()
    {
        $tmpfile = \tmpfile();

        return [
            [null, null, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [null, true, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [true, null, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [true, true, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [false, false, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [true, false, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [false, true, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            ['', '', 'SebastianBergmann\\Comparator\\ScalarComparator'],
            ['0', '0', 'SebastianBergmann\\Comparator\\ScalarComparator'],
            ['0', 0, 'SebastianBergmann\\Comparator\\NumericComparator'],
            [0, '0', 'SebastianBergmann\\Comparator\\NumericComparator'],
            [0, 0, 'SebastianBergmann\\Comparator\\NumericComparator'],
            [1.0, 0, 'SebastianBergmann\\Comparator\\DoubleComparator'],
            [0, 1.0, 'SebastianBergmann\\Comparator\\DoubleComparator'],
            [1.0, 1.0, 'SebastianBergmann\\Comparator\\DoubleComparator'],
            [[1], [1], 'SebastianBergmann\\Comparator\\ArrayComparator'],
            [$tmpfile, $tmpfile, 'SebastianBergmann\\Comparator\\ResourceComparator'],
            [new \stdClass, new \stdClass, 'SebastianBergmann\\Comparator\\ObjectComparator'],
            [new \DateTime, new \DateTime, 'SebastianBergmann\\Comparator\\DateTimeComparator'],
            [new \SplObjectStorage, new \SplObjectStorage, 'SebastianBergmann\\Comparator\\SplObjectStorageComparator'],
            [new \Exception, new \Exception, 'SebastianBergmann\\Comparator\\ExceptionComparator'],
            [new \DOMDocument, new \DOMDocument, 'SebastianBergmann\\Comparator\\DOMNodeComparator'],
            // mixed types
            [$tmpfile, [1], 'SebastianBergmann\\Comparator\\TypeComparator'],
            [[1], $tmpfile, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [$tmpfile, '1', 'SebastianBergmann\\Comparator\\TypeComparator'],
            ['1', $tmpfile, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [$tmpfile, new \stdClass, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [new \stdClass, $tmpfile, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [new \stdClass, [1], 'SebastianBergmann\\Comparator\\TypeComparator'],
            [[1], new \stdClass, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [new \stdClass, '1', 'SebastianBergmann\\Comparator\\TypeComparator'],
            ['1', new \stdClass, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [new ClassWithToString, '1', 'SebastianBergmann\\Comparator\\ScalarComparator'],
            ['1', new ClassWithToString, 'SebastianBergmann\\Comparator\\ScalarComparator'],
            [1.0, new \stdClass, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [new \stdClass, 1.0, 'SebastianBergmann\\Comparator\\TypeComparator'],
            [1.0, [1], 'SebastianBergmann\\Comparator\\TypeComparator'],
            [[1], 1.0, 'SebastianBergmann\\Comparator\\TypeComparator'],
        ];
    }

    /**
     * @dataProvider instanceProvider
     * @covers       ::getComparatorFor
     * @covers       ::__construct
     */
    public function testGetComparatorFor($a, $b, $expected)
    {
        $factory = new Factory;
        $actual  = $factory->getComparatorFor($a, $b);
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @covers ::register
     */
    public function testRegister()
    {
        $comparator = new TestClassComparator;

        $factory = new Factory;
        $factory->register($comparator);

        $a        = new TestClass;
        $b        = new TestClass;
        $expected = 'SebastianBergmann\\Comparator\\TestClassComparator';
        $actual   = $factory->getComparatorFor($a, $b);

        $factory->unregister($comparator);
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @covers ::unregister
     */
    public function testUnregister()
    {
        $comparator = new TestClassComparator;

        $factory = new Factory;
        $factory->register($comparator);
        $factory->unregister($comparator);

        $a        = new TestClass;
        $b        = new TestClass;
        $expected = 'SebastianBergmann\\Comparator\\ObjectComparator';
        $actual   = $factory->getComparatorFor($a, $b);

        $this->assertInstanceOf($expected, $actual);
    }

    public function testIsSingleton()
    {
        $f = Factory::getInstance();
        $this->assertSame($f, Factory::getInstance());
    }
}
