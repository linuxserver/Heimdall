<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket;

use InvalidArgumentException;
use Phrity\Http\HttpFactory;
use Phrity\Net\{
    Context,
    StreamCollection,
    StreamFactory,
    Uri
};
use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface,
    UriInterface,
};
use Psr\Log\{
    LoggerAwareInterface,
    LoggerInterface,
};
use Stringable;
use Throwable;
use WebSocket\Exception\{
    BadUriException,
    ClientException,
    CloseException,
    ConnectionLevelInterface,
    ExceptionInterface,
    HandshakeException,
    MessageLevelInterface,
    ReconnectException,
};
use WebSocket\Http\DefaultHttpFactory;
use WebSocket\Message\Message;
use WebSocket\Middleware\MiddlewareInterface;
use WebSocket\Runtime\{
    IdentityInterface,
    Runner,
};
use WebSocket\Trait\{
    ConfigurationTrait,
    ListenerTrait,
    SendMethodsTrait,
    StringableTrait
};

/**
 * WebSocket\Client class.
 * Entry class for WebSocket client.
 */
class Client implements IdentityInterface, LoggerAwareInterface, Stringable
{
    use ConfigurationTrait;
    /** @use ListenerTrait<Client> */
    use ListenerTrait;
    use SendMethodsTrait;
    use StringableTrait;

    private const SCOPE = 'client';

    // Settings
    /** @var array<string, string> $headers */
    private array $headers = [];

    // Internal resources
    private StreamFactory $streamFactory;
    private Uri $socketUri;
    private Connection|null $connection = null;
    /** @var array<MiddlewareInterface> $middlewares */
    private array $middlewares = [];
    private Runner $runner;
    private bool $running = false;
    private HttpFactory $httpFactory;
    /** @var non-empty-string $identity */
    private string $identity = 'client/<unconnected>';


    /* ---------- Magic methods ------------------------------------------------------------------------------------ */

    /**
     * @param UriInterface|string $uri A ws/wss-URI
     * @param Configuration|null $configuration
     * @param StreamFactory|null $streamFactory
     */
    public function __construct(
        UriInterface|string $uri,
        Configuration|null $configuration = null,
        StreamFactory|null $streamFactory = null,
    ) {
        $this->socketUri = $this->parseUri($uri);
        $this->streamFactory = $streamFactory ?? new StreamFactory();
        $this->httpFactory = new DefaultHttpFactory();
        $this->identity = "client/{$this->socketUri->getHost()}";
        $this->initConfiguration($configuration);
        $this->runner = new Runner($this->streamFactory);
    }

    /**
     * Get string representation of instance.
     * @return string String representation
     */
    public function __toString(): string
    {
        return $this->stringable('%s', $this->connection ? $this->socketUri->__toString() : 'closed');
    }


    /* ---------- Configuration ------------------------------------------------------------------------------------ */

    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * Set stream factory to use.
     * @param StreamFactory $streamFactory
     * @return self
     * @depracated Remove in v4
     */
    public function setStreamFactory(StreamFactory $streamFactory): self
    {
        trigger_error('Client.setStreamFactory is deprecated and will be removed in v4.', E_USER_DEPRECATED);
        $this->streamFactory = $streamFactory;
        return $this;
    }

    /**
     * Set HTTP factory to use.
     * @param HttpFactory $httpFactory
     * @return self
     */
    public function setHttpFactory(HttpFactory $httpFactory): self
    {
        $this->httpFactory = $httpFactory;
        return $this;
    }

    /**
     * Set logger.
     * @param LoggerInterface $logger Logger implementation
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->configuration->setLogger($logger);
        if ($this->connection) {
            $this->connection->setLogger($logger);
        }
    }

    /**
     * Set timeout.
     * @param int<0, max>|float $timeout Timeout in seconds
     * @return self
     * @throws InvalidArgumentException If invalid timeout provided
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setTimeout(int|float $timeout): self
    {
        $this->configuration->setTimeout($timeout);
        if ($this->connection) {
            $this->connection->setTimeout($timeout);
        }
        return $this;
    }

    /**
     * Get timeout.
     * @return int<0, max>|float Timeout in seconds
     * @deprecated Will be removed in future version, get from Configuration instead
     */
    public function getTimeout(): int|float
    {
        return $this->configuration->getTimeout();
    }

