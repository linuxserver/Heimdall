<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub;

use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

/**
 * This is the github manager class.
 *
 * @method \Github\Client                                 connection(string|null $name = null)
 * @method \Github\Client                                 reconnect(string|null $name = null)
 * @method void                                           disconnect(string|null $name = null)
 * @method array<string,\Github\Client>                   getConnections()
 * @method \Github\Api\CurrentUser                        currentUser()
 * @method \Github\Api\CurrentUser                        me()
 * @method \Github\Api\Enterprise                         ent()
 * @method \Github\Api\Enterprise                         enterprise()
 * @method \Github\Api\Miscellaneous\CodeOfConduct        codeOfConduct()
 * @method \Github\Api\Miscellaneous\Emojis               emojis()
 * @method \Github\Api\Miscellaneous\Licenses             licenses()
 * @method \Github\Api\GitData                            git()
 * @method \Github\Api\GitData                            gitData()
 * @method \Github\Api\Gists                              gist()
 * @method \Github\Api\Gists                              gists()
 * @method \Github\Api\Miscellaneous\Gitignore            gitignore()
 * @method \Github\Api\Apps                               apps()
 * @method \Github\Api\Issue                              issue()
 * @method \Github\Api\Issue                              issues()
 * @method \Github\Api\Markdown                           markdown()
 * @method \Github\Api\Notification                       notification()
 * @method \Github\Api\Notification                       notifications()
 * @method \Github\Api\Organization                       organization()
 * @method \Github\Api\Organization                       organizations()
 * @method \Github\Api\Organization\Projects              orgProject()
 * @method \Github\Api\Organization\Projects              orgProjects()
 * @method \Github\Api\Organization\Projects              organizationProject()
 * @method \Github\Api\Organization\Projects              organizationProjects()
 * @method \Github\Api\Organization\OutsideCollaborators  outsideCollaborators()
 * @method \Github\Api\PullRequest                        pr()
 * @method \Github\Api\PullRequest                        pullRequest()
 * @method \Github\Api\PullRequest                        pullRequests()
 * @method \Github\Api\RateLimit                          rateLimit()
 * @method \Github\Api\Repo                               repo()
 * @method \Github\Api\Repo                               repos()
 * @method \Github\Api\Repo                               repository()
 * @method \Github\Api\Repo                               repositories()
 * @method \Github\Api\Search                             search()
 * @method \Github\Api\Organization\Teams                 team()
 * @method \Github\Api\Organization\Teams                 teams()
 * @method \Github\Api\User                               user()
 * @method \Github\Api\User                               users()
 * @method \Github\Api\Authorizations                     authorization()
 * @method \Github\Api\Authorizations                     authorizations()
 * @method \Github\Api\Meta                               meta()
 * @method \Github\Api\GraphQL                            graphql()
 * @method \Github\Api\AbstractApi                        api(string $name)
 * @method void                                           authenticate(string $tokenOrLogin, string|null $password = null, string|null $authMethod = null)
 * @method void                                           setEnterpriseUrl(string $enterpriseUrl)
 * @method string                                         getApiVersion()
 * @method void                                           addCache(\Psr\Cache\CacheItemPoolInterface $cachePool, array $config = [])
 * @method void                                           removeCache()
 * @method \Psr\Http\Message\ResponseInterface|null       getLastResponse()
 * @method \Http\Client\Common\HttpMethodsClientInterface getHttpClient()
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitHubManager extends AbstractManager
{
    /**
     * The factory instance.
     *
     * @var \GrahamCampbell\GitHub\GitHubFactory
     */
    protected $factory;

    /**
     * Create a new github manager instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \GrahamCampbell\GitHub\GitHubFactory    $factory
     *
     * @return void
     */
    public function __construct(Repository $config, GitHubFactory $factory)
    {
        parent::__construct($config);
        $this->factory = $factory;
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return \Github\Client
     */
    protected function createConnection(array $config)
    {
        return $this->factory->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
    {
        return 'github';
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getConnectionConfig(string $name = null)
    {
        $config = parent::getConnectionConfig($name);

        if (is_string($cache = Arr::get($config, 'cache'))) {
            $config['cache'] = $this->getNamedConfig('cache', 'Cache', $cache);
        }

        return $config;
    }

    /**
     * Get the factory instance.
     *
     * @return \GrahamCampbell\GitHub\GitHubFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
