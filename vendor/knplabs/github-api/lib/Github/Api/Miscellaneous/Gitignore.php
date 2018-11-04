<?php

namespace Github\Api\Miscellaneous;

use Github\Api\AbstractApi;

class Gitignore extends AbstractApi
{
    /**
     * List all templates available to pass as an option when creating a repository.
     *
     * @link https://developer.github.com/v3/gitignore/#listing-available-templates
     *
     * @return array
     */
    public function all()
    {
        return $this->get('/gitignore/templates');
    }

    /**
     * Get a single template.
     *
     * @link https://developer.github.com/v3/gitignore/#get-a-single-template
     *
     * @param string $template
     *
     * @return array
     */
    public function show($template)
    {
        return $this->get('/gitignore/templates/'.rawurlencode($template));
    }
}
