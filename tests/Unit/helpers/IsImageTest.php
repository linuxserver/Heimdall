<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

class IsImageTest extends TestCase
{
    /**
     * @return void
     */
    public function test_isImage_returns_false_when_file_is_not_image()
    {
        $actual = isImage("<?php ?>");

        $this->assertFalse($actual);
    }

    /**
     * @return void
     */
    public function test_isImage_returns_true_when_file_is_image()
    {
        $file = file_get_contents(__DIR__ . '/fixtures/heimdall-icon-small.png');

        $actual = isImage($file);

        $this->assertTrue($actual);
    }

    /**
     * @return void
     */
    public function test_isImage_returns_false_when_file_is_php_but_png()
    {
        $file = file_get_contents(__DIR__ . '/fixtures/heimdall-icon-small-php.php');

        $actual = isImage($file);

        $this->assertTrue($actual);
    }
}
