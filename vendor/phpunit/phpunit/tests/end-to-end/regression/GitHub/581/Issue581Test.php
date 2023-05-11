<?php
use PHPUnit\Framework\TestCase;

class Issue581Test extends TestCase
{
    public function testExportingObjectsDoesNotBreakWindowsLineFeeds()
    {
        $this->assertEquals(
            (object) [1, 2, "Test\r\n", 4, 5, 6, 7, 8],
            (object) [1, 2, "Test\r\n", 4, 1, 6, 7, 8]
        );
    }
}
