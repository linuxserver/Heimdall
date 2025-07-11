<?php

namespace Spatie\LaravelIgnition\Solutions\SolutionProviders;

use Spatie\ErrorSolutions\SolutionProviders\Laravel\MissingAppKeySolutionProvider as BaseMissingAppKeySolutionProviderAlias;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;

class MissingAppKeySolutionProvider extends BaseMissingAppKeySolutionProviderAlias  implements HasSolutionsForThrowable
{

}