    /**
     * Set frame size.
     * @param int<1, max> $frameSize Max frame payload size in bytes
     * @return self
     * @throws InvalidArgumentException If invalid frameSize provided
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setFrameSize(int $frameSize): self
    {
        $this->configuration->setFrameSize($frameSize);
        if ($this->connection) {
            $this->connection->setFrameSize($frameSize);
        }
        return $this;
    }

    /**
     * Get frame size.
     * @return int Frame size in bytes
     * @deprecated Will be removed in future version, get from Configuration instead
     */
    public function getFrameSize(): int
    {
        return $this->configuration->getFrameSize();
    }

    /**
     * Set connection persistence.
     * @param bool $persistent True for persistent connection.
     * @deprecated Will be removed in future version, set on Configuration instead
     * @return self
     */
    public function setPersistent(bool $persistent): self
    {
        $this->configuration->setPersistent($persistent);
        return $this;
    }

    /**
     * Set stream context.
     * @param Context|array<string, mixed> $context Context or options as array
     * @see https://www.php.net/manual/en/context.php
     * @return self
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setContext(Context|array $context): self
    {
        if ($context instanceof Context) {
            $this->configuration->setContext($context);
        } else {
            $this->configuration->getContext()->setOptions($context);
            trigger_error('Calling Client.setContext with array is deprecated, use Context class.', E_USER_DEPRECATED);
        }
        return $this;
    }

    /**
     * Get current stream context.
     * @return Context
     * @deprecated Will be removed in future version, get from Configuration instead
     */
    public function getContext(): Context
    {
        return $this->configuration->getContext();
    }

    /**
     * Add header for handshake.
     * @param string $name Header name
     * @param string $content Header content
     * @return self
     */
    public function addHeader(string $name, string $content): self
    {
        $this->headers[$name] = $content;
        return $this;
    }

    /**
     * Add a middleware.
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        if ($this->connection) {
            $this->connection->addMiddleware($middleware);
        }
        return $this;
    }


    /* ---------- Messaging operations ----------------------------------------------------------------------------- */

    /**
     * Send message.
     * @template T of Message
     * @param T $message
     * @return T
     */
    public function send(Message $message): Message
    {
        return $this->connection()->pushMessage($message);
    }

    /**
     * Receive message.
     * Note that this operation will block reading.
     * @return Message
     */
    public function receive(): Message
    {
        return $this->connection()->pullMessage();
    }


    /* ---------- Listener operations ------------------------------------------------------------------------------ */

    /**
     * Start client listener.
     * @throws ExceptionInterface On high level error
     * @throws Throwable On low level error
     */
    public function start(int|float|null $timeout = null): void
    {
        // Check if running
        if ($this->running) {
            $this->configuration->getLogger()->warning("[{scope}] Client is already running", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
            ]);
            return;
        }
        $this->running = true;
        $reconnect = false;
        $this->configuration->getLogger()->info("[{scope}] Client is running", [
            'scope' => self::SCOPE,
            'client' => $this->identity,
        ]);

        $connection = $this->connection();

