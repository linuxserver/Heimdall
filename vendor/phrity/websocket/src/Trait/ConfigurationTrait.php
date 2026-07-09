<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Trait;

use WebSocket\Configuration;

/**
 * Helper for Configuration.
 */
trait ConfigurationTrait
{
    protected Configuration $configuration;

    public function initConfiguration(Configuration|null $configuration = null): self
    {
        $this->configuration = $configuration ?? new Configuration();
        return $this;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }
}
