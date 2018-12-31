<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Exception;

use Throwable;

class Validation extends Request
{
    public function __construct($message = "", $code = 0, Throwable $previous = null, $errors)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationErrors() {
        return $this->errors;
    }
}