<?php

namespace Spatie\LaravelIgnition\Solutions\SolutionProviders;


use Spatie\ErrorSolutions\SolutionProviders\Laravel\MissingColumnSolutionProvider as BaseMissingColumnSolutionProviderAlias;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;

class MissingColumnSolutionProvider extends BaseMissingColumnSolutionProviderAlias implements HasSolutionsForThrowable
{

}
