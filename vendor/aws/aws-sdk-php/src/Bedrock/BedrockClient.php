<?php
namespace Aws\Bedrock;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Bedrock** service.
 * @method \Aws\Result createModelCustomizationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createModelCustomizationJobAsync(array $args = [])
 * @method \Aws\Result createProvisionedModelThroughput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProvisionedModelThroughputAsync(array $args = [])
 * @method \Aws\Result deleteCustomModel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteCustomModelAsync(array $args = [])
 * @method \Aws\Result deleteModelInvocationLoggingConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteModelInvocationLoggingConfigurationAsync(array $args = [])
 * @method \Aws\Result deleteProvisionedModelThroughput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProvisionedModelThroughputAsync(array $args = [])
 * @method \Aws\Result getCustomModel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCustomModelAsync(array $args = [])
 * @method \Aws\Result getFoundationModel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getFoundationModelAsync(array $args = [])
 * @method \Aws\Result getModelCustomizationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getModelCustomizationJobAsync(array $args = [])
 * @method \Aws\Result getModelInvocationLoggingConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getModelInvocationLoggingConfigurationAsync(array $args = [])
 * @method \Aws\Result getProvisionedModelThroughput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getProvisionedModelThroughputAsync(array $args = [])
 * @method \Aws\Result listCustomModels(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCustomModelsAsync(array $args = [])
 * @method \Aws\Result listFoundationModels(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listFoundationModelsAsync(array $args = [])
 * @method \Aws\Result listModelCustomizationJobs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listModelCustomizationJobsAsync(array $args = [])
 * @method \Aws\Result listProvisionedModelThroughputs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProvisionedModelThroughputsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result putModelInvocationLoggingConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putModelInvocationLoggingConfigurationAsync(array $args = [])
 * @method \Aws\Result stopModelCustomizationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopModelCustomizationJobAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 * @method \Aws\Result updateProvisionedModelThroughput(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProvisionedModelThroughputAsync(array $args = [])
 */
class BedrockClient extends AwsClient {}
