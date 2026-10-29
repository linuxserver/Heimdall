<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\NormalizeHeadings;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class NormalizeHeadingsExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('normalize_headings', Expect::structure([
            'min_level' => Expect::int()->min(1)->max(6)->default(1),
            'max_level' => Expect::int()->min(1)->max(6)->default(6),
            'rebase_to_min_level' => Expect::bool()->default(false),
        ])->assert(
            static function (\stdClass $config): bool {
                $headingLevels = (array) $config;

                return $headingLevels['min_level'] <= $headingLevels['max_level'];
            },
            '"min_level" must be less than or equal to "max_level"'
        ));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, new NormalizeHeadingsProcessor(), -99);
    }
}
