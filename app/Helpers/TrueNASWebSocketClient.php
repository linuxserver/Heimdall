<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Phrity\Net\Context;
use WebSocket\Client;
use WebSocket\ConnectionException;

/**
 * TrueNAS JSON-RPC 2.0 WebSocket Client
 *
 * Handles WebSocket communication with TrueNAS using the JSON-RPC 2.0 protocol.
 * Required for TrueNAS 25.04+ as the REST API is deprecated.
 *
 * @see https://api.truenas.com/v25.10/jsonrpc.html
 */
class TrueNASWebSocketClient
{
    private ?Client $client = null;
    private string $url;
    private string $apiKey;
    private bool $ignoreTls;
    private bool $authenticated = false;
    private int $requestId = 1;

    /**
     * Create a new TrueNAS WebSocket client instance.
     *
     * @param string $baseUrl The base URL of the TrueNAS instance (e.g., https://truenas.local)
     * @param string $apiKey The API key for authentication
     * @param bool $ignoreTls Whether to skip TLS certificate verification
     */
    public function __construct(string $baseUrl, string $apiKey, bool $ignoreTls = false)
    {
        $baseUrl = rtrim($baseUrl, '/');
        $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
        $host = parse_url($baseUrl, PHP_URL_HOST);
        $port = parse_url($baseUrl, PHP_URL_PORT);

        $wsScheme = ($scheme === 'https') ? 'wss' : 'ws';
        $portPart = $port ? ':' . $port : '';

        $this->url = "{$wsScheme}://{$host}{$portPart}/api/current";
        $this->apiKey = $apiKey;
        $this->ignoreTls = $ignoreTls;
    }

    /**
     * Connect to the TrueNAS WebSocket API and authenticate.
     *
     * @return bool True if connection and authentication succeeded
     * @throws \Exception If connection or authentication fails
     */
    public function connect(): bool
    {
        if ($this->client !== null && $this->authenticated) {
            return true;
        }

        // Build SSL options - always force HTTP/1.1 via ALPN for WebSocket compatibility
        // TrueNAS nginx defaults to HTTP/2 which doesn't support WebSocket upgrade
        $sslOptions = [
            'alpn_protocols' => 'http/1.1',
        ];

        if ($this->ignoreTls) {
            $sslOptions['verify_peer'] = false;
            $sslOptions['verify_peer_name'] = false;
            $sslOptions['allow_self_signed'] = true;
        }

        // Create context using phrity/net-stream Context class (required by phrity/websocket v3.x)
        $streamContext = stream_context_create(['ssl' => $sslOptions]);
        $context = new Context($streamContext);

        try {
            $this->client = new Client($this->url);
            $this->client->setTimeout(15);
            $this->client->setContext($context);

            $authResult = $this->call('auth.login_with_api_key', [$this->apiKey]);

            if ($authResult === true) {
                $this->authenticated = true;
                return true;
            }

            throw new \Exception('Authentication failed: Invalid API key');
        } catch (ConnectionException $e) {
            Log::error('TrueNAS WebSocket connection failed: ' . $e->getMessage());
            $this->disconnect();
            throw new \Exception('WebSocket connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Make a JSON-RPC 2.0 call to the TrueNAS API.
     *
     * @param string $method The JSON-RPC method name (e.g., 'system.info')
     * @param array $params Optional parameters for the method
     * @return mixed The result from the API call
     * @throws \Exception If the call fails or returns an error
     */
    public function call(string $method, array $params = [])
    {
        if ($this->client === null) {
            throw new \Exception('WebSocket client not connected');
        }

        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'id' => $this->requestId++,
        ];

        if (!empty($params)) {
            $request['params'] = $params;
        }

        try {
            $this->client->text(json_encode($request));
            $response = $this->client->receive();
            $decoded = json_decode($response->getContent(), true);

            if (isset($decoded['error'])) {
                $errorMsg = $decoded['error']['message'] ?? 'Unknown error';
                $errorCode = $decoded['error']['code'] ?? 0;
                throw new \Exception("API error ({$errorCode}): {$errorMsg}");
            }

            return $decoded['result'] ?? null;
        } catch (ConnectionException $e) {
            Log::error('TrueNAS WebSocket call failed: ' . $e->getMessage());
            throw new \Exception('WebSocket call failed: ' . $e->getMessage());
        }
    }

    /**
     * Close the WebSocket connection.
     */
    public function disconnect(): void
    {
        if ($this->client !== null) {
            try {
                $this->client->close();
            } catch (\Exception $e) {
                Log::debug('Error closing WebSocket: ' . $e->getMessage());
            }
            $this->client = null;
            $this->authenticated = false;
        }
    }

    /**
     * Check if the client is connected and authenticated.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->client !== null && $this->authenticated;
    }

    /**
     * Test the connection by calling core.ping.
     *
     * @return bool True if the ping succeeds
     */
    public function ping(): bool
    {
        try {
            $result = $this->call('core.ping');
            return $result === 'pong';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clean up on destruction.
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
