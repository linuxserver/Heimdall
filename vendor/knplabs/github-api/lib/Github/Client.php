<?php

namespace Github;

use Github\Api\AbstractApi;
use Github\Exception\BadMethodCallException;
use Github\Exception\InvalidArgumentException;
use Github\HttpClient\Builder;
use Github\HttpClient\Plugin\Authentication;
use Github\HttpClient\Plugin\GithubExceptionThrower;
use Github\HttpClient\Plugin\History;
use Github\HttpClient\Plugin\PathPrepend;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Simple yet very cool PHP GitHub client.
 *
 * @method Api\CurrentUser                       currentUser()
 * @method Api\CurrentUser                       me()
 * @method Api\Enterprise                        ent()
 * @method Api\Enterprise                        enterprise()
 * @method Api\Miscellaneous\CodeOfConduct       codeOfConduct()
 * @method Api\Miscellaneous\Emojis              emojis()
 * @method Api\Miscellaneous\Licenses            licenses()
 * @method Api\GitData                           git()
 * @method Api\GitData                           gitData()
 * @method Api\Gists                             gist()
 * @method Api\Gists                             gists()
 * @method Api\Miscellaneous\Gitignore           gitignore()
 * @method Api\Apps                              apps()
 * @method Api\Issue                             issue()
 * @method Api\Issue                             issues()
 * @method Api\Markdown                          markdown()
 * @method Api\Notification                      notification()
 * @method Api\Notification                      notifications()
 * @method Api\Organization                      organization()
 * @method Api\Organization                      organizations()
 * @method Api\Organization\Projects             orgProject()
 * @method Api\Organization\Projects             orgProjects()
 * @method Api\Organization\Projects             organizationProject()
 * @method Api\Organization\Projects             organizationProjects()
 * @method Api\Organization\OutsideCollaborators outsideCollaborators()
 * @method Api\PullRequest                       pr()
 * @method Api\PullRequest                       pullRequest()
 * @method Api\PullRequest                       pullRequests()
 * @method Api\RateLimit                         rateLimit()
 * @method Api\Repo                              repo()
 * @method Api\Repo                              repos()
 * @method Api\Repo                              repository()
 * @method Api\Repo                              repositories()
 * @method Api\Search                            search()
 * @method Api\Organization\Teams                team()
 * @method Api\Organization\Teams                teams()
 * @method Api\User                              user()
 * @method Api\User                              users()
 * @method Api\Authorizations                    authorization()
 * @method Api\Authorizations                    authorizations()
 * @method Api\Meta                              meta()
 * @method Api\GraphQL                           graphql()
 * @method Api\Deployment                        deployment()
 * @method Api\Deployment                        deployments()
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 *
 * Website: http://github.com/KnpLabs/php-github-api
 */
class Client
{
    /**
     * Authenticate using a client_id/client_secret combination.
     *
     * @var string
     *
     * @deprecated Use the AuthMethod const
     */
    const AUTH_CLIENT_ID = AuthMethod::CLIENT_ID;

    /**
     * Authenticate using a GitHub access token.
     *
     * @var string
     *
     * @deprecated Use the AuthMethod const
     */
    const AUTH_ACCESS_TOKEN = AuthMethod::ACCESS_TOKEN;

    /**
     * Constant for authentication method.
     *
     * Indicates JSON Web Token authentication required for GitHub apps access
     * to the API.
     *
     * @var string
     *
     * @deprecated Use the AuthMethod const
     */
    const AUTH_JWT = AuthMethod::JWT;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var Builder
     */
    private $httpClientBuilder;

    /**
     * @var History
     */
    private $responseHistory;

    /**
     * Instantiate a new GitHub client.
     *
     * @param Builder|null $httpClientBuilder
     * @param string|null  $apiVersion
     * @param string|null  $enterpriseUrl
     */
    public function __construct(Builder $httpClientBuilder = null, $apiVersion = null, $enterpriseUrl = null)
    {
        $this->responseHistory = new History();
        $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
        $this->apiVersion = $apiVersion ?: 'v3';

        $builder->addPlugin(new GithubExceptionThrower());
        $builder->addPlugin(new Plugin\HistoryPlugin($this->responseHistory));
        $builder->addPlugin(new Plugin\RedirectPlugin());
        $builder->addPlugin(new Plugin\AddHostPlugin(Psr17FactoryDiscovery::findUriFactory()->createUri('https://api.github.com')));
        $builder->addPlugin(new Plugin\HeaderDefaultsPlugin([
            'User-Agent' => 'php-github-api (http://github.com/KnpLabs/php-github-api)',
            'Accept' => sprintf('application/vnd.github.%s+json', $this->apiVersion),
        ]));

        if ($enterpriseUrl) {
            $this->setEnterpriseUrl($enterpriseUrl);
        }
    }

