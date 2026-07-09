<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use RuntimeException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for sebastian/comparator
 */
final class ComparisonFailure extends RuntimeException
{
    private mixed $expected;
    private mixed $actual;
    private string $expectedAsString;
    private string $actualAsString;

    public function __construct(mixed $expected, mixed $actual, string $expectedAsString, string $actualAsString, string $message = '')
    {
        parent::__construct($message);

        $this->expected         = $expected;
        $this->actual           = $actual;
        $this->expectedAsString = $expectedAsString;
        $this->actualAsString   = $actualAsString;
    }

    /**
     * @return array{expected: mixed, actual: mixed, expectedAsString: string, actualAsString: string, message: string}
     */
    public function __serialize(): array
    {
        return [
            'expected'         => $this->expected,
            'actual'           => $this->actual,
            'expectedAsString' => $this->expectedAsString,
            'actualAsString'   => $this->actualAsString,
            'message'          => $this->message,
        ];
    }

    /**
     * @param array{expected: mixed, actual: mixed, expectedAsString: string, actualAsString: string, message: string} $data
     */
    public function __unserialize(array $data): void
    {
        $this->expected         = $data['expected'];
        $this->actual           = $data['actual'];
        $this->expectedAsString = $data['expectedAsString'];
        $this->actualAsString   = $data['actualAsString'];
        $this->message          = $data['message'];
    }

    public function getActual(): mixed
    {
        return $this->actual;
    }

    public function getExpected(): mixed
    {
        return $this->expected;
    }

    public function getActualAsString(): string
    {
        return $this->actualAsString;
    }

    public function getExpectedAsString(): string
    {
        return $this->expectedAsString;
    }

    public function getDiff(): string
    {
        if ($this->actualAsString === '' && $this->expectedAsString === '') {
            return '';
        }

        $differ = new Differ(new UnifiedDiffOutputBuilder("\n--- Expected\n+++ Actual\n"));

        return $differ->diff($this->expectedAsString, $this->actualAsString);
    }

    public function toString(): string
    {
        return $this->getMessage() . $this->getDiff();
    }
}
