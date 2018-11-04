<?php

namespace Github\Api\Enterprise;

use Github\Api\AbstractApi;

class License extends AbstractApi
{
    /**
     * Provides information about your Enterprise license (only available to site admins).
     *
     * @link https://developer.github.com/v3/enterprise/license/
     *
     * @return array array of license information
     */
    public function show()
    {
        return $this->get('/enterprise/settings/license');
    }
}
