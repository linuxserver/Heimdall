<?php

namespace Phrity\Net;

use Closure;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Context class.
 */
class Context
{
    /** @var resource */
    private $stream;

    /** @var array<int<1, 10>, Closure> */
    protected array $notifiers = [];

    /**
     * Create exception.
     * @param open-resource|null $stream
     * @throws InvalidArgumentException if not a resource
     * @throws InvalidArgumentException if wrong resource type
     */
    public function __construct(mixed $stream = null)
    {
        if (is_null($stream)) {
            $stream = stream_context_create();
        }
        $type = gettype($stream);
        if ($type !== 'resource') {
             throw new InvalidArgumentException("Invalid stream provided; got type '{$type}'.");
        }
        $rtype = get_resource_type($stream);
        if (!in_array($rtype, ['stream', 'persistent stream', 'stream-context'])) {
             throw new InvalidArgumentException("Invalid stream provided; got resource type '{$rtype}'.");
        }
        $this->stream = $stream;
        stream_context_set_params($this->stream, ['notification' => function (...$input) {
            $this->notifyCallback(...$input);
        }]);
    }

    public function getOption(string $wrapper, string $option): mixed
    {
        return stream_context_get_options($this->stream)[$wrapper][$option] ?? null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getOptions(): array
    {
        return stream_context_get_options($this->stream);
    }

    /**
     * @throws StreamException on failure
     */
    public function setOption(string $wrapper, string $option, mixed $value): self
    {
        if (!is_resource($this->stream) || !stream_context_set_option($this->stream, $wrapper, $option, $value)) {
            throw new StreamException(StreamException::CONTEXT_SET_ERR);
        }
        return $this;
    }

    /**
     * @param array<string, array<string, mixed>> $options
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $wrapper => $wrapperOptions) {
            foreach ($wrapperOptions as $option => $value) {
                $this->setOption($wrapper, $option, $value);
            }
        }
        return $this;
    }

    /**
     * @deprecated Use getOption.
     */
    public function getParam(string $param): mixed
    {
        return stream_context_get_params($this->stream)[$param] ?? null;
    }

    /**
     * @return array<string, mixed>
     * @deprecated Use getOptions.
     */
    public function getParams(): array
    {
        return stream_context_get_params($this->stream);
    }

    /**
     * @deprecated Use setOption and on- callbacks instead.
     */
    public function setParam(string $param, mixed $value): self
    {
        $this->setParams([$param => $value]);
        return $this;
    }

    /**
     * @param array<string, mixed> $params
     * @deprecated Use setOptions and on- callbacks instead.
     * @throws StreamException on failure
     */
    public function setParams(array $params): self
    {
        /** @phpstan-ignore booleanNot.alwaysFalse */
        if (!is_resource($this->stream) || !stream_context_set_params($this->stream, $params)) {
            throw new StreamException(StreamException::CONTEXT_SET_ERR);
        }
        return $this;
    }

    public function getResource(): mixed
    {
        return $this->stream;
    }

    /** @param Closure(): void $closure */
    public function onResolve(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_RESOLVE] = $closure;
    }

    /** @param Closure(): void $closure */
    public function onConnect(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_CONNECT] = $closure;
    }

    /** @param Closure(): void $closure */
    public function onAuthRequired(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_AUTH_REQUIRED] = $closure;
    }

    /** @param Closure(string $mimeType): void $closure */
    public function onMimeType(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_MIME_TYPE_IS] = $closure;
    }

    /** @param Closure(int $fileSize): void $closure */
    public function onFileSize(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_FILE_SIZE_IS] = $closure;
    }

    /** @param Closure(string $uri): void $closure */
    public function onRedirected(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_REDIRECTED] = $closure;
    }

    /** @param Closure(int $transferred, int $max): void $closure */
    public function onProgress(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_PROGRESS] = $closure;
    }

    /** @param Closure(): void $closure */
    public function onCompleted(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_COMPLETED] = $closure;
    }

    /** @param Closure(string $message, int $code): void $closure */
    public function onFailure(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_FAILURE] = $closure;
    }

    /** @param Closure(): void $closure */
    public function onAuthResult(Closure $closure): void
    {
        $this->notifiers[STREAM_NOTIFY_AUTH_RESULT] = $closure;
    }

    protected function notifyCallback(
        int $code,
        int $severity,
        string|null $message,
        int $errorCode,
        int $transferred,
        int $max,
    ): void {
        if (!array_key_exists($code, $this->notifiers)) {
            return;
        }
        $callback = $this->notifiers[$code];
        $params = match ($code) {
            STREAM_NOTIFY_RESOLVE,
            STREAM_NOTIFY_CONNECT,
            STREAM_NOTIFY_AUTH_REQUIRED,
            STREAM_NOTIFY_COMPLETED,
            STREAM_NOTIFY_AUTH_RESULT => [],
            STREAM_NOTIFY_MIME_TYPE_IS => ['mimeType' => $message],
            STREAM_NOTIFY_FILE_SIZE_IS => ['fileSize' => $message],
            STREAM_NOTIFY_REDIRECTED => ['uri' => $message],
            STREAM_NOTIFY_PROGRESS => ['transferred' => $transferred, 'max' => $max],
            STREAM_NOTIFY_FAILURE => ['message' => $message, 'code' => $errorCode],
        };
        $callback(...$params);
    }
}
