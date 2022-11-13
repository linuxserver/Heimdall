<?php

namespace Facade\FlareClient;

use Facade\FlareClient\Concerns\HasContext;
use Facade\FlareClient\Concerns\UsesTime;
use Facade\FlareClient\Context\ContextInterface;
use Facade\FlareClient\Contracts\ProvidesFlareContext;
use Facade\FlareClient\Enums\GroupingTypes;
use Facade\FlareClient\Glows\Glow;
use Facade\FlareClient\Solutions\ReportSolution;
use Facade\FlareClient\Stacktrace\Stacktrace;
use Facade\IgnitionContracts\Solution;
use Throwable;

class Report
{
    use UsesTime;
    use HasContext;

    /** @var \Facade\FlareClient\Stacktrace\Stacktrace */
    private $stacktrace;

    /** @var string */
    private $exceptionClass;

    /** @var string */
    private $message;

    /** @var array */
    private $glows = [];

    /** @var array */
    private $solutions = [];

    /** @var ContextInterface */
    private $context;

    /** @var string */
    private $applicationPath;

    /** @var ?string */
    private $applicationVersion;

    /** @var array */
    private $userProvidedContext = [];

    /** @var array */
    private $exceptionContext = [];

    /** @var Throwable */
    private $throwable;

    /** @var string */
    private $notifierName;

    /** @var string */
    private $languageVersion;

    /** @var string */
    private $frameworkVersion;

    /** @var int */
    private $openFrameIndex;

    /** @var string */
    private $groupBy ;

    /** @var string */
    private $trackingUuid;

    /** @var null string|null */
    public static $fakeTrackingUuid = null;

    public static function createForThrowable(
        Throwable $throwable,
        ContextInterface $context,
        ?string $applicationPath = null,
        ?string $version = null
    ): self {
        return (new static())
            ->setApplicationPath($applicationPath)
            ->throwable($throwable)
            ->useContext($context)
            ->exceptionClass(self::getClassForThrowable($throwable))
            ->message($throwable->getMessage())
            ->stackTrace(Stacktrace::createForThrowable($throwable, $applicationPath))
            ->exceptionContext($throwable)
            ->setApplicationVersion($version);
    }

    protected static function getClassForThrowable(Throwable $throwable): string
    {
        if ($throwable instanceof \Facade\Ignition\Exceptions\ViewException) {
            if ($previous = $throwable->getPrevious()) {
                return get_class($previous);
            }
        }

        return get_class($throwable);
    }

    public static function createForMessage(string $message, string $logLevel, ContextInterface $context, ?string $applicationPath = null): self
    {
        $stacktrace = Stacktrace::create($applicationPath);

        return (new static())
            ->setApplicationPath($applicationPath)
            ->message($message)
            ->useContext($context)
            ->exceptionClass($logLevel)
            ->stacktrace($stacktrace)
            ->openFrameIndex($stacktrace->firstApplicationFrameIndex());
    }

    public function __construct()
    {
        $this->trackingUuid = self::$fakeTrackingUuid ?? $this->generateUuid();
    }

    public function trackingUuid(): string
    {
        return $this->trackingUuid;
    }

    public function exceptionClass(string $exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;

        return $this;
    }

    public function getExceptionClass(): string
    {
        return $this->exceptionClass;
    }

    public function throwable(Throwable $throwable)
    {
        $this->throwable = $throwable;

        return $this;
    }

    public function getThrowable(): ?Throwable
    {
        return $this->throwable;
    }

    public function message(string $message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function stacktrace(Stacktrace $stacktrace)
    {
        $this->stacktrace = $stacktrace;

        return $this;
    }

    public function getStacktrace(): Stacktrace
    {
        return $this->stacktrace;
    }

    public function notifierName(string $notifierName)
    {
        $this->notifierName = $notifierName;

        return $this;
    }

    public function languageVersion(string $languageVersion)
    {
        $this->languageVersion = $languageVersion;

        return $this;
    }

    public function frameworkVersion(string $frameworkVersion)
    {
        $this->frameworkVersion = $frameworkVersion;

        return $this;
    }

    public function useContext(ContextInterface $request)
    {
        $this->context = $request;

        return $this;
    }

    public function openFrameIndex(?int $index)
    {
        $this->openFrameIndex = $index;

        return $this;
    }

    public function setApplicationPath(?string $applicationPath)
    {
        $this->applicationPath = $applicationPath;

        return $this;
    }

    public function getApplicationPath(): ?string
    {
        return $this->applicationPath;
    }

    public function setApplicationVersion(?string $applicationVersion)
    {
        $this->applicationVersion = $applicationVersion;

        return $this;
    }

    public function getApplicationVersion(): ?string
    {
        return $this->applicationVersion;
    }

    public function view(?View $view)
    {
        $this->view = $view;

        return $this;
    }

    public function addGlow(Glow $glow)
    {
        $this->glows[] = $glow->toArray();

        return $this;
    }

    public function addSolution(Solution $solution)
    {
        $this->solutions[] = ReportSolution::fromSolution($solution)->toArray();

        return $this;
    }

    public function userProvidedContext(array $userProvidedContext)
    {
        $this->userProvidedContext = $userProvidedContext;

        return $this;
    }

    /** @deprecated  */
    public function groupByTopFrame()
    {
        $this->groupBy = GroupingTypes::TOP_FRAME;

        return $this;
    }

    /** @deprecated  */
    public function groupByException()
    {
        $this->groupBy = GroupingTypes::EXCEPTION;

        return $this;
    }

    public function allContext(): array
    {
        $context = $this->context->toArray();

        $context = array_merge_recursive_distinct($context, $this->exceptionContext);

        return array_merge_recursive_distinct($context, $this->userProvidedContext);
    }

    private function exceptionContext(Throwable $throwable)
    {
        if ($throwable instanceof ProvidesFlareContext) {
            $this->exceptionContext = $throwable->context();
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'notifier' => $this->notifierName ?? 'Flare Client',
            'language' => 'PHP',
            'framework_version' => $this->frameworkVersion,
            'language_version' => $this->languageVersion ?? phpversion(),
            'exception_class' => $this->exceptionClass,
            'seen_at' => $this->getCurrentTime(),
            'message' => $this->message,
            'glows' => $this->glows,
            'solutions' => $this->solutions,
            'stacktrace' => $this->stacktrace->toArray(),
            'context' => $this->allContext(),
            'stage' => $this->stage,
            'message_level' => $this->messageLevel,
            'open_frame_index' => $this->openFrameIndex,
            'application_path' => $this->applicationPath,
            'application_version' => $this->applicationVersion,
            'tracking_uuid' => $this->trackingUuid,
        ];
    }

    /*
 * Found on https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
 */
    private function generateUuid(): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
