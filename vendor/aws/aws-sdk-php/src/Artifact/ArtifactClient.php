<?php
namespace Aws\Artifact;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Artifact** service.
 * @method \Aws\Result getAccountSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccountSettingsAsync(array $args = [])
 * @method \Aws\Result getReport(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReportAsync(array $args = [])
 * @method \Aws\Result getReportMetadata(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReportMetadataAsync(array $args = [])
 * @method \Aws\Result getTermForReport(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTermForReportAsync(array $args = [])
 * @method \Aws\Result listCustomerAgreements(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCustomerAgreementsAsync(array $args = [])
 * @method \Aws\Result listReports(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listReportsAsync(array $args = [])
 * @method \Aws\Result putAccountSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAccountSettingsAsync(array $args = [])
 */
class ArtifactClient extends AwsClient {}
