<?php
namespace Aws\MarketplaceAgreement;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Marketplace Agreement Service** service.
 * @method \Aws\Result describeAgreement(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAgreementAsync(array $args = [])
 * @method \Aws\Result getAgreementTerms(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAgreementTermsAsync(array $args = [])
 * @method \Aws\Result searchAgreements(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchAgreementsAsync(array $args = [])
 */
class MarketplaceAgreementClient extends AwsClient {}
