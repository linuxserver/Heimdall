<?php

namespace Github\Exception;

/**
 * When GitHub returns with a HTTP response that says our request is invalid.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ValidationFailedException extends ErrorException
{
}
