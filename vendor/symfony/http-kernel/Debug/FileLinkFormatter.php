<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Debug;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Formats debug file links.
 *
 * @author Jérémy Romey <jeremy@free-agent.fr>
 *
 * @final
 */
class FileLinkFormatter
{
    private const FORMATS = [
        'textmate' => 'txmt://open?url=file://%f&line=%l',
        'macvim' => 'mvim://open?url=file://%f&line=%l',
        'emacs' => 'emacs://open?url=file://%f&line=%l',
        'sublime' => 'subl://open?url=file://%f&line=%l',
        'phpstorm' => 'phpstorm://open?file=%f&line=%l',
        'atom' => 'atom://core/open/file?filename=%f&line=%l',
        'vscode' => 'vscode://file/%f:%l',
    ];

    private $fileLinkFormat;
    private $requestStack;
    private $baseDir;
    private $urlFormat;

    /**
     * @param string|\Closure $urlFormat the URL format, or a closure that returns it on-demand
     */
    public function __construct(string $fileLinkFormat = null, RequestStack $requestStack = null, string $baseDir = null, $urlFormat = null)
    {
        $fileLinkFormat = (self::FORMATS[$fileLinkFormat] ?? $fileLinkFormat) ?: ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        if ($fileLinkFormat && !\is_array($fileLinkFormat)) {
            $i = strpos($f = $fileLinkFormat, '&', max(strrpos($f, '%f'), strrpos($f, '%l'))) ?: \strlen($f);
            $fileLinkFormat = [substr($f, 0, $i)] + preg_split('/&([^>]++)>/', substr($f, $i), -1, \PREG_SPLIT_DELIM_CAPTURE);
        }

        $this->fileLinkFormat = $fileLinkFormat;
        $this->requestStack = $requestStack;
        $this->baseDir = $baseDir;
        $this->urlFormat = $urlFormat;
    }

    public function format(string $file, int $line)
    {
        if ($fmt = $this->getFileLinkFormat()) {
            for ($i = 1; isset($fmt[$i]); ++$i) {
                if (str_starts_with($file, $k = $fmt[$i++])) {
                    $file = substr_replace($file, $fmt[$i], 0, \strlen($k));
                    break;
                }
            }

            return strtr($fmt[0], ['%f' => $file, '%l' => $line]);
        }

        return false;
    }

    /**
     * @internal
     */
    public function __sleep(): array
    {
        $this->fileLinkFormat = $this->getFileLinkFormat();

        return ['fileLinkFormat'];
    }

    /**
     * @internal
     */
    public static function generateUrlFormat(UrlGeneratorInterface $router, string $routeName, string $queryString): ?string
    {
        try {
            return $router->generate($routeName).$queryString;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function getFileLinkFormat()
    {
        if ($this->fileLinkFormat) {
            return $this->fileLinkFormat;
        }

        if ($this->requestStack && $this->baseDir && $this->urlFormat) {
            $request = $this->requestStack->getMainRequest();

            if ($request instanceof Request && (!$this->urlFormat instanceof \Closure || $this->urlFormat = ($this->urlFormat)())) {
                return [
                    $request->getSchemeAndHttpHost().$this->urlFormat,
                    $this->baseDir.\DIRECTORY_SEPARATOR, '',
                ];
            }
        }

        return null;
    }
}
