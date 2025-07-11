<?php

namespace Spatie\FlareClient\Support;

class PhpStackFrameArgumentsFixer
{
    public function enable(): void
    {
        if (! $this->isCurrentlyIgnoringStackFrameArguments()) {
            return;
        }

        ini_set('zend.exception_ignore_args', '0');
    }

    protected function isCurrentlyIgnoringStackFrameArguments(): bool
    {
        return match (ini_get('zend.exception_ignore_args')) {
            '1' => true,
            '0' => false,
            default => false,
        };
    }
}
