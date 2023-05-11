<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

/**
 * CompiledRoutes are returned by the RouteCompiler class.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CompiledRoute implements \Serializable
{
    private $variables;
    private $tokens;
    private $staticPrefix;
    private $regex;
    private $pathVariables;
    private $hostVariables;
    private $hostRegex;
    private $hostTokens;

    /**
     * @param string      $staticPrefix  The static prefix of the compiled route
     * @param string      $regex         The regular expression to use to match this route
     * @param array       $tokens        An array of tokens to use to generate URL for this route
     * @param array       $pathVariables An array of path variables
     * @param string|null $hostRegex     Host regex
     * @param array       $hostTokens    Host tokens
     * @param array       $hostVariables An array of host variables
     * @param array       $variables     An array of variables (variables defined in the path and in the host patterns)
     */
    public function __construct(string $staticPrefix, string $regex, array $tokens, array $pathVariables, string $hostRegex = null, array $hostTokens = [], array $hostVariables = [], array $variables = [])
    {
        $this->staticPrefix = $staticPrefix;
        $this->regex = $regex;
        $this->tokens = $tokens;
        $this->pathVariables = $pathVariables;
        $this->hostRegex = $hostRegex;
        $this->hostTokens = $hostTokens;
        $this->hostVariables = $hostVariables;
        $this->variables = $variables;
    }

    public function __serialize(): array
    {
        return [
            'vars' => $this->variables,
            'path_prefix' => $this->staticPrefix,
            'path_regex' => $this->regex,
            'path_tokens' => $this->tokens,
            'path_vars' => $this->pathVariables,
            'host_regex' => $this->hostRegex,
            'host_tokens' => $this->hostTokens,
            'host_vars' => $this->hostVariables,
        ];
    }

    /**
     * @internal
     */
    final public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        $this->variables = $data['vars'];
        $this->staticPrefix = $data['path_prefix'];
        $this->regex = $data['path_regex'];
        $this->tokens = $data['path_tokens'];
        $this->pathVariables = $data['path_vars'];
        $this->hostRegex = $data['host_regex'];
        $this->hostTokens = $data['host_tokens'];
        $this->hostVariables = $data['host_vars'];
    }

    /**
     * @internal
     */
    final public function unserialize($serialized)
    {
        $this->__unserialize(unserialize($serialized, ['allowed_classes' => false]));
    }

    /**
     * Returns the static prefix.
     *
     * @return string
     */
    public function getStaticPrefix()
    {
        return $this->staticPrefix;
    }

    /**
     * Returns the regex.
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Returns the host regex.
     *
     * @return string|null
     */
    public function getHostRegex()
    {
        return $this->hostRegex;
    }

    /**
     * Returns the tokens.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns the host tokens.
     *
     * @return array
     */
    public function getHostTokens()
    {
        return $this->hostTokens;
    }

    /**
     * Returns the variables.
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Returns the path variables.
     *
     * @return array
     */
    public function getPathVariables()
    {
        return $this->pathVariables;
    }

    /**
     * Returns the host variables.
     *
     * @return array
     */
    public function getHostVariables()
    {
        return $this->hostVariables;
    }
}
