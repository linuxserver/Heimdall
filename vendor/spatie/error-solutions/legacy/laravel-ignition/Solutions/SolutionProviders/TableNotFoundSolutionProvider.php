<?php

namespace Spatie\LaravelIgnition\Solutions\SolutionProviders;

use Spatie\ErrorSolutions\SolutionProviders\Laravel\TableNotFoundSolutionProvider as BaseTableNotFoundSolutionProviderAlias;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;

class TableNotFoundSolutionProvider extends BaseTableNotFoundSolutionProviderAlias implements HasSolutionsForThrowable
{

}
