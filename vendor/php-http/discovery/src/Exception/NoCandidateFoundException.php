<?php

namespace Http\Discovery\Exception;

use Http\Discovery\Exception;

/**
 * When we have used a strategy but no candidates provided by that strategy could be used.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class NoCandidateFoundException extends \Exception implements Exception
{
    /**
     * @param string $strategy
     * @param array  $candidates
     */
    public function __construct($strategy, array $candidates)
    {
        $classes = array_map(
            function ($a) {
                return $a['class'];
            },
            $candidates
        );

        $message = sprintf(
            'No valid candidate found using strategy "%s". We tested the following candidates: %s.',
            $strategy,
            implode(', ', $classes)
        );

        parent::__construct($message);
    }
}
