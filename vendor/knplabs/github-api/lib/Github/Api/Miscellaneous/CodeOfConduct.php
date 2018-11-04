<?php

namespace Github\Api\Miscellaneous;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

class CodeOfConduct extends AbstractApi
{
    use AcceptHeaderTrait;

    public function configure()
    {
        $this->acceptHeaderValue = 'application/vnd.github.scarlet-witch-preview+json';

        return $this;
    }

    /**
     * List all codes of conduct.
     *
     * @link https://developer.github.com/v3/codes_of_conduct/#list-all-codes-of-conduct
     *
     * @return array
     */
    public function all()
    {
        return $this->get('/codes_of_conduct');
    }

    /**
     * Get an individual code of conduct.
     *
     * @link https://developer.github.com/v3/codes_of_conduct/#get-an-individual-code-of-conduct
     *
     * @param string $key
     *
     * @return array
     */
    public function show($key)
    {
        return $this->get('/codes_of_conduct/'.rawurlencode($key));
    }
}
