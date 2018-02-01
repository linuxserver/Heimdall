<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Diff;

use PHPUnit\Framework\TestCase;

/**
 * @requires OS Linux
 */
final class DifferTestTest extends TestCase
{
    private $fileFrom;
    private $filePatch;

    protected function setUp()
    {
        $dir             = \realpath(__DIR__ . '/../') . '/';
        $this->fileFrom  = $dir . 'from.txt';
        $this->filePatch = $dir . 'patch.txt';
    }

    /**
     * @dataProvider provideDiffWithLineNumbers
     */
    public function testTheTestProvideDiffWithLineNumbers($expected, $from, $to)
    {
        $this->runThisTest($expected, $from, $to);
    }

    public function provideDiffWithLineNumbers()
    {
        require_once __DIR__ . '/DifferTest.php';
        $test  = new DifferTest();
        $tests = $test->provideDiffWithLineNumbers();

        $tests = \array_filter(
            $tests,
            function ($key) {
                return !\is_string($key) || false === \strpos($key, 'non_patch_compat');
            },
            ARRAY_FILTER_USE_KEY
        );

        return $tests;
    }

    private function runThisTest(string $expected, string $from, string $to)
    {
        $expected = \str_replace('--- Original', '--- from.txt', $expected);
        $expected = \str_replace('+++ New', '+++ from.txt', $expected);

        @\unlink($this->fileFrom);
        @\unlink($this->filePatch);

        $this->assertNotFalse(\file_put_contents($this->fileFrom, $from));
        $this->assertNotFalse(\file_put_contents($this->filePatch, $expected));

        $command = \sprintf(
            'patch -u --verbose %s < %s', // --posix
            \escapeshellarg($this->fileFrom),
            \escapeshellarg($this->filePatch)
        );

        \exec($command, $output, $d);

        $this->assertSame(0, $d, \sprintf('%s | %s', $command, \implode("\n", $output)));

        $patched = \file_get_contents($this->fileFrom);
        $this->assertSame($patched, $to);

        @\unlink($this->fileFrom . '.orig');
        @\unlink($this->fileFrom);
        @\unlink($this->filePatch);
    }
}
