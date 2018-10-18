<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;

/**
 * @link   https://developer.github.com/v3/repos/traffic/
 *
 * @author Miguel Piedrafita <soy@miguelpiedrafita.com>
 */
class Traffic extends AbstractApi
{
    /**
     * @link https://developer.github.com/v3/repos/traffic/#list-referrers
     *
     * @param string $owner
     * @param string $repository
     *
     * @return array
     */
    public function referers($owner, $repository)
    {
        return $this->get('/repos/'.rawurlencode($owner).'/'.rawurlencode($repository).'/traffic/popular/referrers');
    }

    /**
     * @link https://developer.github.com/v3/repos/traffic/#list-paths
     *
     * @param string $owner
     * @param string $repository
     *
     * @return array
     */
    public function paths($owner, $repository)
    {
        return $this->get('/repos/'.rawurlencode($owner).'/'.rawurlencode($repository).'/traffic/popular/paths');
    }

    /**
     * @link https://developer.github.com/v3/repos/traffic/#views
     *
     * @param string $owner
     * @param string $repository
     * @param string $per
     *
     * @return array
     */
    public function views($owner, $repository, $per = 'day')
    {
        return $this->get('/repos/'.rawurlencode($owner).'/'.rawurlencode($repository).'/traffic/views?per='.rawurlencode($per));
    }

    /**
     * @link https://developer.github.com/v3/repos/traffic/#clones
     *
     * @param string $owner
     * @param string $repository
     * @param string $per
     *
     * @return array
     */
    public function clones($owner, $repository, $per = 'day')
    {
        return $this->get('/repos/'.rawurlencode($owner).'/'.rawurlencode($repository).'/traffic/clones?per='.rawurlencode($per));
    }
}
