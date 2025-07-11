<?php
namespace Aws\ApplicationSignals;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CloudWatch Application Signals** service.
 * @method \Aws\Result batchGetServiceLevelObjectiveBudgetReport(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetServiceLevelObjectiveBudgetReportAsync(array $args = [])
 * @method \Aws\Result batchUpdateExclusionWindows(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchUpdateExclusionWindowsAsync(array $args = [])
 * @method \Aws\Result createServiceLevelObjective(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createServiceLevelObjectiveAsync(array $args = [])
 * @method \Aws\Result deleteServiceLevelObjective(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteServiceLevelObjectiveAsync(array $args = [])
 * @method \Aws\Result getService(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getServiceAsync(array $args = [])
 * @method \Aws\Result getServiceLevelObjective(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getServiceLevelObjectiveAsync(array $args = [])
 * @method \Aws\Result listServiceDependencies(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceDependenciesAsync(array $args = [])
 * @method \Aws\Result listServiceDependents(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceDependentsAsync(array $args = [])
 * @method \Aws\Result listServiceLevelObjectiveExclusionWindows(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceLevelObjectiveExclusionWindowsAsync(array $args = [])
 * @method \Aws\Result listServiceLevelObjectives(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceLevelObjectivesAsync(array $args = [])
 * @method \Aws\Result listServiceOperations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServiceOperationsAsync(array $args = [])
 * @method \Aws\Result listServices(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listServicesAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result startDiscovery(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startDiscoveryAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateServiceLevelObjective(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateServiceLevelObjectiveAsync(array $args = [])
 */
class ApplicationSignalsClient extends AwsClient {}
