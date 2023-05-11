## UPGRADE from 2.x to 3.0

### General

* The `php-http/httplug` dependency requires is bumped to minimum ^2.1.
* A client implementing `psr/http-client-implementation` is required.
  To upgrade your application (default install) switch from guzzle 6 to guzzle 7 (or replace `php-http/guzzle6-adapter` with any `psr/http-client-implementation`), see the install instructions in the [README file](README.md)
* All previous deprecated code in version 2 is removed.
* The following classes are now final
    * `Github\HttpClient\Message\ResponseMediator`
    * `Github\HttpClient\Plugin\Authentication`
    * `Github\HttpClient\Plugin\GithubExceptionThrower`
    * `Github\HttpClient\Plugin\History`
    * `Github\HttpClient\Plugin\PathPrepend`

### Authentication methods

* `Github\Client::AUTH_URL_TOKEN` use `Github\Client::AUTH_ACCESS_TOKEN` instead.
* `Github\Client::AUTH_URL_CLIENT_ID` use `Github\Client::AUTH_CLIENT_ID` instead.
* `Github\Client::AUTH_HTTP_TOKEN` use `Github\Client::AUTH_ACCESS_TOKEN` instead.
* `Github\Client::AUTH_HTTP_PASSWORD` use `Github\Client::AUTH_ACCESS_TOKEN` instead.
