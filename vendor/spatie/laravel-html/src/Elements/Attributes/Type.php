<?php

namespace Spatie\Html\Elements\Attributes;

/**
 * @method static typeIf(bool $condition, string|null $type)
 * @method static typeIfNotNull(bool $condition, string|null $type)
 * @method static typeUnless(bool $condition, string|null $type)
 */
trait Type
{
    /**
     * @param string|null $type
     *
     * @return static
     */
    public function type($type)
    {
        return $this->attribute('type', $type);
    }
}
