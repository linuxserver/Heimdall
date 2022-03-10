<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link   https://developer.github.com/v3/repos/pages/
 *
 * @author yunwuxin <tzzhangyajun@qq.com>
 */
class Pages extends AbstractApi
{
    use AcceptHeaderTrait;

    public function show($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages');
    }

    public function enable($username, $repository, array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.switcheroo-preview+json';

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages', $params);
    }

    public function disable($username, $repository)
    {
        $this->acceptHeaderValue = 'application/vnd.github.switcheroo-preview+json';

        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages');
    }

    public function update($username, $repository, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages', $params);
    }

    public function requestBuild($username, $repository)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages/builds');
    }

    public function builds($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages/builds');
    }

    public function showLatestBuild($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages/builds/latest');
    }

    public function showBuild($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pages/builds/'.rawurlencode($id));
    }
}
