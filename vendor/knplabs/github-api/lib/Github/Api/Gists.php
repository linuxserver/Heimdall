<?php

namespace Github\Api;

use Github\Api\Gist\Comments;
use Github\Exception\MissingArgumentException;

/**
 * Creating, editing, deleting and listing gists.
 *
 * @link   http://developer.github.com/v3/gists/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Edoardo Rivello <edoardo.rivello at gmail dot com>
 */
class Gists extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @link https://developer.github.com/v3/gists/#custom-media-types
     *
     * @param string|null $bodyType
     *
     * @return $this
     */
    public function configure($bodyType = null)
    {
        if ('base64' !== $bodyType) {
            $bodyType = 'raw';
        }

        $this->acceptHeaderValue = sprintf('application/vnd.github.%s.%s', $this->getApiVersion(), $bodyType);

        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return array|string
     */
    public function all($type = null)
    {
        if (!in_array($type, ['public', 'starred'])) {
            return $this->get('/gists');
        }

        return $this->get('/gists/'.rawurlencode($type));
    }

    /**
     * @param string $number
     *
     * @return array
     */
    public function show($number)
    {
        return $this->get('/gists/'.rawurlencode($number));
    }

    /**
     * Get a specific revision of a gist.
     *
     * @param string $number
     * @param string $sha
     *
     * @link https://developer.github.com/v3/gists/#get-a-specific-revision-of-a-gist
     *
     * @return array
     */
    public function revision($number, $sha)
    {
        return $this->get('/gists/'.rawurlencode($number).'/'.rawurlencode($sha));
    }

    public function create(array $params)
    {
        if (!isset($params['files']) || (!is_array($params['files']) || 0 === count($params['files']))) {
            throw new MissingArgumentException('files');
        }

        $params['public'] = (bool) $params['public'];

        return $this->post('/gists', $params);
    }

    /**
     * @param string $id
     * @param array  $params
     *
     * @return array
     */
    public function update($id, array $params)
    {
        return $this->patch('/gists/'.rawurlencode($id), $params);
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function commits($id)
    {
        return $this->get('/gists/'.rawurlencode($id).'/commits');
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function fork($id)
    {
        return $this->post('/gists/'.rawurlencode($id).'/fork');
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function forks($id)
    {
        return $this->get('/gists/'.rawurlencode($id).'/forks');
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function remove($id)
    {
        return $this->delete('/gists/'.rawurlencode($id));
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function check($id)
    {
        return $this->get('/gists/'.rawurlencode($id).'/star');
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function star($id)
    {
        return $this->put('/gists/'.rawurlencode($id).'/star');
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function unstar($id)
    {
        return $this->delete('/gists/'.rawurlencode($id).'/star');
    }

    /**
     * Get a gist's comments.
     *
     * @link http://developer.github.com/v3/gists/comments/
     *
     * @return Comments
     */
    public function comments()
    {
        return new Comments($this->getClient());
    }
}
