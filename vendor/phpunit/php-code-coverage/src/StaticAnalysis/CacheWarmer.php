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

use function file_get_contents;
use SebastianBergmann\CodeCoverage\Filter;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class CacheWarmer
{
    /**
     * @return array{cacheHits: non-negative-int, cacheMisses: non-negative-int}
     */
    public function warmCache(string $cacheDirectory, bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode, Filter $filter): array
    {
        $analyser = new CachingSourceAnalyser(
            $cacheDirectory,
            new ParsingSourceAnalyser,
        );

        foreach ($filter->files() as $file) {
            $analyser->analyse(
                $file,
                file_get_contents($file),
                $useAnnotationsForIgnoringCode,
                $ignoreDeprecatedCode,
            );
        }

        return [
            'cacheHits'   => $analyser->cacheHits(),
            'cacheMisses' => $analyser->cacheMisses(),
        ];
    }
}
