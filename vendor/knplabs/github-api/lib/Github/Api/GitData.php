<?php

namespace Github\Api;

use Github\Api\GitData\Blobs;
use Github\Api\GitData\Commits;
use Github\Api\GitData\References;
use Github\Api\GitData\Tags;
use Github\Api\GitData\Trees;

/**
 * Getting full versions of specific files and trees in your Git repositories.
 *
 * @link   http://developer.github.com/v3/git/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class GitData extends AbstractApi
{
    /**
     * @return Blobs
     */
    public function blobs()
    {
        return new Blobs($this->getClient());
    }

    /**
     * @return Commits
     */
    public function commits()
    {
        return new Commits($this->getClient());
    }

    /**
     * @return References
     */
    public function references()
    {
        return new References($this->getClient());
    }

    /**
     * @return Tags
     */
    public function tags()
    {
        return new Tags($this->getClient());
    }

    /**
     * @return Trees
     */
    public function trees()
    {
        return new Trees($this->getClient());
    }
}
