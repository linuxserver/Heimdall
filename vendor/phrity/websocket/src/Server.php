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
    SocketServer,
    SocketStream,
    StreamCollection,
    StreamContainerInterface,
    StreamException,
    StreamFactory,
    StreamInterface,
    Uri
};
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Log\{
    LoggerAwareInterface,
    LoggerInterface,
};
use Stringable;
use Throwable;
use WebSocket\Exception\{
    CloseException,
    ConnectionFailureException,
    ConnectionLevelInterface,
    ExceptionInterface,
    HandshakeException,
    MessageLevelInterface,
    ServerException
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
 * WebSocket\Server class.
 * Entry class for WebSocket server.
 */
class Server implements IdentityInterface, LoggerAwareInterface, StreamContainerInterface, Stringable
{
    use ConfigurationTrait;
    /** @use ListenerTrait<Server> */
    use ListenerTrait;
    use SendMethodsTrait;
    use StringableTrait;

    private const SCOPE = 'server';

    // Settings
    private int $port;
    private string $scheme;

    // Internal resources
    private StreamFactory $streamFactory;
    private SocketServer|null $server = null;
    private Runner $runner;

    private bool $running = false;
    /** @var array<Connection> $connections */
    private array $connections = [];
    /** @var array<MiddlewareInterface> $middlewares */
    private array $middlewares = [];
    private bool $allowConnections = false;
    private HttpFactory $httpFactory;
    /** @var non-empty-string $identity */
    private string $identity;


    /* ---------- Magic methods ------------------------------------------------------------------------------------ */

    /**
     * @param int $port Socket port to listen to
     * @param bool $ssl If SSL should be used
     * @param Configuration|null $configuration
     * @param StreamFactory|null $streamFactory
     * @throws InvalidArgumentException If invalid port provided
     */
    public function __construct(
        int $port = 80,
        bool $ssl = false,
        Configuration|null $configuration = null,
        StreamFactory|null $streamFactory = null,
    ) {
        if ($port < 0 || $port > 65535) {
            throw new InvalidArgumentException("Invalid port '{$port}' provided");
        }
        $this->port = $port;
        $this->scheme = $ssl ? 'ssl' : 'tcp';
        $this->httpFactory = new DefaultHttpFactory();
        $this->streamFactory = $streamFactory ?? new StreamFactory();
        $this->identity = "server/{$port}";
        $this->initConfiguration($configuration);
        $this->runner = new Runner($this->streamFactory);
    }

    /**
     * Get string representation of instance.
     * @return string String representation
     */
    public function __toString(): string
    {
        return $this->stringable('%s', $this->server ? "{$this->scheme}://0.0.0.0:{$this->port}" : 'closed');
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
        trigger_error('Server.setStreamFactory is deprecated and will be removed in v4.', E_USER_DEPRECATED);
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
        foreach ($this->connections as $connection) {
            $connection->setLogger($logger);
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
        foreach ($this->connections as $connection) {
            $connection->setTimeout($timeout);
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
     * @param int<1, max> $frameSize Frame size in bytes
     * @return self
     * @throws InvalidArgumentException If invalid frameSize provided
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setFrameSize(int $frameSize): self
    {
        $this->configuration->setFrameSize($frameSize);
        foreach ($this->connections as $connection) {
            $connection->setFrameSize($frameSize);
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
     * Get socket port number.
     * @return int port
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Get connection scheme.
     * @return string scheme
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Get connection scheme.
     * @return bool SSL mode
     */
    public function isSsl(): bool
    {
        return $this->scheme === 'ssl';
    }

    /**
     * Number of currently connected clients.
     * @return int Connection count
     */
    public function getConnectionCount(): int
    {
        return count($this->connections);
    }

    /**
     * Get currently connected clients.
     * @return array<Connection> Connections
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Get currently readable clients.
     * @return array<Connection> Connections
     */
    public function getReadableConnections(): array
    {
        return array_filter($this->connections, function (Connection $connection) {
            return $connection->isReadable();
        });
    }

    /**
     * Get currently writable clients.
     * @return array<Connection> Connections
     */
    public function getWritableConnections(): array
    {
        return array_filter($this->connections, function (Connection $connection) {
            return $connection->isWritable();
        });
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
            trigger_error('Calling Server.setContext with array is deprecated, use Context class.', E_USER_DEPRECATED);
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
     * Add a middleware.
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        foreach ($this->connections as $connection) {
            $connection->addMiddleware($middleware);
        }
        return $this;
    }

    /**
     * Set maximum number of connections allowed, null means unlimited.
     * @param int<1, max>|null $maxConnections
     * @return self
     * @throws InvalidArgumentException If number provided
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setMaxConnections(int|null $maxConnections): self
    {
        $this->configuration->setMaxConnections($maxConnections);
        return $this;
    }


    /* ---------- Messaging operations ----------------------------------------------------------------------------- */

    /**
     * Send message (broadcast to all connected clients).
     * @template T of Message
     * @param T $message
     * @return T
     */
    public function send(Message $message): Message
    {
        foreach ($this->connections as $connection) {
            if ($connection->isWritable()) {
                $connection->send($message);
            }
        }
        return $message;
    }


    /* ---------- Listener operations ------------------------------------------------------------------------------ */

    /**
     * Start server listener.
     * @throws Throwable On low level error
     */
    public function start(int|float|null $timeout = null): void
    {
        // Create socket server
        if (empty($this->server)) {
            $this->createSocketServer();
        }

        // Check if running
        if ($this->running) {
            $this->configuration->getLogger()->warning("[{scope}] Server is already running", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
            ]);
            return;
        }
        $this->running = true;
        $this->configuration->getLogger()->info("[{scope}] Server is running", [
            'scope' => self::SCOPE,
            'server' => $this->identity,
        ]);

        // Run handler
        while ($this->running) {
            try {
                // Clear closed connections
                $this->detachUnconnected();

                if (!$this->server) {
                    $this->stop();
                    return;
                }

                // Run attached handlers on selected streams
                $this->runner->handle($timeout ?? $this->configuration->getTimeout());

                foreach ($this->connections as $connection) {
                    $connection->tick();
                }
                $this->dispatch('tick', [$this]);
            } catch (ExceptionInterface $e) {
                // Low-level error
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'server' => $this->identity,
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->dispatch('error', [$this, null, $e]);
            } catch (Throwable $e) {
                // Crash it
                $this->configuration->getLogger()->error("[{scope}] {message}", [
                    'scope' => self::SCOPE,
                    'server' => $this->identity,
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]);
                $this->disconnect();
                throw $e;
            }
            gc_collect_cycles(); // Collect garbage
        }
    }

    /**
     * Stop server listener (resumable).
     */
    public function stop(): void
    {
        $this->running = false;
        $this->configuration->getLogger()->info("[{scope}] Server is stopped", [
            'scope' => self::SCOPE,
            'server' => $this->identity,
        ]);
    }

    /**
     * If server is running (accepting connections and messages).
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }


    /* ---------- Connection management ---------------------------------------------------------------------------- */

    /**
     * Orderly shutdown of server.
     * @param int $closeStatus Default is 1001 "Going away"
     */
    public function shutdown(int $closeStatus = 1001): void
    {
        $this->configuration->getLogger()->info("[{scope}] Shutting down", [
            'scope' => self::SCOPE,
            'server' => $this->identity,
        ]);
        if ($this->getConnectionCount() == 0) {
            $this->disconnect();
            return;
        }
        // Store and reset settings, lock new connections, reset listeners
        $this->allowConnections = false;
        $listeners = $this->listeners;
        $this->listeners = [];
        // Track disconnects
        $this->onDisconnect(function () use ($listeners) {
            if ($this->getConnectionCount() > 0) {
                return;
            }
            $this->disconnect();
            // Restore settings
            $this->listeners = $listeners;
        });
        // Close all current connections, listen to acks
        $this->close($closeStatus);
        $this->start();
    }

    /**
     * Disconnect all connections and stop server.
     */
    public function disconnect(): void
    {
        $this->running = false;
        foreach ($this->connections as $connection) {
            $connection->disconnect();
            $this->runner->detach($connection->getIdentity());
            $this->dispatch('disconnect', [$this, $connection]);
        }
        $this->connections = [];
        if ($this->server) {
            $this->server->close();
            $this->runner->detach($this->identity);
        }
        $this->server = null;
        $this->configuration->getLogger()->info("[{scope}] Server disconnected", [
            'scope' => self::SCOPE,
            'server' => $this->identity,
        ]);
    }

    public function getStream(): SocketServer
    {
        return $this->server ?? $this->createSocketServer();
    }


    /* ---------- Internal helper methods -------------------------------------------------------------------------- */

    // Create socket server
    protected function createSocketServer(): SocketServer
    {
        try {
            $uri = new Uri("{$this->scheme}://0.0.0.0:{$this->port}");
            $this->server = $server = $this->streamFactory->createSocketServer(
                $uri,
                $this->configuration->getContext()
            );
            $this->runner->attach($this, function (Runner $runner, Server $server) {
                $this->acceptSocket($server->getStream());
            }, $this->getIdentity());
            $this->allowConnections = true;
            $this->configuration->getLogger()->info("[{scope}] Starting server on {uri}", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
                'uri' => $uri,
            ]);
            return $server;
        } catch (Throwable $e) {
            $error = "Server failed to start: {$e->getMessage()}";
            throw new ServerException($error);
        }
    }

    /**
     * Accept connection on socket server
     * @throws ConnectionFailureException
     */
    protected function acceptSocket(SocketServer $socket): void
    {
        $maxConnections = $this->configuration->getMaxConnections();
        if (!is_null($maxConnections) && $this->getConnectionCount() >= $maxConnections) {
            $this->configuration->getLogger()->warning("[{scope}] Denied connection, reached max {maxConnections}", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
                'connections' => $this->getConnectionCount(),
                'maxConnections' => $maxConnections,
            ]);
            return;
        }
        if (!$this->allowConnections) {
            $this->configuration->getLogger()->warning("[{scope}] Denied connection, shutting down", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
            ]);
            return;
        }
        try {
            /** @var SocketStream $stream */
            $stream = $socket->accept();
            $connection = new Connection(
                $stream,
                false,
                true,
                $this->isSsl(),
                $this->httpFactory,
                clone $this->configuration
            );
            $this->runner->attach($connection, function (Runner $runner, Connection $connection) {
                $key = $connection->getIdentity();

                try {
                    $message = $connection->pullMessage();
                    $this->dispatch($message->getOpcode(), [$this, $connection, $message]);
                } catch (MessageLevelInterface $e) {
                    // Error, but keep connection open
                    $this->configuration->getLogger()->error("[{scope}] {message}", [
                        'scope' => self::SCOPE,
                        'server' => $this->identity,
                        'connection' => $connection->getIdentity(),
                        'exception' => $e,
                        'message' => $e->getMessage(),
                    ]);
                    $this->dispatch('error', [$this, $connection, $e]);
                } catch (ConnectionLevelInterface $e) {
                    // Error, disconnect connection
                    $this->runner->detach($key);
                    unset($this->connections[$key]);
                    $connection->disconnect();
                    $this->configuration->getLogger()->error("[{scope}] {message}", [
                        'scope' => self::SCOPE,
                        'server' => $this->identity,
                        'exception' => $e,
                        'message' => $e->getMessage(),
                    ]);
                    $this->dispatch('error', [$this, $connection, $e]);
                } catch (CloseException $e) {
                    // Should close
                    $connection->close($e->getCloseStatus(), $e->getMessage());
                    $this->configuration->getLogger()->error("[{scope}] {message}", [
                        'scope' => self::SCOPE,
                        'server' => $this->identity,
                        'connection' => $connection->getIdentity(),
                        'exception' => $e,
                        'message' => $e->getMessage(),
                    ]);
                    $this->dispatch('error', [$this, $connection, $e]);
                }
            }, $connection->getIdentity());
        } catch (StreamException $e) {
            throw new ConnectionFailureException("Server failed to accept: {$e->getMessage()}");
        }
        try {
            foreach ($this->middlewares as $middleware) {
                $connection->addMiddleware($middleware);
            }
            /** @throws StreamException */
            $request = $this->performHandshake($connection);
            $this->connections[$connection->getIdentity()] = $connection;
            $this->configuration->getLogger()->info("[{scope}] Accepted connection from {connection}", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
                'connection' => $connection->getIdentity(),
            ]);

            $this->dispatch('handshake', [
                $this,
                $connection,
                $connection->getHandshakeRequest(),
                $connection->getHandshakeResponse(),
            ]);
            $this->dispatch('connect', [$this, $connection, $request]);
        } catch (ExceptionInterface | StreamException $e) {
            $this->runner->detach($connection->getIdentity());
            $connection->disconnect();
            throw new ConnectionFailureException("Server failed to accept: {$e->getMessage()}");
        }
    }

    // Detach connections no longer available
    protected function detachUnconnected(): void
    {
        foreach ($this->connections as $key => $connection) {
            if (!$connection->isConnected()) {
                $this->runner->detach($key);
                unset($this->connections[$key]);
                $this->configuration->getLogger()->info("[{scope}] Disconnected {connection}", [
                    'scope' => self::SCOPE,
                    'server' => $this->identity,
                    'connection' => $connection->getIdentity(),
                ]);
                $this->dispatch('disconnect', [$this, $connection]);
            }
        }
    }

    // Perform upgrade handshake on new connections.
    protected function performHandshake(Connection $connection): ServerRequestInterface
    {
        $response = $this->httpFactory->createResponse(101, 'Switching Protocols');
        $exception = null;

        // Read handshake request
        /** @var ServerRequestInterface */
        $request = $connection->pullHttp();

        // Verify handshake request
        try {
            if ($request->getMethod() != 'GET') {
                throw new HandshakeException(
                    "Handshake request with invalid method: '{$request->getMethod()}'",
                    $response->withStatus(405)
                );
            }
            $connectionHeader = trim($request->getHeaderLine('Connection'));
            if (!str_contains(strtolower($connectionHeader), 'upgrade')) {
                throw new HandshakeException(
                    "Handshake request with invalid Connection header: '{$connectionHeader}'",
                    $response->withStatus(426)
                );
            }
            $upgradeHeader = trim($request->getHeaderLine('Upgrade'));
            if (strtolower($upgradeHeader) != 'websocket') {
                throw new HandshakeException(
                    "Handshake request with invalid Upgrade header: '{$upgradeHeader}'",
                    $response->withStatus(426)
                );
            }
            $versionHeader = trim($request->getHeaderLine('Sec-WebSocket-Version'));
            if ($versionHeader != '13') {
                throw new HandshakeException(
                    "Handshake request with invalid Sec-WebSocket-Version header: '{$versionHeader}'",
                    $response->withStatus(426)->withHeader('Sec-WebSocket-Version', '13')
                );
            }
            $keyHeader = trim($request->getHeaderLine('Sec-WebSocket-Key'));
            if (empty($keyHeader)) {
                throw new HandshakeException(
                    "Handshake request with invalid Sec-WebSocket-Key header: '{$keyHeader}'",
                    $response->withStatus(426)
                );
            }
            if (strlen(base64_decode($keyHeader)) != 16) {
                throw new HandshakeException(
                    "Handshake request with invalid Sec-WebSocket-Key header: '{$keyHeader}'",
                    $response->withStatus(426)
                );
            }

            $responseKey = base64_encode(pack('H*', sha1($keyHeader . Constant::GUID)));
            $response = $response
                ->withHeader('Upgrade', 'websocket')
                ->withHeader('Connection', 'Upgrade')
                ->withHeader('Sec-WebSocket-Accept', $responseKey);
        } catch (HandshakeException $e) {
            $this->configuration->getLogger()->warning("[{scope}] {message}", [
                'scope' => self::SCOPE,
                'server' => $this->identity,
                'connection' => $connection->getIdentity(),
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            $response = $e->getResponse();
            $exception = $e;
        }

        // Respond to handshake
        /** @var ResponseInterface */
        $response = $connection->pushHttp($response);
        if ($response->getStatusCode() != 101) {
            $exception = new HandshakeException("Invalid status code {$response->getStatusCode()}", $response);
        }

        if ($exception) {
            throw $exception;
        }

        $this->configuration->getLogger()->debug("[{scope}] Handshake on {path}", [
            'scope' => self::SCOPE,
            'server' => $this->identity,
            'connection' => $connection->getIdentity(),
            'path' => $request->getUri()->getPath(),
        ]);

        $connection->setHandshakeRequest($request);
        $connection->setHandshakeResponse($response);

        return $request;
    }
}
