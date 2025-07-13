<?php
namespace enshrined\svgSanitize\Exceptions;

use Exception;

class NestingException extends \Exception
{
    /**
     * @var \DOMElement
     */
    protected $element;

    /**
     * NestingException constructor.
     *
     * @param string           $message
     * @param int              $code
     * @param Exception|null   $previous
     * @param \DOMElement|null $element
     */
    public function __construct($message = "", $code = 0, ?Exception $previous = null, ?\DOMElement $element = null)
    {
        $this->element = $element;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the element that caused the exception.
     *
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }
}
