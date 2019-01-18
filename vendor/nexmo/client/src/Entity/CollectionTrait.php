<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Application\Application;

/**
 * Common code for iterating over a collection, and using the collection class to discover the API path.
 */
trait CollectionTrait
{
    /**
     * Index of the current resource of the current page
     * @var int
     */
    protected $current;

    /**
     * Current page data.
     * @var array
     */
    protected $page;

    /**
     * Last API Response
     * @var ResponseInterface
     */
    protected $response;

    /**
     * User set page index.
     * @var int
     */
    protected $index;

    /**
     * User set pgge sixe.
     * @var int
     */
    protected $size;

    /**
     * @var FilterInterface
     */
    protected $filter;

    abstract public function getCollectionName();
    abstract public function getCollectionPath();
    abstract public function hydrateEntity($data, $id);

    /**
     * Return the current item, expects concrete collection to handle creating the object.
     * @return mixed
     */
    public function current()
    {
        return $this->hydrateEntity($this->page['_embedded'][$this->getCollectionName()][$this->current], $this->key());
    }

    /**
     * No checks here, just advance the index.
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * Return the ID of the resource, in some cases this is `id`, in others `uuid`.
     * @return string
     */
    public function key()
    {
        if(isset($this->page['_embedded'][$this->getCollectionName()][$this->current]['id'])){
            return $this->page['_embedded'][$this->getCollectionName()][$this->current]['id'];
        } elseif(isset($this->page['_embedded'][$this->getCollectionName()][$this->current]['uuid'])) {
            return $this->page['_embedded'][$this->getCollectionName()][$this->current]['uuid'];
        }

        return $this->current;
    }

    /**
     * Handle pagination automatically (unless configured not to).
     * @return bool
     */
    public function valid()
    {
        //can't be valid if there's not a page (rewind sets this)
        if(!isset($this->page)){
            return false;
        }

        //all hal collections have an `_embedded` object, we expect there to be a property matching the collection name
        if(!isset($this->page['_embedded']) OR !isset($this->page['_embedded'][$this->getCollectionName()])){
            return false;
        }

        //if we have a page with no items, we've gone beyond the end of the collection
        if(!count($this->page['_embedded'][$this->getCollectionName()])){
            return false;
        }

        //index the start of a page at 0
        if(is_null($this->current)){
            $this->current = 0;
        }

        //if our current index is past the current page, fetch the next page if possible and reset the index
        if(!isset($this->page['_embedded'][$this->getCollectionName()][$this->current])){
            if(isset($this->page['_links']) AND isset($this->page['_links']['next'])){
                $this->fetchPage($this->page['_links']['next']['href']);
                $this->current = 0;

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Fetch the initial page
     */
    public function rewind()
    {
        $this->fetchPage($this->getCollectionPath());
    }

    /**
     * Count of total items
     * @return integer
     */
    public function count()
    {
        if(isset($this->page)){
            return (int) $this->page['count'];
        }
    }

    public function setPage($index)
    {
        $this->index = (int) $index;
        return $this;
    }

    public function getPage()
    {
        if(isset($this->page)){
            return $this->page['page_index'];
        }

        if(isset($this->index)){
            return $this->index;
        }

        throw new \RuntimeException('page not set');
    }

    public function getSize()
    {
        if(isset($this->page)){
            return $this->page['page_size'];
        }

        if(isset($this->size)){
            return $this->size;
        }

        throw new \RuntimeException('size not set');
    }

    public function setSize($size)
    {
        $this->size = (int) $size;
        return $this;
    }

    /**
     * Filters reduce to query params and include paging settings.
     *
     * @param FilterInterface $filter
     * @return $this
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function getFilter()
    {
        if(!isset($this->filter)){
            $this->setFilter(new EmptyFilter());
        }

        return $this->filter;
    }

    /**
     * Fetch a page using the current filter if no query is provided.
     *
     * @param $absoluteUri
     */
    protected function fetchPage($absoluteUri)
    {
        //use filter if no query provided
        if(false === strpos($absoluteUri, '?')){
            $query = [];

            if(isset($this->size)){
                $query['page_size'] = $this->size;
            }

            if(isset($this->index)){
                $query['page_index'] = $this->index;
            }

            if(isset($this->filter)){
                $query = array_merge($this->filter->getQuery(), $query);
            }

            $absoluteUri .= '?' . http_build_query($query);
        }

        //
        $request = new Request(
            $this->getClient()->getApiUrl() . $absoluteUri,
            'GET'
        );

        $response = $this->client->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $this->response = $response;
        $this->page = json_decode($this->response->getBody()->getContents(), true);
    }
}
