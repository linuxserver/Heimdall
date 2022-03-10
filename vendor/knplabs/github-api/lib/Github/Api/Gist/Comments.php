<?php

namespace Github\Api\Gist;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link   https://developer.github.com/v3/gists/comments/
 *
 * @author Kayla Daniels <kayladnls@gmail.com>
 */
class Comments extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @link https://developer.github.com/v3/gists/comments/#custom-media-types
     *
     * @param string|null $bodyType
     *
     * @return $this
     */
    public function configure($bodyType = null)
    {
        if (!in_array($bodyType, ['text', 'html', 'full'])) {
            $bodyType = 'raw';
        }

        $this->acceptHeaderValue = sprintf('application/vnd.github.%s.%s+json', $this->getApiVersion(), $bodyType);

        return $this;
    }

    /**
     * Get all comments for a gist.
     *
     * @param string $gist
     *
     * @return array
     */
    public function all($gist)
    {
        return $this->get('/gists/'.rawurlencode($gist).'/comments');
    }

    /**
     * Get a comment of a gist.
     *
     * @param string $gist
     * @param int    $comment
     *
     * @return array
     */
    public function show($gist, $comment)
    {
        return $this->get('/gists/'.rawurlencode($gist).'/comments/'.$comment);
    }

    /**
     * Create a comment for gist.
     *
     * @param string $gist
     * @param string $body
     *
     * @return array
     */
    public function create($gist, $body)
    {
        return $this->post('/gists/'.rawurlencode($gist).'/comments', ['body' => $body]);
    }

    /**
     * Create a comment for a gist.
     *
     * @param string $gist
     * @param int    $comment_id
     * @param string $body
     *
     * @return array
     */
    public function update($gist, $comment_id, $body)
    {
        return $this->patch('/gists/'.rawurlencode($gist).'/comments/'.$comment_id, ['body' => $body]);
    }

    /**
     * Delete a comment for a gist.
     *
     * @param string $gist
     * @param int    $comment
     *
     * @return array
     */
    public function remove($gist, $comment)
    {
        return $this->delete('/gists/'.rawurlencode($gist).'/comments/'.$comment);
    }
}