        // Run handler
        while ($this->running) {
            try {
                // Run attached handlers on selected streams
                $this->runner->handle($timeout ?? $this->configuration->getTimeout());

                if (!$connection->isConnected()) {
                    $this->running = false;
                }
                $connection->tick();
                $this->dispatch('tick', [$this]);
            } catch (CloseException $e) {
                // Close connection
                $connection->close($e->getCloseStatus(), $e->getMessage());
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, $connection, $e]);
            } catch (ReconnectException $e) {
                // Reconnect connection
                $reconnect = true;
                if ($uri = $e->getUri()) {
                    $this->socketUri = $uri;
                }
                $connection->close();
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, $connection, $e]);
            } catch (ExceptionInterface $e) {
                $this->disconnect();
                $this->running = false;

                // Low-level error
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, null, $e]);
            } catch (Throwable $e) {
                $this->disconnect();
                $this->running = false;

                // Crash it
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                throw $e;
            }
            gc_collect_cycles(); // Collect garbage

            if ($reconnect && !$connection->isConnected()) {
                $reconnect = false;
                $this->running = true;
            }
        }
    }

    /**
     * Stop client listener (resumable).
     */
    public function stop(): void
    {
        $this->running = false;
        $this->configuration->getLogger()->info("[{scope}] Client is stopped", [
            'scope' => self::SCOPE,
            'client' => $this->identity,
        ]);
    }

    /**
     * If client is running (accepting messages).
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }


    /* ---------- Connection management ---------------------------------------------------------------------------- */

    /**
     * If Client has active connection.
     * @return bool True if active connection.
     */
    public function isConnected(): bool
    {
        return $this->connection && $this->connection->isConnected();
    }

    /**
     * If Client is readable.
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->connection && $this->connection->isReadable();
    }

    /**
     * If Client is writable.
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->connection && $this->connection->isWritable();
    }


    /**
     * Connect to server and perform upgrade.
     * @throws ClientException On failed connection
     */
    public function connect(): void
    {
        $this->disconnect();

        $hostUri = (new Uri())
            ->withScheme(match ($this->socketUri->getScheme()) {
                'ws', 'http' => 'tcp',
                'wss', 'https' => 'ssl',
                default => throw new ClientException("Invalid socket scheme: {$this->socketUri->getScheme()}")
            })
            ->withHost($this->socketUri->getHost(Uri::IDN_ENCODE))
            ->withPort($this->socketUri->getPort(Uri::REQUIRE_PORT));

        $stream = null;

        try {
            $client = $this->streamFactory->createSocketClient($hostUri, $this->configuration->getContext());
            $client->setPersistent($this->configuration->isPersistent());
            $client->setTimeout($this->configuration->getTimeout());
            $stream = $client->connect();
        } catch (Throwable $e) {
            $error = "Could not open socket to \"{$hostUri}\": {$e->getMessage()}";
            $this->configuration->getLogger()->error("[{scope}] {message}", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            throw new ClientException($error);
        }
        $this->connection = $connection = new Connection(
            $stream,
            true,
            false,
            $hostUri->getScheme() === 'ssl',
            $this->httpFactory,
            clone $this->configuration
        );

        $this->runner->attach($this->connection, function (Runner $runner, Connection $connection) {
            try {
                // Read from connection
                $message = $connection->pullMessage();
                $this->dispatch($message->getOpcode(), [$this, $connection, $message]);
            } catch (MessageLevelInterface $e) {
                // Error, but keep connection open
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, $connection, $e]);
            } catch (ConnectionLevelInterface $e) {
                // Error, disconnect connection
                $this->disconnect();
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'client' => $this->identity,
                    'connection' => $connection->getIdentity(),
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, $connection, $e]);
            }
        }, $this->connection->getIdentity());

        foreach ($this->middlewares as $middleware) {
            $connection->addMiddleware($middleware);
        }

        if (!$this->isConnected()) {
            $this->configuration->getLogger()->error("[{scope}] Invalid stream on {uri}", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
                'connection' => $connection->getIdentity(),
                'uri' => $hostUri,
            ]);
            throw new ClientException("Invalid stream on \"{$hostUri}\".");
        }
        try {
            if (!$this->configuration->isPersistent() || $stream->tell() == 0) {
                /** @throws ReconnectException */
                $response = $this->performHandshake($this->socketUri, $connection);
            }
        } catch (ReconnectException $e) {
            $this->configuration->getLogger()->info("[{scope}] {message}", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
                'connection' => $connection->getIdentity(),
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            if ($uri = $e->getUri()) {
                $this->socketUri = $uri;
            }
            $this->connect();
            return;
        }
        $this->configuration->getLogger()->info("[{scope}] Client connected to {uri}", [
            'scope' => self::SCOPE,
            'client' => $this->identity,
            'connection' => $connection->getIdentity(),
            'uri' => $this->socketUri,
        ]);
        $this->dispatch('handshake', [
            $this,
            $connection,
            $connection->getHandshakeRequest(),
            $connection->getHandshakeResponse(),
        ]);
        $this->dispatch('connect', [$this, $connection, $connection->getHandshakeResponse()]);
    }

    /**
     * Disconnect from server.
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            $this->runner->detach($this->connection->getIdentity());
        }
        if ($this->connection && $this->isConnected()) {
            $this->connection->disconnect();
            $this->configuration->getLogger()->info("[{scope}] Client disconnected", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
                'connection' => $this->connection->getIdentity(),
            ]);
            $this->dispatch('disconnect', [$this, $this->connection]);
        }
    }


    /* ---------- Connection wrapper methods ----------------------------------------------------------------------- */

    /**
     * Get name of local socket, or null if not connected.
     * @return string|null
     */
    public function getName(): string|null
    {
        return $this->isConnected() ? $this->connection?->getName() : null;
    }

    /**
     * Get name of remote socket, or null if not connected.
     * @return string|null
     */
    public function getRemoteName(): string|null
    {
        return $this->isConnected() ? $this->connection?->getRemoteName() : null;
    }

    /**
     * Get meta value on connection.
     * @param string $key Meta key
     * @return mixed Meta value
     * @deprecated Will be removed in v4
     */
    public function getMeta(string $key): mixed
    {
        trigger_error('Client.getMeta is deprecated and will be removed in v4.', E_USER_DEPRECATED);
        return $this->isConnected() ? $this->connection?->getMeta($key) : null;
    }

    /**
     * Get Response for handshake procedure.
     * @return ResponseInterface|null Handshake.
     */
    public function getHandshakeResponse(): ResponseInterface|null
    {
        return $this->connection ? $this->connection->getHandshakeResponse() : null;
    }


    /* ---------- Internal helper methods -------------------------------------------------------------------------- */

    /**
     * Perform upgrade handshake on new connections.
     * @throws HandshakeException On failed handshake
     */
    protected function performHandshake(Uri $uri, Connection $connection): ResponseInterface
    {
        // Generate the WebSocket key.
        $key = $this->generateKey();

        $request = $this->httpFactory->createRequest('GET', $uri);

        $request = $request
            ->withHeader('User-Agent', 'websocket-client-php')
            ->withHeader('Connection', 'Upgrade')
            ->withHeader('Upgrade', 'websocket')
            ->withHeader('Sec-WebSocket-Key', $key)
            ->withHeader('Sec-WebSocket-Version', '13');

        // Handle basic authentication.
        if ($userinfo = $uri->getUserInfo(Uri::URI_DECODE)) {
            $request = $request->withHeader('Authorization', 'Basic ' . base64_encode($userinfo));
        }

        // Add and override with headers.
        foreach ($this->headers as $name => $content) {
            $request = $request->withHeader($name, $content);
        }

        try {
            /** @var RequestInterface */
            $request = $connection->pushHttp($request);
            /** @var ResponseInterface */
            $response = $connection->pullHttp();
            if ($response->getStatusCode() != 101) {
                throw new HandshakeException("Invalid status code {$response->getStatusCode()}.", $response);
            }

            if (empty($response->getHeaderLine('Sec-WebSocket-Accept'))) {
                throw new HandshakeException(
                    "Connection to '{$uri}' failed: Server sent invalid upgrade response.",
                    $response
                );
            }

            $responseKey = trim($response->getHeaderLine('Sec-WebSocket-Accept'));
            $expectedKey = base64_encode(
                pack('H*', sha1($key . Constant::GUID))
            );

            if ($responseKey !== $expectedKey) {
                throw new HandshakeException("Server sent bad upgrade response.", $response);
            }
        } catch (HandshakeException $e) {
            $this->configuration->getLogger()->error("[{scope}] {message}", [
                'scope' => self::SCOPE,
                'client' => $this->identity,
                'connection' => $connection->getIdentity(),
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }

        $this->configuration->getLogger()->debug("[{scope}] Handshake on {path}", [
            'scope' => self::SCOPE,
            'client' => $this->identity,
            'connection' => $connection->getIdentity(),
            'path' => $uri->getPath(),
        ]);

        $connection->setHandshakeRequest($request);
        $connection->setHandshakeResponse($response);

        return $response;
    }

    /**
     * Generate a random string for WebSocket key.
     * @return string Random string
     */
    protected function generateKey(): string
    {
        $key = '';
        for ($i = 0; $i < 16; $i++) {
            $key .= chr(rand(33, 126));
        }
        return base64_encode($key);
    }

    /**
     * Ensure URI instance to use in client.
     * @param UriInterface|string $uri A ws/wss-URI
     * @return Uri
     * @throws BadUriException On invalid URI
     */
    protected function parseUri(UriInterface|string $uri): Uri
    {
        try {
            if ($uri instanceof Uri) {
                $uriInstance = $uri;
            } elseif ($uri instanceof UriInterface) {
                $uriInstance = new Uri("{$uri}");
            } else {
                $uriInstance = new Uri($uri);
            }
        } catch (InvalidArgumentException $e) {
            throw new BadUriException("Invalid URI '{$uri}' provided.");
        }


        if (!in_array($uriInstance->getScheme(), ['ws', 'wss'])) {
            throw new BadUriException("Invalid URI scheme, must be 'ws' or 'wss'.");
        }
        if (!$uriInstance->getHost()) {
            throw new BadUriException("Invalid URI host.");
        }
        $uriInstance = $uriInstance->withPath($uriInstance->getPath(), Uri::ABSOLUTE_PATH | Uri::NORMALIZE_PATH);
        return $uriInstance;
    }

    protected function connection(): Connection
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        /** @var Connection */
        $connection = $this->connection;
        return $connection;
    }
}
