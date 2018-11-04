<?php

namespace Github\HttpClient\Plugin;

use Github\Exception\ApiLimitExceedException;
use Github\Exception\ErrorException;
use Github\Exception\RuntimeException;
use Github\Exception\TwoFactorAuthenticationRequiredException;
use Github\Exception\ValidationFailedException;
use Github\HttpClient\Message\ResponseMediator;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GithubExceptionThrower implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
                return $response;
            }

            // If error:
            $remaining = ResponseMediator::getHeader($response, 'X-RateLimit-Remaining');
            if (null != $remaining && 1 > $remaining && 'rate_limit' !== substr($request->getRequestTarget(), 1, 10)) {
                $limit = ResponseMediator::getHeader($response, 'X-RateLimit-Limit');
                $reset = ResponseMediator::getHeader($response, 'X-RateLimit-Reset');

                throw new ApiLimitExceedException($limit, $reset);
            }

            if (401 === $response->getStatusCode()) {
                if ($response->hasHeader('X-GitHub-OTP') && 0 === strpos((string) ResponseMediator::getHeader($response, 'X-GitHub-OTP'), 'required;')) {
                    $type = substr((string) ResponseMediator::getHeader($response, 'X-GitHub-OTP'), 9);

                    throw new TwoFactorAuthenticationRequiredException($type);
                }
            }

            $content = ResponseMediator::getContent($response);
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 400);
                } elseif (422 == $response->getStatusCode() && isset($content['errors'])) {
                    $errors = [];
                    foreach ($content['errors'] as $error) {
                        switch ($error['code']) {
                            case 'missing':
                                $errors[] = sprintf('The %s %s does not exist, for resource "%s"', $error['field'], $error['value'], $error['resource']);
                                break;

                            case 'missing_field':
                                $errors[] = sprintf('Field "%s" is missing, for resource "%s"', $error['field'], $error['resource']);
                                break;

                            case 'invalid':
                                if (isset($error['message'])) {
                                    $errors[] = sprintf('Field "%s" is invalid, for resource "%s": "%s"', $error['field'], $error['resource'], $error['message']);
                                } else {
                                    $errors[] = sprintf('Field "%s" is invalid, for resource "%s"', $error['field'], $error['resource']);
                                }
                                break;

                            case 'already_exists':
                                $errors[] = sprintf('Field "%s" already exists, for resource "%s"', $error['field'], $error['resource']);
                                break;

                            default:
                                $errors[] = $error['message'];
                                break;

                        }
                    }

                    throw new ValidationFailedException('Validation Failed: '.implode(', ', $errors), 422);
                }
            }

            throw new RuntimeException(isset($content['message']) ? $content['message'] : $content, $response->getStatusCode());
        });
    }
}
