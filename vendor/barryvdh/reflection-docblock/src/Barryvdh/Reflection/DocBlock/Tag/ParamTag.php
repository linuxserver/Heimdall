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

use Barryvdh\Reflection\DocBlock\Tag;

/**
 * Reflection class for a @param tag in a Docblock.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ParamTag extends ReturnTag
{
    /** @var string */
    protected $variableName = '';

    /** @var bool determines whether this is a variadic argument */
    protected $isVariadic = false;

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (null === $this->content) {
            $this->content
                = "{$this->type} {$this->variableName} {$this->description}";
        }
        return $this->content;
    }
    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        Tag::setContent($content);

        $parts = [];
        $rest = $this->description;

        // parsing generics and closures to detect types
        for($pos = 0, $stacks = []; $pos < strlen($rest); $pos++) {
            $char = $rest[$pos];

            if(in_array($char, ['<', '(', '[', '{'])) {
                array_unshift($stacks, $char);
            }
            if(
                ($char === '>' && isset($stacks[0]) && $stacks[0] === '<')
                || ($char === ')' && isset($stacks[0]) && $stacks[0] === '(')
                || ($char === ']' && isset($stacks[0]) && $stacks[0] === '[')
                || ($char === '}' && isset($stacks[0]) && $stacks[0] === '{')
            ) {
                array_shift($stacks);
            }

            if(!$stacks && preg_match('/\A(\s+)(.*)/su', substr($rest, $pos), $matches)) {
                $parts[0] = substr($rest, 0, $pos);
                $parts[1] = $matches[1];
                $rest = $matches[2];

                break;
            }
        }

        array_push($parts, ...preg_split('/(\s+)/u', $rest, 2, PREG_SPLIT_DELIM_CAPTURE));

        // if the first item that is encountered is not a variable; it is a type
        if (isset($parts[0])
            && (strlen($parts[0]) > 0)
            && ($parts[0][0] !== '$')
        ) {
            $this->type = array_shift($parts);
            array_shift($parts);
        }

        // if the next item starts with a $ or ...$ it must be the variable name
        if (isset($parts[0])
            && (strlen($parts[0]) > 0)
            && ($parts[0][0] == '$' || substr($parts[0], 0, 4) === '...$')
        ) {
            $this->variableName = array_shift($parts);
            array_shift($parts);

            if (substr($this->variableName, 0, 3) === '...') {
                $this->isVariadic = true;
                $this->variableName = substr($this->variableName, 3);
            }
        }

        $this->setDescription(implode('', $parts));

        $this->content = $content;
        return $this;
    }

    /**
     * Returns the variable's name.
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Sets the variable's name.
     *
     * @param string $name The new name for this variable.
     *
     * @return $this
     */
    public function setVariableName($name)
    {
        $this->variableName = $name;

        $this->content = null;
        return $this;
    }

    /**
     * Returns whether this tag is variadic.
     *
     * @return boolean
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }
}
