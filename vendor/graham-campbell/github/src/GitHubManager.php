<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub;

use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;

/**
 * This is the github manager class.
 *
 * @method \Github\Api\CurrentUser currentUser()
 * @method \Github\Api\CurrentUser me()
 * @method \Github\Api\Enterprise ent()
 * @method \Github\Api\Enterprise enterprise()
 * @method \Github\Api\Miscellaneous\CodeOfConduct codeOfConduct()
 * @method \Github\Api\Miscellaneous\Emojis emojis()
 * @method \Github\Api\GitData git()
 * @method \Github\Api\GitData gitData()
 * @method \Github\Api\Gists gist()
 * @method \Github\Api\Gists gists()
 * @method \Github\Api\Miscellaneous\Gitignore gitignore()
 * @method \Github\Api\Integrations integration() (deprecated)
 * @method \Github\Api\Integrations integrations() (deprecated)
 * @method \Github\Api\Apps apps()
 * @method \Github\Api\Issue issue()
 * @method \Github\Api\Issue issues()
 * @method \Github\Api\Markdown markdown()
 * @method \Github\Api\Notification notification()
 * @method \Github\Api\Notification notifications()
 * @method \Github\Api\Organization organization()
 * @method \Github\Api\Organization organizations()
 * @method \Github\Api\Organization\Projects orgProject()
 * @method \Github\Api\Organization\Projects orgProjects()
 * @method \Github\Api\Organization\Projects organizationProject()
 * @method \Github\Api\Organization\Projects organizationProjects()
 * @method \Github\Api\PullRequest pr()
 * @method \Github\Api\PullRequest pullRequest()
 * @method \Github\Api\PullRequest pullRequests()
 * @method \Github\Api\RateLimit rateLimit()
 * @method \Github\Api\Repo repo()
 * @method \Github\Api\Repo repos()
 * @method \Github\Api\Repo repository()
 * @method \Github\Api\Repo repositories()
 * @method \Github\Api\Search search()
 * @method \Github\Api\Organization team()
 * @method \Github\Api\Organization teams()
 * @method \Github\Api\User user()
 * @method \Github\Api\User users()
 * @method \Github\Api\Authorizations authorization()
 * @method \Github\Api\Authorizations authorizations()
 * @method \Github\Api\Meta meta()
 * @method \Github\Api\GraphQL graphql()
 * @method \Github\Api\ApiInterface api(string $name)
 * @method void authenticate(string $tokenOrLogin, string|null $password = null, string|null $authMethod = null)
 * @method string getApiVersion()
 * @method void addCache(\Psr\Cache\CacheItemPoolInterface $cachePool, array $config = [])
 * @method void removeCache()
 * @method \Http\Client\Common\HttpMethodsClient getHttpClient()
 * @method \Psr\Http\Message\ResponseInterface|null getLastResponse()
 *
 * @author Graham Campbell <graham@alt-three.com>
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
     * Get the factory instance.
     *
     * @return \GrahamCampbell\GitHub\GitHubFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
