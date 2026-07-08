<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Html;

use function array_values;
use function asort;
use function assert;
use function count;
use function explode;
use function floor;
use function json_encode;
use function sprintf;
use function str_replace;
use function uasort;
use function usort;
use SebastianBergmann\CodeCoverage\Data\ProcessedClassType;
use SebastianBergmann\CodeCoverage\Data\ProcessedMethodType;
use SebastianBergmann\CodeCoverage\Data\ProcessedTraitType;
use SebastianBergmann\CodeCoverage\FileCouldNotBeWrittenException;
use SebastianBergmann\CodeCoverage\Node\AbstractNode;
use SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use SebastianBergmann\Template\Exception;
use SebastianBergmann\Template\Template;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Dashboard extends Renderer
{
    public function render(DirectoryNode $node, string $file): void
    {
        $classes      = $node->classesAndTraits();
        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'dashboard_branch.html' : 'dashboard.html');
        $template     = new Template(
            $templateName,
            '{{',
            '}}',
        );

        $this->setCommonTemplateVariables($template, $node);

        $baseLink             = $node->id() . '/';
        $complexity           = $this->complexity($classes, $baseLink);
        $coverageDistribution = $this->coverageDistribution($classes);
        $insufficientCoverage = $this->insufficientCoverage($classes, $baseLink);
        $projectRisks         = $this->projectRisks($classes, $baseLink);

        $template->setVar(
            [
                'insufficient_coverage_classes' => $insufficientCoverage['class'],
                'insufficient_coverage_methods' => $insufficientCoverage['method'],
                'project_risks_classes'         => $projectRisks['class'],
                'project_risks_methods'         => $projectRisks['method'],
                'complexity_class'              => $complexity['class'],
                'complexity_method'             => $complexity['method'],
                'class_coverage_distribution'   => $coverageDistribution['class'],
                'method_coverage_distribution'  => $coverageDistribution['method'],
            ],
        );

        try {
            $template->renderTo($file);
        } catch (Exception $e) {
            throw new FileCouldNotBeWrittenException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    protected function activeBreadcrumb(AbstractNode $node): string
    {
        return sprintf(
            '         <li class="breadcrumb-item"><a href="index.html">%s</a></li>' . "\n" .
            '         <li class="breadcrumb-item active">(Dashboard)</li>' . "\n",
            $this->escapeHtml($node->name()),
        );
    }

    /**
     * @param array<string, ProcessedClassType|ProcessedTraitType> $classes
     *
     * @return array{class: non-empty-string, method: non-empty-string}
     */
    private function complexity(array $classes, string $baseLink): array
    {
        $result = ['class' => [], 'method' => []];

        foreach ($classes as $className => $class) {
            foreach ($class->methods as $methodName => $method) {
                if ($className !== '*') {
                    $methodName = $className . '::' . $methodName;
                }

                $result['method'][] = [
                    $method->coverage,
                    $method->ccn,
                    str_replace($baseLink, '', $method->link),
                    $methodName,
                    $method->crap,
                ];
            }

            $result['class'][] = [
                $class->coverage,
                $class->ccn,
                str_replace($baseLink, '', $class->link),
                $className,
                $class->crap,
            ];
        }

        usort($result['class'], static fn (mixed $a, mixed $b) => ($a[0] <=> $b[0]));
        usort($result['method'], static fn (mixed $a, mixed $b) => ($a[0] <=> $b[0]));

        $class = json_encode($result['class']);

        assert($class !== false);

        $method = json_encode($result['method']);

        assert($method !== false);

        return ['class' => $class, 'method' => $method];
    }

    /**
     * @param array<string, ProcessedClassType|ProcessedTraitType> $classes
     *
     * @return array{class: non-empty-string, method: non-empty-string}
     */
    private function coverageDistribution(array $classes): array
    {
        $result = [
            'class' => [
                '0%'      => 0,
                '0-10%'   => 0,
                '10-20%'  => 0,
                '20-30%'  => 0,
                '30-40%'  => 0,
                '40-50%'  => 0,
                '50-60%'  => 0,
                '60-70%'  => 0,
                '70-80%'  => 0,
                '80-90%'  => 0,
                '90-100%' => 0,
                '100%'    => 0,
            ],
            'method' => [
                '0%'      => 0,
                '0-10%'   => 0,
                '10-20%'  => 0,
                '20-30%'  => 0,
                '30-40%'  => 0,
                '40-50%'  => 0,
                '50-60%'  => 0,
                '60-70%'  => 0,
                '70-80%'  => 0,
                '80-90%'  => 0,
                '90-100%' => 0,
                '100%'    => 0,
            ],
        ];

        foreach ($classes as $class) {
            foreach ($class->methods as $method) {
                if ($method->coverage === 0) {
                    $result['method']['0%']++;
                } elseif ($method->coverage === 100) {
                    $result['method']['100%']++;
                } else {
                    $key = floor($method->coverage / 10) * 10;
                    $key = $key . '-' . ($key + 10) . '%';
                    $result['method'][$key]++;
                }
            }

            if ($class->coverage === 0) {
                $result['class']['0%']++;
            } elseif ($class->coverage === 100) {
                $result['class']['100%']++;
            } else {
                $key = floor($class->coverage / 10) * 10;
                $key = $key . '-' . ($key + 10) . '%';
                $result['class'][$key]++;
            }
        }

        $class = json_encode(array_values($result['class']));

        assert($class !== false);

        $method = json_encode(array_values($result['method']));

        assert($method !== false);

        return ['class' => $class, 'method' => $method];
    }

    /**
     * @param array<string, ProcessedClassType|ProcessedTraitType> $classes
     *
     * @return array{class: string, method: string}
     */
    private function insufficientCoverage(array $classes, string $baseLink): array
    {
        $leastTestedClasses = [];
        $leastTestedMethods = [];
        $result             = ['class' => '', 'method' => ''];

        foreach ($classes as $className => $class) {
            foreach ($class->methods as $methodName => $method) {
                if ($method->coverage < $this->thresholds->highLowerBound()) {
                    $key = $methodName;

                    if ($className !== '*') {
                        $key = $className . '::' . $methodName;
                    }

                    $leastTestedMethods[$key] = $method->coverage;
                }
            }

            if ($class->coverage < $this->thresholds->highLowerBound()) {
                $leastTestedClasses[$className] = $class->coverage;
            }
        }

        asort($leastTestedClasses);
        asort($leastTestedMethods);

        foreach ($leastTestedClasses as $className => $coverage) {
            $result['class'] .= sprintf(
                '       <tr><td><a href="%s">%s</a></td><td class="text-right">%d%%</td></tr>' . "\n",
                str_replace($baseLink, '', $classes[$className]->link),
                $className,
                $coverage,
            );
        }

        foreach ($leastTestedMethods as $methodName => $coverage) {
            [$class, $method] = explode('::', $methodName);

            $result['method'] .= sprintf(
                '       <tr><td><a href="%s"><abbr title="%s">%s</abbr></a></td><td class="text-right">%d%%</td></tr>' . "\n",
                str_replace($baseLink, '', $classes[$class]->methods[$method]->link),
                $methodName,
                $method,
                $coverage,
            );
        }

        return $result;
    }

    /**
     * @param array<string, ProcessedClassType|ProcessedTraitType> $classes
     *
     * @return array{class: string, method: string}
     */
    private function projectRisks(array $classes, string $baseLink): array
    {
        $classRisks  = [];
        $methodRisks = [];
        $result      = ['class' => '', 'method' => ''];

        foreach ($classes as $className => $class) {
            foreach ($class->methods as $methodName => $method) {
                if ($method->coverage < $this->thresholds->highLowerBound() && $method->ccn > 1) {
                    $key = $methodName;

                    if ($className !== '*') {
                        $key = $className . '::' . $methodName;
                    }

                    $methodRisks[$key] = $method;
                }
            }

            if ($class->coverage < $this->thresholds->highLowerBound() &&
                $class->ccn > count($class->methods)) {
                $classRisks[$className] = $class;
            }
        }

        uasort($classRisks, static function (ProcessedClassType|ProcessedTraitType $a, ProcessedClassType|ProcessedTraitType $b)
        {
            return ((int) ($a->crap) <=> (int) ($b->crap)) * -1;
        });
        uasort($methodRisks, static function (ProcessedMethodType $a, ProcessedMethodType $b)
        {
            return ((int) ($a->crap) <=> (int) ($b->crap)) * -1;
        });

        foreach ($classRisks as $className => $class) {
            $result['class'] .= sprintf(
                '       <tr><td><a href="%s">%s</a></td><td class="text-right">%.1f%%</td><td class="text-right">%d</td><td class="text-right">%d</td></tr>' . "\n",
                str_replace($baseLink, '', $classes[$className]->link),
                $className,
                $class->coverage,
                $class->ccn,
                $class->crap,
            );
        }

        foreach ($methodRisks as $methodName => $methodVals) {
            [$class, $method] = explode('::', $methodName);

            $result['method'] .= sprintf(
                '       <tr><td><a href="%s"><abbr title="%s">%s</abbr></a></td><td class="text-right">%.1f%%</td><td class="text-right">%d</td><td class="text-right">%d</td></tr>' . "\n",
                str_replace($baseLink, '', $classes[$class]->methods[$method]->link),
                $methodName,
                $method,
                $methodVals->coverage,
                $methodVals->ccn,
                $methodVals->crap,
            );
        }

        return $result;
    }
}
