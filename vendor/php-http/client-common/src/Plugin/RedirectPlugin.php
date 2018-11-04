<?php

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Exception\CircularRedirectionException;
use Http\Client\Common\Exception\MultipleRedirectionException;
use Http\Client\Common\Plugin;
use Http\Client\Exception\HttpException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Follow redirections.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class RedirectPlugin implements Plugin
{
    /**
     * Rule on how to redirect, change method for the new request.
     *
     * @var array
     */
    protected $redirectCodes = [
        300 => [
            'switch' => [
                'unless' => ['GET', 'HEAD'],
                'to' => 'GET',
            ],
            'multiple' => true,
            'permanent' => false,
        ],
        301 => [
            'switch' => [
                'unless' => ['GET', 'HEAD'],
                'to' => 'GET',
            ],
            'multiple' => false,
            'permanent' => true,
        ],
        302 => [
            'switch' => [
                'unless' => ['GET', 'HEAD'],
                'to' => 'GET',
            ],
            'multiple' => false,
            'permanent' => false,
        ],
        303 => [
            'switch' => [
                'unless' => ['GET', 'HEAD'],
                'to' => 'GET',
            ],
            'multiple' => false,
            'permanent' => false,
        ],
        307 => [
            'switch' => false,
            'multiple' => false,
            'permanent' => false,
        ],
        308 => [
            'switch' => false,
            'multiple' => false,
            'permanent' => true,
        ],
    ];

    /**
     * Determine how header should be preserved from old request.
     *
     * @var bool|array
     *
     * true     will keep all previous headers (default value)
     * false    will ditch all previous headers
     * string[] will keep only headers with the specified names
     */
    protected $preserveHeader;

    /**
     * Store all previous redirect from 301 / 308 status code.
     *
     * @var array
     */
    protected $redirectStorage = [];

    /**
     * Whether the location header must be directly used for a multiple redirection status code (300).
     *
     * @var bool
     */
    protected $useDefaultForMultiple;

    /**
     * @var array
     */
    protected $circularDetection = [];

    /**
     * @param array $config {
     *
     *     @var bool|string[] $preserve_header True keeps all headers, false remove all of them, an array is interpreted as a list of header names to keep
     *     @var bool $use_default_for_multiple Whether the location header must be directly used for a multiple redirection status code (300).
     * }
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'preserve_header' => true,
            'use_default_for_multiple' => true,
        ]);
        $resolver->setAllowedTypes('preserve_header', ['bool', 'array']);
        $resolver->setAllowedTypes('use_default_for_multiple', 'bool');
        $resolver->setNormalizer('preserve_header', function (OptionsResolver $resolver, $value) {
            if (is_bool($value) && false === $value) {
                return [];
            }

            return $value;
        });
        $options = $resolver->resolve($config);

        $this->preserveHeader = $options['preserve_header'];
        $this->useDefaultForMultiple = $options['use_default_for_multiple'];
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        // Check in storage
        if (array_key_exists((string) $request->getUri(), $this->redirectStorage)) {
            $uri = $this->redirectStorage[(string) $request->getUri()]['uri'];
            $statusCode = $this->redirectStorage[(string) $request->getUri()]['status'];
            $redirectRequest = $this->buildRedirectRequest($request, $uri, $statusCode);

            return $first($redirectRequest);
        }

        return $next($request)->then(function (ResponseInterface $response) use ($request, $first) {
            $statusCode = $response->getStatusCode();

            if (!array_key_exists($statusCode, $this->redirectCodes)) {
                return $response;
            }

            $uri = $this->createUri($response, $request);
            $redirectRequest = $this->buildRedirectRequest($request, $uri, $statusCode);
            $chainIdentifier = spl_object_hash((object) $first);

            if (!array_key_exists($chainIdentifier, $this->circularDetection)) {
                $this->circularDetection[$chainIdentifier] = [];
            }

            $this->circularDetection[$chainIdentifier][] = (string) $request->getUri();

            if (in_array((string) $redirectRequest->getUri(), $this->circularDetection[$chainIdentifier])) {
                throw new CircularRedirectionException('Circular redirection detected', $request, $response);
            }

            if ($this->redirectCodes[$statusCode]['permanent']) {
                $this->redirectStorage[(string) $request->getUri()] = [
                    'uri' => $uri,
                    'status' => $statusCode,
                ];
            }

            // Call redirect request in synchrone
            $redirectPromise = $first($redirectRequest);

            return $redirectPromise->wait();
        });
    }

    /**
     * Builds the redirect request.
     *
     * @param RequestInterface $request    Original request
     * @param UriInterface     $uri        New uri
     * @param int              $statusCode Status code from the redirect response
     *
     * @return MessageInterface|RequestInterface
     */
    protected function buildRedirectRequest(RequestInterface $request, UriInterface $uri, $statusCode)
    {
        $request = $request->withUri($uri);

        if (false !== $this->redirectCodes[$statusCode]['switch'] && !in_array($request->getMethod(), $this->redirectCodes[$statusCode]['switch']['unless'])) {
            $request = $request->withMethod($this->redirectCodes[$statusCode]['switch']['to']);
        }

        if (is_array($this->preserveHeader)) {
            $headers = array_keys($request->getHeaders());

            foreach ($headers as $name) {
                if (!in_array($name, $this->preserveHeader)) {
                    $request = $request->withoutHeader($name);
                }
            }
        }

        return $request;
    }

    /**
     * Creates a new Uri from the old request and the location header.
     *
     * @param ResponseInterface $response The redirect response
     * @param RequestInterface  $request  The original request
     *
     * @throws HttpException                If location header is not usable (missing or incorrect)
     * @throws MultipleRedirectionException If a 300 status code is received and default location cannot be resolved (doesn't use the location header or not present)
     *
     * @return UriInterface
     */
    private function createUri(ResponseInterface $response, RequestInterface $request)
    {
        if ($this->redirectCodes[$response->getStatusCode()]['multiple'] && (!$this->useDefaultForMultiple || !$response->hasHeader('Location'))) {
            throw new MultipleRedirectionException('Cannot choose a redirection', $request, $response);
        }

        if (!$response->hasHeader('Location')) {
            throw new HttpException('Redirect status code, but no location header present in the response', $request, $response);
        }

        $location = $response->getHeaderLine('Location');
        $parsedLocation = parse_url($location);

        if (false === $parsedLocation) {
            throw new HttpException(sprintf('Location %s could not be parsed', $location), $request, $response);
        }

        $uri = $request->getUri();

        if (array_key_exists('scheme', $parsedLocation)) {
            $uri = $uri->withScheme($parsedLocation['scheme']);
        }

        if (array_key_exists('host', $parsedLocation)) {
            $uri = $uri->withHost($parsedLocation['host']);
        }

        if (array_key_exists('port', $parsedLocation)) {
            $uri = $uri->withPort($parsedLocation['port']);
        }

        if (array_key_exists('path', $parsedLocation)) {
            $uri = $uri->withPath($parsedLocation['path']);
        }

        if (array_key_exists('query', $parsedLocation)) {
            $uri = $uri->withQuery($parsedLocation['query']);
        } else {
            $uri = $uri->withQuery('');
        }

        if (array_key_exists('fragment', $parsedLocation)) {
            $uri = $uri->withFragment($parsedLocation['fragment']);
        } else {
            $uri = $uri->withFragment('');
        }

        return $uri;
    }
}
