<?php
namespace Aws\ObservabilityAdmin;

use Aws\AwsClient;

/**
 * This client is used to interact with the **CloudWatch Observability Admin Service** service.
 * @method \Aws\Result getTelemetryEvaluationStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTelemetryEvaluationStatusAsync(array $args = [])
 * @method \Aws\Result getTelemetryEvaluationStatusForOrganization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTelemetryEvaluationStatusForOrganizationAsync(array $args = [])
 * @method \Aws\Result listResourceTelemetry(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourceTelemetryAsync(array $args = [])
 * @method \Aws\Result listResourceTelemetryForOrganization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourceTelemetryForOrganizationAsync(array $args = [])
 * @method \Aws\Result startTelemetryEvaluation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startTelemetryEvaluationAsync(array $args = [])
 * @method \Aws\Result startTelemetryEvaluationForOrganization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startTelemetryEvaluationForOrganizationAsync(array $args = [])
 * @method \Aws\Result stopTelemetryEvaluation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopTelemetryEvaluationAsync(array $args = [])
 * @method \Aws\Result stopTelemetryEvaluationForOrganization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopTelemetryEvaluationForOrganizationAsync(array $args = [])
 */
class ObservabilityAdminClient extends AwsClient {}
