<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use const DIRECTORY_SEPARATOR;
use function file_get_contents;
use function file_put_contents;
use function hash;
use function implode;
use function is_file;
use function serialize;
use function unserialize;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Version;

/**
 * @internal This interface is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class CachingSourceAnalyser implements SourceAnalyser
{
    /**
     * @var non-empty-string
     */
    private readonly string $directory;
    private readonly SourceAnalyser $sourceAnalyser;

    /**
     * @var non-negative-int
     */
    private int $cacheHits = 0;

    /**
     * @var non-negative-int
     */
    private int $cacheMisses = 0;

    public function __construct(string $directory, SourceAnalyser $sourceAnalyser)
    {
        Filesystem::createDirectory($directory);

        $this->directory      = $directory;
        $this->sourceAnalyser = $sourceAnalyser;
    }

    /**
     * @param non-empty-string $sourceCodeFile
     */
    public function analyse(string $sourceCodeFile, string $sourceCode, bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode): AnalysisResult
    {
        $cacheFile = $this->cacheFile(
            $sourceCode,
            $useAnnotationsForIgnoringCode,
            $ignoreDeprecatedCode,
        );

        $cachedAnalysisResult = $this->read($cacheFile);

        if ($cachedAnalysisResult !== false) {
            $this->cacheHits++;

            return $cachedAnalysisResult;
        }

        $this->cacheMisses++;

        $analysisResult = $this->sourceAnalyser->analyse(
            $sourceCodeFile,
            $sourceCode,
            $useAnnotationsForIgnoringCode,
            $ignoreDeprecatedCode,
        );

        $this->write($cacheFile, $analysisResult);

        return $analysisResult;
    }

    /**
     * @return non-negative-int
     */
    public function cacheHits(): int
    {
        return $this->cacheHits;
    }

    /**
     * @return non-negative-int
     */
    public function cacheMisses(): int
    {
        return $this->cacheMisses;
    }

    /**
     * @param non-empty-string $cacheFile
     */
    private function read(string $cacheFile): AnalysisResult|false
    {
        if (!is_file($cacheFile)) {
            return false;
        }

        return unserialize(
            file_get_contents($cacheFile),
            [
                'allowed_classes' => [
                    AnalysisResult::class,
                    Class_::class,
                    Function_::class,
                    Interface_::class,
                    LinesOfCode::class,
                    Method::class,
                    Trait_::class,
                ],
            ],
        );
    }

    /**
     * @param non-empty-string $cacheFile
     */
    private function write(string $cacheFile, AnalysisResult $result): void
    {
        file_put_contents(
            $cacheFile,
            serialize($result),
        );
    }

    private function cacheFile(string $source, bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode): string
    {
        $cacheKey = hash(
            'sha256',
            implode(
                "\0",
                [
                    $source,
                    Version::id(),
                    $useAnnotationsForIgnoringCode,
                    $ignoreDeprecatedCode,
                ],
            ),
        );

        return $this->directory . DIRECTORY_SEPARATOR . $cacheKey;
    }
}
