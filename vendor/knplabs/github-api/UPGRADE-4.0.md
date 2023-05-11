## UPGRADE from 3.x to 4.0

### ResultPager

* `\Github\ResultPagerInterface::postFetch` is deprecated, and the method will be removed from the ResultPager interface/class.

### Authentication methods

* `Github\Client::AUTH_CLIENT_ID` is deprecated, use `Github\AuthMethod::CLIENT_ID` instead.
* `Github\Client::AUTH_ACCESS_TOKEN` is deprecated, use `Github\AuthMethod::ACCESS_TOKEN` instead.
* `Github\Client::AUTH_JWT` is deprecated, use `Github\AuthMethod::JWT` instead.
