<?php

namespace Nexmo;
use Nexmo\Client\Exception;

class ApiErrorHandler {
    public static function check($body, $statusCode) {
        $statusCodeType = (int) ($statusCode / 100);

        // If it's ok, we can continue
        if ($statusCodeType == 2) {
            return;
        }

        // Build up our error message
        $errorMessage = $body['title'];
        if (isset($body['detail']) && $body['detail']) {
            $errorMessage .= ': '.$body['detail'].'.';
        } else {
            $errorMessage .= '.';
        }

        $errorMessage .= ' See '.$body['type'].' for more information';

        // If it's a 5xx error, throw an exception
        if ($statusCodeType == 5) {
            throw new Exception\Server($errorMessage, $statusCode);
        }

        // Otherwise it's a 4xx, so we may have more context for the user
        // If it's a validation error, share that information
        if (isset($body['invalid_parameters'])) {
            throw new Exception\Validation($errorMessage, $statusCode, null, $body['invalid_parameters']);
        }

        // Otherwise throw a normal error
        throw new Exception\Request($errorMessage, $statusCode);
    }
}
