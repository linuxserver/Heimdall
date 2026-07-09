<?php

namespace Phrity\Util\Transformer;

interface TransformerInterface
{
    public function canTransform(mixed $subject, string|null $type = null): bool;
    public function transform(mixed $subject, string|null $type = null): mixed;
}
