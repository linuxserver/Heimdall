<?php

namespace Spatie\LaravelIgnition\Solutions\SolutionProviders;

use Spatie\ErrorSolutions\SolutionProviders\Laravel\ViewNotFoundSolutionProvider as BaseViewNotFoundSolutionProviderAlias;
use Spatie\Ignition\Contracts\HasSolutionsForThrowable;

class ViewNotFoundSolutionProvider extends BaseViewNotFoundSolutionProviderAlias  implements HasSolutionsForThrowable
{

}
