<?php

/**
 * Make sure the sniff doesn't trigger on closure use.
 */

declare(strict_types=1);

namespace Test;

$cl = function($a) use ($b) {};
