<?php

namespace Github;

use Generator;
use Github\Api\AbstractApi;

/**
 * Pager interface.
 *
 * @author Ramon de la Fuente <ramon@future500.nl>
 * @author Mitchel Verschoof <mitchel@future500.nl>
 * @author Graham Campbell <graham@alt-three.com>
 */
interface ResultPagerInterface
{
    /**
     * Fetch a single result (page) from an api call.
     *
     * @param AbstractApi $api        the Api instance
     * @param string      $method     the method name to call on the Api instance
     * @param array       $parameters the method parameters in an array
     *
     * @return array returns the result of the Api::$method() call
     */
    public function fetch(AbstractApi $api, string $method, array $parameters = []): array;

    /**
     * Fetch all results (pages) from an api call.
     *
     * Use with care - there is no maximum.
     *
     * @param AbstractApi $api        the Api instance
     * @param string      $method     the method name to call on the Api instance
     * @param array       $parameters the method parameters in an array
     *
     * @return array returns a merge of the results of the Api::$method() call
     */
    public function fetchAll(AbstractApi $api, string $method, array $parameters = []): array;

    /**
     * Lazily fetch all results (pages) from an api call.
     *
     * Use with care - there is no maximum.
     *
     * @param AbstractApi $api        the Api instance
     * @param string      $method     the method name to call on the Api instance
     * @param array       $parameters the method parameters in an array
     *
     * @return \Generator returns a merge of the results of the Api::$method() call
     */
    public function fetchAllLazy(AbstractApi $api, string $method, array $parameters = []): Generator;

    /**
     * Method that performs the actual work to refresh the pagination property.
     *
     * @deprecated since 3.2 and will be removed in 4.0.
     *
     * @return void
     */
    public function postFetch(): void;

    /**
     * Check to determine the availability of a next page.
     *
     * @return bool
     */
    public function hasNext(): bool;

    /**
     * Check to determine the availability of a previous page.
     *
     * @return bool
     */
    public function hasPrevious(): bool;

    /**
     * Fetch the next page.
     *
     * @return array
     */
    public function fetchNext(): array;

    /**
     * Fetch the previous page.
     *
     * @return array
     */
    public function fetchPrevious(): array;

    /**
     * Fetch the first page.
     *
     * @return array
     */
    public function fetchFirst(): array;

    /**
     * Fetch the last page.
     *
     * @return array
     */
    public function fetchLast(): array;
}
