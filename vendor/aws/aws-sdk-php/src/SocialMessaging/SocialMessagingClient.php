<?php
namespace Aws\SocialMessaging;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS End User Messaging Social** service.
 * @method \Aws\Result associateWhatsAppBusinessAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise associateWhatsAppBusinessAccountAsync(array $args = [])
 * @method \Aws\Result deleteWhatsAppMessageMedia(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteWhatsAppMessageMediaAsync(array $args = [])
 * @method \Aws\Result disassociateWhatsAppBusinessAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateWhatsAppBusinessAccountAsync(array $args = [])
 * @method \Aws\Result getLinkedWhatsAppBusinessAccount(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLinkedWhatsAppBusinessAccountAsync(array $args = [])
 * @method \Aws\Result getLinkedWhatsAppBusinessAccountPhoneNumber(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLinkedWhatsAppBusinessAccountPhoneNumberAsync(array $args = [])
 * @method \Aws\Result getWhatsAppMessageMedia(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getWhatsAppMessageMediaAsync(array $args = [])
 * @method \Aws\Result listLinkedWhatsAppBusinessAccounts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listLinkedWhatsAppBusinessAccountsAsync(array $args = [])
 * @method \Aws\Result listTagsForResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \Aws\Result postWhatsAppMessageMedia(array $args = [])
 * @method \GuzzleHttp\Promise\Promise postWhatsAppMessageMediaAsync(array $args = [])
 * @method \Aws\Result putWhatsAppBusinessAccountEventDestinations(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putWhatsAppBusinessAccountEventDestinationsAsync(array $args = [])
 * @method \Aws\Result sendWhatsAppMessage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise sendWhatsAppMessageAsync(array $args = [])
 * @method \Aws\Result tagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise tagResourceAsync(array $args = [])
 * @method \Aws\Result untagResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise untagResourceAsync(array $args = [])
 */
class SocialMessagingClient extends AwsClient {}
