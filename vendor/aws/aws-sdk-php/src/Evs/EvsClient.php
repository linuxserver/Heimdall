<?php
namespace Aws\Evs;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Elastic VMware Service** service.
 * @method \Aws\Result createEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createEnvironmentAsync(array $args = [])
 * @method \Aws\Result createEnvironmentHost(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createEnvironmentHostAsync(array $args = [])
 * @method \Aws\Result deleteEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteEnvironmentAsync(array $args = [])
 * @method \Aws\Result deleteEnvironmentHost(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteEnvironmentHostAsync(array $args = [])
 * @method \Aws\Result getEnvironment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEnvironmentAsync(array $args = [])
 * @method \Aws\Result listEnvironmentHosts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEnvironmentHostsAsync(array $args = [])
 * @method \Aws\Result listEnvironmentVlans(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEnvironmentVlansAsync(array $args = [])
 * @method \Aws\Result listEnvironments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listEnvironmentsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 */
class EvsClient extends AwsClient {}
