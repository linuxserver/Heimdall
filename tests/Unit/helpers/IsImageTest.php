<?php

namespace Tests\Unit\helpers;

use Tests\TestCase;

class IsImageTest extends TestCase
{
    /**
     * @return void
     */
    public function test_returns_true_when_file_is_image()
    {
        $file = file_get_contents(__DIR__ . '/fixtures/heimdall-icon-small.png');

        $actual = isImage($file, 'png');

        $this->assertTrue($actual);
    }

    /**
     * @return void
     */
    public function test_returns_false_when_file_extension_is_image_but_content_is_not()
    {
        $actual = isImage("<?php ?>", "png");

        $this->assertFalse($actual);
    }

    /**
     * @return void
     */
    public function test_returns_false_when_file_extension_is_not_image_but_content_is()
    {
        $file = file_get_contents(__DIR__ . '/fixtures/heimdall-icon-small.png');

        $actual = isImage($file, 'php');

        $this->assertFalse($actual);
    }
}
