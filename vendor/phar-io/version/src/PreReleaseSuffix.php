<?php
namespace PharIo\Version;

class PreReleaseSuffix
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $number;

    /**
     * @param string   $value
     * @param int|null $number
     */
    public function __construct($value, $number = null)
    {
        $this->value  = $value;
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getNumber()
    {
        return $this->number;
    }
}
