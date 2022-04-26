<?php

namespace Facade\Ignition\ErrorPage;

use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Facade\Ignition\IgnitionConfig;
use Facade\IgnitionContracts\SolutionProviderRepository;
use Illuminate\Foundation\Application;
use Throwable;

class ErrorPageHandler
{
    /** @var \Facade\Ignition\IgnitionConfig */
    protected $ignitionConfig;

    /** @var \Facade\FlareClient\Flare */
    protected $flareClient;

    /** @var \Facade\Ignition\ErrorPage\Renderer */
    protected $renderer;

    /** @var \Facade\IgnitionContracts\SolutionProviderRepository */
    protected $solutionProviderRepository;

    public function __construct(
        Application $app,
        IgnitionConfig $ignitionConfig,
        Renderer $renderer,
        SolutionProviderRepository $solutionProviderRepository
    ) {
        $this->flareClient = $app->make(Flare::class);
        $this->ignitionConfig = $ignitionConfig;
        $this->renderer = $renderer;
        $this->solutionProviderRepository = $solutionProviderRepository;
    }

    public function handle(Throwable $throwable, $defaultTab = null, $defaultTabProps = [])
    {
        $report = $this->flareClient->createReport($throwable);

        $solutions = $this->solutionProviderRepository->getSolutionsForThrowable($throwable);

        $viewModel = new ErrorPageViewModel(
            $throwable,
            $this->ignitionConfig,
            $report,
            $solutions
        );

        $viewModel->defaultTab($defaultTab, $defaultTabProps);

        $this->renderException($viewModel);
    }

    public function handleReport(Report $report, $defaultTab = null, $defaultTabProps = [])
    {
        $viewModel = new ErrorPageViewModel(
            $report->getThrowable(),
            $this->ignitionConfig,
            $report,
            []
        );

        $viewModel->defaultTab($defaultTab, $defaultTabProps);

        $this->renderException($viewModel);
    }

    protected function renderException(ErrorPageViewModel $exceptionViewModel)
    {
        echo $this->renderer->render(
            'errorPage',
            $exceptionViewModel->toArray()
        );
    }
}
