<?php

namespace Phrity\Util\Transformer;

use JsonException;

class FlattenDecoder implements TransformerInterface
{
    public function __construct(
        private string $separator,
    ) {
    }

    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        return in_array($type, [null, Type::ARRAY]) && in_array(gettype($subject), [Type::ARRAY, Type::OBJECT]);
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        if (empty($this->separator)) {
            return $subject;
        }
        $re = "|^([{$this->separator}]*[^{$this->separator}]+){$this->separator}(.+)|";
        $coll = [];
        foreach ($subject as $key => $value) {
            if ($key == $this->separator) {
                $coll[$this->separator] = $value;
                continue;
            }
            preg_match($re, $key, $res);
            $keyf = $res[1] ?? $key;
            $keyl = $res[2] ?? $this->separator;
            $coll[$keyf][$keyl] = $value;
        }
        foreach ($coll as $key => $sub) {
            if (!is_array($sub)) {
                continue;
            }
            $coll[$key] = $this->transform($sub);
        }
        if (count($coll) === 1 && isset($coll[$this->separator])) {
            return $coll[$this->separator];
        }
        return $coll;
    }
}
