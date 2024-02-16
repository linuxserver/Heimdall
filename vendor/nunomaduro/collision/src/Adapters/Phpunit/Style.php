<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Throwable;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
final class Style
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * Style constructor.
     */
    public function __construct(ConsoleOutputInterface $output)
    {
        if (! $output instanceof ConsoleOutput) {
            throw new ShouldNotHappen();
        }

        $this->output = $output;
    }

    /**
     * Prints the content.
     */
    public function write(string $content): void
    {
        $this->output->write($content);
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeCurrentTestCaseSummary(State $state): void
    {
        if ($state->testCaseTestsCount() === 0) {
            return;
        }

        if (! $state->headerPrinted) {
            $this->output->writeln($this->titleLineFrom(
                $state->getTestCaseTitle() === 'FAIL' ? 'white' : 'black',
                $state->getTestCaseTitleColor(),
                $state->getTestCaseTitle(),
                $state->testCaseName
            ));
            $state->headerPrinted = true;
        }

        $state->eachTestCaseTests(function (TestResult $testResult) {
            $this->output->writeln($this->testLineFrom(
                $testResult->color,
                $testResult->icon,
                $testResult->description,
                $testResult->warning
            ));
        });
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeErrorsSummary(State $state, bool $onFailure): void
    {
        $errors = array_filter($state->suiteTests, function (TestResult $testResult) {
            return $testResult->type === TestResult::FAIL;
        });

        if (! $onFailure) {
            $this->output->writeln(['', "  \e[2m---\e[22m", '']);
        }

        array_map(function (TestResult $testResult) use ($onFailure) {
            if (! $onFailure) {
                $this->output->write(sprintf(
                    '  <fg=red;options=bold>• %s </>> <fg=red;options=bold>%s</>',
                    $testResult->testCaseName,
                    $testResult->description
                ));
            }

            if (! $testResult->throwable instanceof Throwable) {
                throw new ShouldNotHappen();
            }

            $this->writeError($testResult->throwable);
        }, $errors);
    }

    /**
     * Writes the final recap.
     */
    public function writeRecap(State $state, Timer $timer = null): void
    {
        $types = [TestResult::FAIL, TestResult::WARN, TestResult::RISKY, TestResult::INCOMPLETE, TestResult::SKIPPED, TestResult::PASS];
        foreach ($types as $type) {
            if (($countTests = $state->countTestsInTestSuiteBy($type)) !== 0) {
                $color = TestResult::makeColor($type);
                $tests[] = "<fg=$color;options=bold>$countTests $type</>";
            }
        }

        $pending = $state->suiteTotalTests - $state->testSuiteTestsCount();
        if ($pending !== 0) {
            $tests[] = "\e[2m$pending pending\e[22m";
        }

        if (! empty($tests)) {
            $this->output->write([
                "\n",
                sprintf(
                    '  <fg=white;options=bold>Tests:  </><fg=default>%s</>',
                    implode(', ', $tests)
                ),
            ]);
        }

        if ($timer !== null) {
            $timeElapsed = number_format($timer->result(), 2, '.', '');
            $this->output->writeln([
                '',
                sprintf(
                    '  <fg=white;options=bold>Time:   </><fg=default>%ss</>',
                    $timeElapsed
                ),
            ]
            );
        }

        $this->output->writeln('');
    }

    /**
     * Displays a warning message.
     */
    public function writeWarning(string $message): void
    {
        $this->output->writeln($this->testLineFrom('yellow', $message, ''));
    }

    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     */
    public function writeError(Throwable $throwable): void
    {
        $writer = (new Writer())->setOutput($this->output);

        if ($throwable instanceof AssertionFailedError) {
            $writer->showTitle(false);
            $this->output->write('', true);
        }

        $writer->ignoreFilesIn([
            '/vendor\/bin\/pest/',
            '/bin\/pest/',
            '/vendor\/pestphp\/pest/',
            '/vendor\/phpspec\/prophecy-phpunit/',
            '/vendor\/phpspec\/prophecy/',
            '/vendor\/phpunit\/phpunit\/src/',
            '/vendor\/mockery\/mockery/',
            '/vendor\/laravel\/dusk/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Testing/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Testing/',
            '/vendor\/symfony\/framework-bundle\/Test/',
            '/vendor\/symfony\/phpunit-bridge/',
            '/vendor\/symfony\/dom-crawler/',
            '/vendor\/symfony\/browser-kit/',
            '/vendor\/symfony\/css-selector/',
            '/vendor\/bin\/.phpunit/',
            '/bin\/.phpunit/',
            '/vendor\/bin\/simple-phpunit/',
            '/bin\/phpunit/',
            '/vendor\/coduo\/php-matcher\/src\/PHPUnit/',
            '/vendor\/sulu\/sulu\/src\/Sulu\/Bundle\/TestBundle\/Testing/',
            '/vendor\/webmozart\/assert/',
        ]);

        if ($throwable instanceof ExceptionWrapper && $throwable->getOriginalException() !== null) {
            $throwable = $throwable->getOriginalException();
        }

        $inspector = new Inspector($throwable);

        $writer->write($inspector);

        if ($throwable instanceof ExpectationFailedException && $comparisionFailure = $throwable->getComparisonFailure()) {
            $diff = $comparisionFailure->getDiff();
            $lines = explode(PHP_EOL, $diff);
            $diff = '';
            foreach ($lines as $line) {
                if (0 === strpos($line, '-')) {
                    $line = '<fg=red>'.$line.'</>';
                } elseif (0 === strpos($line, '+')) {
                    $line = '<fg=green>'.$line.'</>';
                }

                $diff .= $line.PHP_EOL;
            }

            $diff = trim((string) preg_replace("/\r|\n/", "\n  ", $diff));

            $this->output->write("  $diff");
        }

        $this->output->writeln('');
    }

    /**
     * Returns the title contents.
     */
    private function titleLineFrom(string $fg, string $bg, string $title, string $testCaseName): string
    {
        return sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $bg,
            $title,
            $testCaseName
        );
    }

    /**
     * Returns the test contents.
     */
    private function testLineFrom(string $fg, string $icon, string $description, string $warning = null): string
    {
        if (! empty($warning)) {
            $warning = sprintf(
                ' → %s',
                $warning
            );
        }

        return sprintf(
            "  <fg=%s;options=bold>%s</><fg=default> \e[2m%s\e[22m</><fg=yellow>%s</>",
            $fg,
            $icon,
            $description,
            $warning
        );
    }
}
