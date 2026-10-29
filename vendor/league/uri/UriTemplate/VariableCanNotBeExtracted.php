<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UriTemplate;

use Exception;
use League\Uri\Contracts\UriException;
use Throwable;
use TypeError;

use function array_values;
use function get_debug_type;
use function implode;
use function is_string;

class VariableCanNotBeExtracted extends Exception implements UriException
{
    /** @var list<string> */
    public readonly array $names;
    /** @var list<string> */
    public readonly array $missingNames;
    /** @var list<ExtractionErrorReason> */
    public readonly array $reasons;

    protected function __construct(
        string $message,
        array $reasons,
        array $names = [],
        array $missingNames = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);

        $this->reasons = self::uniqueReasons($reasons);
        $this->missingNames = self::uniqueNames($missingNames, 'Missing variable name');
        $this->names = self::uniqueNames($names, 'Expected variable name');
    }

    /**
     * @return list<ExtractionErrorReason>
     */
    private static function uniqueReasons(iterable $reasons): array
    {
        $unique = [];
        foreach ($reasons as $reason) {
            $reason instanceof ExtractionErrorReason || throw new TypeError('An extraction error value must be an '.ExtractionErrorReason::class.'; '.get_debug_type($reason).' received.');
            $unique[$reason->name] = $reason;
        }

        return array_values($unique);
    }

    /**
     * @return list<string>
     */
    private static function uniqueNames(iterable $names, string $message): array
    {
        $unique = [];
        foreach ($names as $name) {
            is_string($name) || throw new TypeError($message.' must be a string; '.get_debug_type($name).' received.');
            $unique[$name] = $name;
        }

        return array_values($unique);
    }

    public static function dueTo(string $message, ExtractionErrorReason ...$reasons): self
    {
        return new self($message, array_values($reasons));
    }

    public static function dueToMissingVariables(string $input, Template $template, ExtractionResult|iterable $missingNames): self
    {
        $missingNames = $missingNames instanceof ExtractionResult ? $missingNames->missingNames() : self::uniqueNames($missingNames, 'Missing variable name');

        return new self(
            message: 'The value "'.$input.'" does not provide all the variables defined by the template "'.$template->value.'"; Missing: "'.implode('", "', $missingNames).'".',
            reasons: [ExtractionErrorReason::MissingVariables],
            names: $template->variableNames,
            missingNames: $missingNames,
        );
    }

    public static function dueToExtractionFailure(string $input, Template $template, self $previous): self
    {
        return new self(
            message: 'The value "'.$input.'" could not be completely extracted using the template "'.$template->value.'"',
            reasons: $previous->reasons,
            names: $template->variableNames,
            missingNames: $previous->missingNames,
            previous: $previous,
        );
    }

    public static function dueToSuitableCandidateNotFound(string $input, array $reasons, array $missingNames = []): self
    {
        return new self(
            message: 'No suitable candidate was found to satisfy the complete extraction of "'.$input.'".',
            reasons: $reasons,
            missingNames: $missingNames,
        );
    }
}
