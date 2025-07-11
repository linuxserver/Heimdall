<?php
namespace Aws\BedrockDataAutomation;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Data Automation for Amazon Bedrock** service.
 * @method \Aws\Result createBlueprint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBlueprintAsync(array $args = [])
 * @method \Aws\Result createBlueprintVersion(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBlueprintVersionAsync(array $args = [])
 * @method \Aws\Result createDataAutomationProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createDataAutomationProjectAsync(array $args = [])
 * @method \Aws\Result deleteBlueprint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBlueprintAsync(array $args = [])
 * @method \Aws\Result deleteDataAutomationProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteDataAutomationProjectAsync(array $args = [])
 * @method \Aws\Result getBlueprint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBlueprintAsync(array $args = [])
 * @method \Aws\Result getDataAutomationProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDataAutomationProjectAsync(array $args = [])
 * @method \Aws\Result listBlueprints(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBlueprintsAsync(array $args = [])
 * @method \Aws\Result listDataAutomationProjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listDataAutomationProjectsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateBlueprint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBlueprintAsync(array $args = [])
 * @method \Aws\Result updateDataAutomationProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateDataAutomationProjectAsync(array $args = [])
 */
class BedrockDataAutomationClient extends AwsClient {}
