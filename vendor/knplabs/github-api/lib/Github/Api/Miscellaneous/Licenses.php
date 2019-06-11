<?php

namespace Github\Api\Miscellaneous;

use Github\Api\AbstractApi;

class Licenses extends AbstractApi
{
    /**
     * Lists all the licenses available on GitHub.
     *
     * @link https://developer.github.com/v3/licenses/
     *
     * @return array
     */
    public function all()
    {
        return $this->get('/licenses');
    }

    /**
     * Get an individual license by its license key.
     *
     * @link https://developer.github.com/v3/licenses/#get-an-individual-license
     *
     * @param string $license
     *
     * @return array
     */
    public function show($license)
    {
        return $this->get('/licenses/'.rawurlencode($license));
    }
}
