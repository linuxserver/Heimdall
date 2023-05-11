<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HttpKernelInterface handles a Request to convert it to a Response.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface HttpKernelInterface
{
    public const MAIN_REQUEST = 1;
    public const SUB_REQUEST = 2;

    /**
     * @deprecated since symfony/http-kernel 5.3, use MAIN_REQUEST instead.
     *             To ease the migration, this constant won't be removed until Symfony 7.0.
     */
    public const MASTER_REQUEST = self::MAIN_REQUEST;

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param int  $type  The type of the request
     *                    (one of HttpKernelInterface::MAIN_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool $catch Whether to catch exceptions or not
     *
     * @return Response
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true);
}
