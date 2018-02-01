<?php declare(strict_types = 1);
namespace PharIo\Manifest;

use PHPUnit\Framework\TestCase;

class ApplicationNameTest extends TestCase {

    public function testCanBeCreatedWithValidName() {
        $this->assertInstanceOf(
            ApplicationName::class,
            new ApplicationName('foo/bar')
        );
    }

    public function testUsingInvalidFormatForNameThrowsException() {
        $this->expectException(InvalidApplicationNameException::class);
        $this->expectExceptionCode(InvalidApplicationNameException::InvalidFormat);
        new ApplicationName('foo');
    }

    public function testUsingWrongTypeForNameThrowsException() {
        $this->expectException(InvalidApplicationNameException::class);
        $this->expectExceptionCode(InvalidApplicationNameException::NotAString);
        new ApplicationName(123);
    }

    public function testReturnsTrueForEqualNamesWhenCompared() {
        $app = new ApplicationName('foo/bar');
        $this->assertTrue(
            $app->isEqual($app)
        );
    }

    public function testReturnsFalseForNonEqualNamesWhenCompared() {
        $app1 = new ApplicationName('foo/bar');
        $app2 = new ApplicationName('foo/foo');
        $this->assertFalse(
            $app1->isEqual($app2)
        );
    }

    public function testCanBeConvertedToString() {
        $this->assertEquals(
            'foo/bar',
            new ApplicationName('foo/bar')
        );
    }
}
