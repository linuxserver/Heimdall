<?php
namespace enshrined\svgSanitize;

class Helper
{
    /**
     * @param \DOMElement $element
     * @return string|null
     */
    public static function getElementHref(\DOMElement $element)
    {
        if ($element->hasAttribute('href')) {
            return $element->getAttribute('href');
        }
        if ($element->hasAttributeNS('http://www.w3.org/1999/xlink', 'href')) {
            return $element->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
        }
        return null;
    }

    /**
     * @param string $href
     * @return string|null
     */
    public static function extractIdReferenceFromHref($href)
    {
        if (!is_string($href) || strpos($href, '#') !== 0) {
            return null;
        }
        return substr($href, 1);
    }

    /**
     * @param \DOMElement $needle
     * @param \DOMElement $haystack
     * @return bool
     */
    public static function isElementContainedIn(\DOMElement $needle, \DOMElement $haystack)
    {
        if ($needle === $haystack) {
            return true;
        }
        foreach ($haystack->childNodes as $childNode) {
            if (!$childNode instanceof \DOMElement) {
                continue;
            }
            if (self::isElementContainedIn($needle, $childNode)) {
                return true;
            }
        }
        return false;
    }
}