    /**
     * Create a Github\Client using a HTTP client.
     *
     * @param ClientInterface $httpClient
     *
     * @return Client
     */
    public static function createWithHttpClient(ClientInterface $httpClient): self
    {
        $builder = new Builder($httpClient);

        return new self($builder);
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractApi
     */
    public function api($name): AbstractApi
    {
        switch ($name) {
            case 'me':
            case 'current_user':
            case 'currentUser':
                $api = new Api\CurrentUser($this);
                break;
            case 'codeOfConduct':
                $api = new Api\Miscellaneous\CodeOfConduct($this);
                break;

            case 'deployment':
            case 'deployments':
                $api = new Api\Deployment($this);
                break;

            case 'ent':
            case 'enterprise':
                $api = new Api\Enterprise($this);
                break;

            case 'emojis':
                $api = new Api\Miscellaneous\Emojis($this);
                break;

            case 'git':
            case 'git_data':
            case 'gitData':
                $api = new Api\GitData($this);
                break;

            case 'gist':
            case 'gists':
                $api = new Api\Gists($this);
                break;

            case 'gitignore':
                $api = new Api\Miscellaneous\Gitignore($this);
                break;

            case 'apps':
                $api = new Api\Apps($this);
                break;

            case 'issue':
            case 'issues':
                $api = new Api\Issue($this);
                break;

            case 'markdown':
                $api = new Api\Markdown($this);
                break;

            case 'licenses':
                $api = new Api\Miscellaneous\Licenses($this);
                break;

            case 'notification':
            case 'notifications':
                $api = new Api\Notification($this);
                break;

            case 'organization':
            case 'organizations':
                $api = new Api\Organization($this);
                break;

            case 'org_project':
            case 'orgProject':
            case 'org_projects':
            case 'orgProjects':
            case 'organization_project':
            case 'organizationProject':
            case 'organization_projects':
            case 'organizationProjects':
                $api = new Api\Organization\Projects($this);
                break;

            case 'pr':
            case 'pulls':
            case 'pullRequest':
            case 'pull_request':
            case 'pullRequests':
            case 'pull_requests':
                $api = new Api\PullRequest($this);
                break;

            case 'rateLimit':
            case 'rate_limit':
                $api = new Api\RateLimit($this);
                break;

            case 'repo':
            case 'repos':
            case 'repository':
            case 'repositories':
                $api = new Api\Repo($this);
                break;

            case 'search':
                $api = new Api\Search($this);
                break;

            case 'team':
            case 'teams':
                $api = new Api\Organization\Teams($this);
                break;

            case 'member':
            case 'members':
                $api = new Api\Organization\Members($this);
                break;

            case 'user':
            case 'users':
                $api = new Api\User($this);
                break;

            case 'authorization':
            case 'authorizations':
                $api = new Api\Authorizations($this);
                break;

            case 'meta':
                $api = new Api\Meta($this);
                break;

            case 'graphql':
                $api = new Api\GraphQL($this);
                break;

            case 'outsideCollaborators':
            case 'outside_collaborators':
                $api = new Api\Organization\OutsideCollaborators($this);
                break;

            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests.
     *
     * @param string      $tokenOrLogin GitHub private token/username/client ID
     * @param string|null $password     GitHub password/secret (optionally can contain $authMethod)
     * @param string|null $authMethod   One of the AUTH_* class constants
     *
     * @throws InvalidArgumentException If no authentication method was given
     *
     * @return void
     */
    public function authenticate($tokenOrLogin, $password = null, $authMethod = null): void
    {
        if (null === $authMethod && (AuthMethod::JWT === $password || AuthMethod::ACCESS_TOKEN === $password)) {
            $authMethod = $password;
            $password = null;
        }

        if (null === $authMethod) {
            throw new InvalidArgumentException('You need to specify authentication method!');
        }

        $this->getHttpClientBuilder()->removePlugin(Authentication::class);
        $this->getHttpClientBuilder()->addPlugin(new Authentication($tokenOrLogin, $password, $authMethod));
    }

    /**
     * Sets the URL of your GitHub Enterprise instance.
     *
     * @param string $enterpriseUrl URL of the API in the form of http(s)://hostname
     *
     * @return void
     */
    private function setEnterpriseUrl($enterpriseUrl): void
    {
        $builder = $this->getHttpClientBuilder();
        $builder->removePlugin(Plugin\AddHostPlugin::class);
        $builder->removePlugin(PathPrepend::class);

        $builder->addPlugin(new Plugin\AddHostPlugin(Psr17FactoryDiscovery::findUriFactory()->createUri($enterpriseUrl)));

        // For GHE, v4 API endpoint is at `api/graphql` so we don't want to add the version number
        // For earlier versions add the version number after /api
        if ($this->getApiVersion() === 'v4') {
            $builder->addPlugin(new PathPrepend('/api'));
        } else {
            $builder->addPlugin(new PathPrepend(sprintf('/api/%s', $this->getApiVersion())));
        }
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param CacheItemPoolInterface $cachePool
     * @param array                  $config
     *
     * @return void
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = []): void
    {
        $this->getHttpClientBuilder()->addCache($cachePool, $config);
    }

    /**
     * Remove the cache plugin.
     *
     * @return void
     */
    public function removeCache(): void
    {
        $this->getHttpClientBuilder()->removeCache();
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return AbstractApi
     */
    public function __call($name, $args): AbstractApi
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }

    /**
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->responseHistory->getLastResponse();
    }

    /**
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * @return Builder
     */
    protected function getHttpClientBuilder(): Builder
    {
        return $this->httpClientBuilder;
    }
}
