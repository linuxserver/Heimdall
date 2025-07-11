<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace Barryvdh\Reflection\DocBlock\Tag;

/**
 * Reflection class for a @template tag in a Docblock.
 *
 * @author  chack1172 <chack1172@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class TemplateTag extends ParamTag
{
    /** @var string */
    protected $templateName = null;

    /** @var string|null */
    protected $bound = null;

    public function getContent()
    {
        if (null === $this->content) {
            $this->content = $this->templateName;
            if (null !== $this->bound) {
                $this->content .= ' of ' . $this->bound;
            }
        }

        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($content)
    {
        $parts = explode(' of ', $content);
        $this->templateName = $parts[0];
        if (isset($parts[1])) {
            $this->bound = $parts[1];
        }

        $this->setDescription('');
        $this->content = $content;
        return $this;
    }

    /**
     * Gets the template name
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Sets the template name
     *
     * @param string $templateName
     *
     * @return $this
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
        $this->content = null;
        return $this;
    }

    /**
     * Gets the bound type
     *
     * @return string|null
     */
    public function getBound()
    {
        return $this->bound;
    }

    /**
     * Sets the bound type
     * @param string|null $bound
     * @return $this
     */
    public function setBound($bound)
    {
        $this->bound = $bound;
        $this->content = null;
        return $this;
    }
}
