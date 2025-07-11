<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Support\Str;
use OpenAI\Client;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Solutions\OpenAi\OpenAiSolutionProvider as BaseOpenAiSolutionProvider;
use Throwable;

class OpenAiSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! class_exists(Client::class)) {
            return false;
        }

        if (config('error-solutions.open_ai_key') === null) {
            return false;
        }

        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        $solutionProvider = new BaseOpenAiSolutionProvider(
            openAiKey: config('error-solutions.open_ai_key'),
            cache: cache()->store(config('cache.default')),
            cacheTtlInSeconds: 60,
            applicationType: 'Laravel ' . Str::before(app()->version(), '.'),
            applicationPath: base_path(),
            openAiModel: config('error-solutions.open_ai_model', 'gpt-3.5-turbo'),
        );

        return $solutionProvider->getSolutions($throwable);
    }
}
