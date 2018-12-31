<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Debug;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @deprecated since Symfony 4.1
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface TraceableEventDispatcherInterface extends EventDispatcherInterface, ResetInterface
{
    /**
     * Gets the called listeners.
     *
     * @return array An array of called listeners
     */
    public function getCalledListeners();

    /**
     * Gets the not called listeners.
     *
     * @return array An array of not called listeners
     */
    public function getNotCalledListeners();
}
