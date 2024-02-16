<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

interface Key
{
    /** @return non-empty-string */
    public function contents(): string;

    public function passphrase(): string;
}
