<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/repos/releases/
 *
 * @author Matthew Simo <matthew.a.simo@gmail.com>
 * @author Evgeniy Guseletov <d46k16@gmail.com>
 */
class Releases extends AbstractApi
{
    /**
     * Get the latest release.
     *
     * @param string $username
     * @param string $repository
     *
     * @return array
     */
    public function latest($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/latest');
    }

    /**
     * List releases for a tag.
     *
     * @param string $username
     * @param string $repository
     * @param string $tag
     *
     * @return array
     */
    public function tag($username, $repository, $tag)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/tags/'.rawurlencode($tag));
    }

    /**
     * List releases in selected repository.
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param array  $params     the additional parameters like milestone, assignees, labels, sort, direction
     *
     * @return array
     */
    public function all($username, $repository, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases', $params);
    }

    /**
     * Get a release in selected repository.
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param int    $id         the id of the release
     *
     * @return array
     */
    public function show($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/'.$id);
    }

    /**
     * Generate release notes content for a release.
     *
     * @param string $username
     * @param string $repository
     * @param array  $params
     *
     * @return array
     */
    public function generateNotes($username, $repository, array $params)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/generate-notes', $params);
    }

    /**
     * Create new release in selected repository.
     *
     * @param string $username
     * @param string $repository
     * @param array  $params
     *
     * @throws MissingArgumentException
     *
     * @return array
     */
    public function create($username, $repository, array $params)
    {
        if (!isset($params['tag_name'])) {
            throw new MissingArgumentException('tag_name');
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases', $params);
    }

    /**
     * Edit release in selected repository.
     *
     * @param string $username
     * @param string $repository
     * @param int    $id
     * @param array  $params
     *
     * @return array
     */
    public function edit($username, $repository, $id, array $params)
    {
        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/'.$id, $params);
    }

    /**
     * Delete a release in selected repository (Not thoroughly tested!).
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param int    $id         the id of the release
     *
     * @return array
     */
    public function remove($username, $repository, $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/releases/'.$id);
    }

    /**
     * @return Assets
     */
    public function assets()
    {
        return new Assets($this->getClient());
    }
}
