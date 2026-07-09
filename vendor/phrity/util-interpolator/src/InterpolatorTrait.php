<?php

namespace Phrity\Util\Interpolator;

use Phrity\Util\DataAccessor;
use Phrity\Util\Transformer\{
    BasicTypeConverter,
    DateTimeConverter,
    EnumConverter,
    FirstMatchResolver,
    ReadableConverter,
    StringableConverter,
    ThrowableConverter,
    TransformerInterface,
    Type,
};
use Stringable;

trait InterpolatorTrait
{
    /**
     * @param string|Stringable $source
     * @param array<array-key, mixed>|object $replacers
     * @param non-empty-string $separator
     * @param TransformerInterface|null $transformer
     */
    public function interpolate(
        string|Stringable $source,
        array|object $replacers = [],
        string $separator = '.',
        TransformerInterface|null $transformer = null,
    ): string {
        $transformer = $transformer ?? new FirstMatchResolver([
            new DateTimeConverter(),
            new EnumConverter(),
            new ReadableConverter(),
            new ThrowableConverter(),
            new StringableConverter(),
            new BasicTypeConverter(),
        ]);
        $accessor = new DataAccessor($replacers, $separator, $transformer);
        $result = preg_replace_callback('/{([^{}]{1,})}/', function (array $matches) use ($accessor): string {
            return $accessor->get($matches[1], $matches[0], Type::STRING);
        }, $source);
        return $result ?? $source;
    }
}
