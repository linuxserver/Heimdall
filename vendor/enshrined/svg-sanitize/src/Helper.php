<?php
namespace enshrined\svgSanitize;

class Helper
{
    const XLINK_NAMESPACE_URI = 'http://www.w3.org/1999/xlink';

    /**
     * Resolves the `href`/`xlink:href` value of an element.
     *
     * The lookup is case insensitive on purpose: `Sanitizer::cleanHrefAttributes()`
     * normalizes names such as `HrEf`/`xlink:HrEf` back to `href`/`xlink:href`,
     * so anything consuming this helper has to see those attributes as well -
     * otherwise a mixed-case name hides the reference from e.g. the `<use>`
     * reference graph, which is built before that normalization happens.
     *
     * @param \DOMElement $element
     * @return string|null
     */
    public static function getElementHref(\DOMElement $element)
    {
        if ($element->hasAttribute('href')) {
            return $element->getAttribute('href');
        }
        if ($element->hasAttributeNS(self::XLINK_NAMESPACE_URI, 'href')) {
            return $element->getAttributeNS(self::XLINK_NAMESPACE_URI, 'href');
        }
        foreach ($element->attributes as $attribute) {
            if (!$attribute instanceof \DOMAttr
                || strtolower($attribute->localName) !== 'href'
            ) {
                continue;
            }
            $prefix = strtolower((string)$attribute->prefix);
            if ($prefix === '' || $prefix === 'xlink') {
                return $attribute->value;
            }
        }
        return null;
    }

    /**
     * @param string $href
     * @return string|null
     */
    public static function extractIdReferenceFromHref($href)
    {
        if (!is_string($href)) {
            return null;
        }
        // Read the href the way the URL parser does: it trims spaces and control
        // characters from both ends and drops tabs and line breaks anywhere, so
        // `#a ` and `#&#10;a` both reach the element with id `a`.
        $href = str_replace(array("\t", "\n", "\r"), '', trim($href, "\x00..\x20"));
        if (strpos($href, '#') !== 0) {
            return null;
        }
        // A fragment is percent-decoded before it is matched against ids, so
        // `#%61` addresses the element with id `a`.
        return rawurldecode(substr($href, 1));
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
