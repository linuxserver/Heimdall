<?php

namespace Facade\FlareClient;

use Error;
use ErrorException;
use Exception;
use Facade\FlareClient\Concerns\HasContext;
use Facade\FlareClient\Context\ContextContextDetector;
use Facade\FlareClient\Context\ContextDetectorInterface;
use Facade\FlareClient\Enums\MessageLevels;
use Facade\FlareClient\Glows\Glow;
use Facade\FlareClient\Glows\Recorder;
use Facade\FlareClient\Http\Client;
use Facade\FlareClient\Middleware\AddGlows;
use Facade\FlareClient\Middleware\AnonymizeIp;
use Facade\FlareClient\Middleware\CensorRequestBodyFields;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class Flare
{
    use HasContext;

    /** @var \Facade\FlareClient\Http\Client */
    protected $client;

    /** @var \Facade\FlareClient\Api */
    protected $api;

    /** @var array */
    protected $middleware = [];

    /** @var \Facade\FlareClient\Glows\Recorder */
    protected $recorder;

    /** @var string */
    protected $applicationPath;

    /** @var \Illuminate\Contracts\Container\Container|null */
    protected $container;

    /** @var ContextDetectorInterface */
    protected $contextDetector;

    /** @var callable|null */
    protected $previousExceptionHandler;

    /** @var callable|null */
    protected $previousErrorHandler;

    /** @var callable|null */
    protected $determineVersionCallable;

    /** @var int|null */
    protected $reportErrorLevels;

    /** @var callable|null */
    protected $filterExceptionsCallable;

    public static function register(string $apiKey, string $apiSecret = null, ContextDetectorInterface $contextDetector = null, Container $container = null)
    {
        $client = new Client($apiKey, $apiSecret);

        return new static($client, $contextDetector, $container);
    }

    public function determineVersionUsing($determineVersionCallable)
    {
        $this->determineVersionCallable = $determineVersionCallable;
    }

    public function reportErrorLevels(int $reportErrorLevels)
    {
        $this->reportErrorLevels = $reportErrorLevels;
    }

    public function filterExceptionsUsing(callable $filterExceptionsCallable)
    {
        $this->filterExceptionsCallable = $filterExceptionsCallable;
    }

    /**
     * @return null|string
     */
    public function version()
    {
        if (! $this->determineVersionCallable) {
            return null;
        }

        return ($this->determineVersionCallable)();
    }

    public function __construct(Client $client, ContextDetectorInterface $contextDetector = null, Container $container = null, array $middleware = [])
    {
        $this->client = $client;
        $this->recorder = new Recorder();
        $this->contextDetector = $contextDetector ?? new ContextContextDetector();
        $this->container = $container;
        $this->middleware = $middleware;
        $this->api = new Api($this->client);

        $this->registerDefaultMiddleware();
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function registerFlareHandlers()
    {
        $this->registerExceptionHandler();
        $this->registerErrorHandler();

        return $this;
    }

    public function registerExceptionHandler()
    {
        $this->previousExceptionHandler = set_exception_handler([$this, 'handleException']);

        return $this;
    }

    public function registerErrorHandler()
    {
        $this->previousErrorHandler = set_error_handler([$this, 'handleError']);

        return $this;
    }

    private function registerDefaultMiddleware()
    {
        return $this->registerMiddleware(new AddGlows($this->recorder));
    }

    public function registerMiddleware($callable)
    {
        $this->middleware[] = $callable;

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middleware;
    }

    public function glow(
        string $name,
        string $messageLevel = MessageLevels::INFO,
        array $metaData = []
    ) {
        $this->recorder->record(new Glow($name, $messageLevel, $metaData));
    }

    public function handleException(Throwable $throwable)
    {
        $this->report($throwable);

        if ($this->previousExceptionHandler) {
            call_user_func($this->previousExceptionHandler, $throwable);
        }
    }

    public function handleError($code, $message, $file = '', $line = 0)
    {
        $exception = new ErrorException($message, 0, $code, $file, $line);

        $this->report($exception);

        if ($this->previousErrorHandler) {
            return call_user_func(
                $this->previousErrorHandler,
                $message,
                $code,
                $file,
                $line
            );
        }
    }

    public function applicationPath(string $applicationPath)
    {
        $this->applicationPath = $applicationPath;

        return $this;
    }

    public function report(Throwable $throwable, callable $callback = null): ?Report
    {
        if (! $this->shouldSendReport($throwable)) {
            return null;
        }

        $report = $this->createReport($throwable);

        if (! is_null($callback)) {
            call_user_func($callback, $report);
        }

        $this->sendReportToApi($report);

        return $report;
    }

    protected function shouldSendReport(Throwable $throwable): bool
    {
        if ($this->reportErrorLevels && $throwable instanceof Error) {
            return $this->reportErrorLevels & $throwable->getCode();
        }

        if ($this->reportErrorLevels && $throwable instanceof ErrorException) {
            return $this->reportErrorLevels & $throwable->getSeverity();
        }

        if ($this->filterExceptionsCallable && $throwable instanceof Exception) {
            return call_user_func($this->filterExceptionsCallable, $throwable);
        }

        return true;
    }

    public function reportMessage(string $message, string $logLevel, callable $callback = null)
    {
        $report = $this->createReportFromMessage($message, $logLevel);

        if (! is_null($callback)) {
            call_user_func($callback, $report);
        }

        $this->sendReportToApi($report);
    }

    public function sendTestReport(Throwable $throwable)
    {
        $this->api->sendTestReport($this->createReport($throwable));
    }

    private function sendReportToApi(Report $report)
    {
        try {
            $this->api->report($report);
        } catch (Exception $exception) {
        }
    }

    public function reset()
    {
        $this->api->sendQueuedReports();

        $this->userProvidedContext = [];
        $this->recorder->reset();
    }

    private function applyAdditionalParameters(Report $report)
    {
        $report
            ->stage($this->stage)
            ->messageLevel($this->messageLevel)
            ->setApplicationPath($this->applicationPath)
            ->userProvidedContext($this->userProvidedContext);
    }

    public function anonymizeIp()
    {
        $this->registerMiddleware(new AnonymizeIp());

        return $this;
    }

    public function censorRequestBodyFields(array $fieldNames)
    {
        $this->registerMiddleware(new CensorRequestBodyFields($fieldNames));

        return $this;
    }

    public function createReport(Throwable $throwable): Report
    {
        $report = Report::createForThrowable(
            $throwable,
            $this->contextDetector->detectCurrentContext(),
            $this->applicationPath,
            $this->version()
        );

        return $this->applyMiddlewareToReport($report);
    }

    public function createReportFromMessage(string $message, string $logLevel): Report
    {
        $report = Report::createForMessage(
            $message,
            $logLevel,
            $this->contextDetector->detectCurrentContext(),
            $this->applicationPath
        );

        return $this->applyMiddlewareToReport($report);
    }

    protected function applyMiddlewareToReport(Report $report): Report
    {
        $this->applyAdditionalParameters($report);

        $report = (new Pipeline($this->container))
            ->send($report)
            ->through($this->middleware)
            ->then(function ($report) {
                return $report;
            });

        return $report;
    }
}
