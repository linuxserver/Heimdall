<?php

namespace Github\HttpClient\Plugin;

use Github\Exception\ApiLimitExceedException;
use Github\Exception\ErrorException;
use Github\Exception\RuntimeException;
use Github\Exception\SsoRequiredException;
use Github\Exception\TwoFactorAuthenticationRequiredException;
use Github\Exception\ValidationFailedException;
use Github\HttpClient\Message\ResponseMediator;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class GithubExceptionThrower implements Plugin
{
    /**
     * @return Promise
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
                $this->checkGraphqlErrors($response);

                return $response;
            }

            // If error:
            $remaining = ResponseMediator::getHeader($response, 'X-RateLimit-Remaining');
            if ((429 === $response->getStatusCode()) && null !== $remaining && 1 > $remaining && 'rate_limit' !== substr($request->getRequestTarget(), 1, 10)) {
                $limit = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Limit');
                $reset = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Reset');

                throw new ApiLimitExceedException($limit, $reset);
            }

            if ((401 === $response->getStatusCode()) && $response->hasHeader('X-GitHub-OTP') && 0 === strpos((string) ResponseMediator::getHeader($response, 'X-GitHub-OTP'), 'required;')) {
                $type = substr((string) ResponseMediator::getHeader($response, 'X-GitHub-OTP'), 9);

                throw new TwoFactorAuthenticationRequiredException($type);
            }

            $content = ResponseMediator::getContent($response);
            if (is_array($content) && isset($content['message'])) {
                if (400 === $response->getStatusCode()) {
                    throw new ErrorException(sprintf('%s (%s)', $content['message'], $response->getReasonPhrase()), 400);
                }

                if (422 === $response->getStatusCode() && isset($content['errors'])) {
                    $errors = [];
                    foreach ($content['errors'] as $error) {
                        switch ($error['code'] ?? null) {
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
                                if (is_string($error)) {
                                    $errors[] = $error;

                                    break;
                                }

                                if (isset($error['message'])) {
                                    $errors[] = $error['message'];
                                }
                                break;
                        }
                    }

                    throw new ValidationFailedException(
                        $errors ? 'Validation Failed: '.implode(', ', $errors) : 'Validation Failed',
                        422
                    );
                }
            }

            if (502 == $response->getStatusCode() && isset($content['errors']) && is_array($content['errors'])) {
                $errors = [];
                foreach ($content['errors'] as $error) {
                    if (isset($error['message'])) {
                        $errors[] = $error['message'];
                    }
                }

                throw new RuntimeException(implode(', ', $errors), 502);
            }

            if ((403 === $response->getStatusCode()) && $response->hasHeader('X-GitHub-SSO') && 0 === strpos((string) ResponseMediator::getHeader($response, 'X-GitHub-SSO'), 'required;')) {
                // The header will look something like this:
                // required; url=https://github.com/orgs/octodocs-test/sso?authorization_request=AZSCKtL4U8yX1H3sCQIVnVgmjmon5fWxks5YrqhJgah0b2tlbl9pZM4EuMz4
                // So we strip out the first 14 characters, leaving only the URL.
                // @see https://developer.github.com/v3/auth/#authenticating-for-saml-sso
                $url = substr((string) ResponseMediator::getHeader($response, 'X-GitHub-SSO'), 14);

                throw new SsoRequiredException($url);
            }

            $remaining = ResponseMediator::getHeader($response, 'X-RateLimit-Remaining');
            if ((403 === $response->getStatusCode()) && null !== $remaining && 1 > $remaining && isset($content['message']) && (0 === strpos($content['message'], 'API rate limit exceeded'))) {
                $limit = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Limit');
                $reset = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Reset');

                throw new ApiLimitExceedException($limit, $reset);
            }

            $reset = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Reset');
            if ((403 === $response->getStatusCode()) && 0 < $reset && isset($content['message']) && (0 === strpos($content['message'], 'You have exceeded a secondary rate limit'))) {
                $limit = (int) ResponseMediator::getHeader($response, 'X-RateLimit-Limit');

                throw new ApiLimitExceedException($limit, $reset);
            }

            throw new RuntimeException(isset($content['message']) ? $content['message'] : $content, $response->getStatusCode());
        });
    }

    /**
     * The graphql api doesn't return a 5xx http status for errors. Instead it returns a 200 with an error body.
     *
     * @throws RuntimeException
     */
    private function checkGraphqlErrors(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== 200) {
            return;
        }

        $content = ResponseMediator::getContent($response);
        if (!is_array($content)) {
            return;
        }

        if (!isset($content['errors']) || !is_array($content['errors'])) {
            return;
        }

        $errors = [];
        foreach ($content['errors'] as $error) {
            if (isset($error['message'])) {
                $errors[] = $error['message'];
            }
        }

        if (empty($errors)) {
            return;
        }

        throw new RuntimeException(implode(', ', $errors));
    }
}
