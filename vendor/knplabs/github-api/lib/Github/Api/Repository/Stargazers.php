<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link   https://developer.github.com/v3/activity/starring/#list-stargazers
 *
 * @author Nicolas Dupont <nicolas@akeneo.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Stargazers extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @see https://developer.github.com/v3/activity/starring/#alternative-response-with-star-creation-timestamps
     *
     * @param string $bodyType
     *
     * @return $this
     */
    public function configure($bodyType = null)
    {
        if ('star' === $bodyType) {
            $this->acceptHeaderValue = sprintf('application/vnd.github.%s.star+json', $this->getApiVersion());
        }

        return $this;
    }

    public function all($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/stargazers');
    }
}
