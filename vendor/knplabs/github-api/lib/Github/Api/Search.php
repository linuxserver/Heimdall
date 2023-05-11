<?php

namespace Github\Api;

/**
 * Implement the Search API.
 *
 * @link   https://developer.github.com/v3/search/
 *
 * @author Greg Payne <greg.payne@gmail.com>
 */
class Search extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Search repositories by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-repositories
     *
     * @param string $q     the filter
     * @param string $sort  the sort field
     * @param string $order asc/desc
     *
     * @return array list of repositories found
     */
    public function repositories($q, $sort = 'updated', $order = 'desc')
    {
        return $this->get('/search/repositories', ['q' => $q, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Search issues by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-issues
     *
     * @param string $q     the filter
     * @param string $sort  the sort field
     * @param string $order asc/desc
     *
     * @return array list of issues found
     */
    public function issues($q, $sort = 'updated', $order = 'desc')
    {
        return $this->get('/search/issues', ['q' => $q, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Search code by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-code
     *
     * @param string $q     the filter
     * @param string $sort  the sort field
     * @param string $order asc/desc
     *
     * @return array list of code found
     */
    public function code($q, $sort = 'updated', $order = 'desc')
    {
        return $this->get('/search/code', ['q' => $q, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Search code by filter (q), but will return additional data to highlight
     * the matched results.
     *
     * @link https://docs.github.com/en/rest/reference/search#text-match-metadata
     *
     * @return array list of code found
     */
    public function codeWithMatch(string $q, string $sort = 'updated', string $order = 'desc'): array
    {
        $this->acceptHeaderValue = 'application/vnd.github.v3.text-match+json';

        return $this->code($q, $sort, $order);
    }

    /**
     * Search users by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-users
     *
     * @param string $q     the filter
     * @param string $sort  the sort field
     * @param string $order asc/desc
     *
     * @return array list of users found
     */
    public function users($q, $sort = 'updated', $order = 'desc')
    {
        return $this->get('/search/users', ['q' => $q, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Search commits by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-commits
     *
     * @param string $q     the filter
     * @param string $sort  the sort field
     * @param string $order sort order. asc/desc
     *
     * @return array
     */
    public function commits($q, $sort = null, $order = 'desc')
    {
        // This api is in preview mode, so set the correct accept-header
        $this->acceptHeaderValue = 'application/vnd.github.cloak-preview';

        return $this->get('/search/commits', ['q' => $q, 'sort' => $sort, 'order' => $order]);
    }

    /**
     * Search topics by filter (q).
     *
     * @link https://developer.github.com/v3/search/#search-topics
     *
     * @param string $q the filter
     *
     * @return array
     */
    public function topics($q)
    {
        // This api is in preview mode, so set the correct accept-header
        $this->acceptHeaderValue = 'application/vnd.github.mercy-preview+json';

        return $this->get('/search/topics', ['q' => $q]);
    }
}
