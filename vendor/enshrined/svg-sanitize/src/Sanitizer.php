<?php
namespace enshrined\svgSanitize;

use enshrined\svgSanitize\data\AllowedAttributes;
use enshrined\svgSanitize\data\AllowedTags;
use enshrined\svgSanitize\data\AttributeInterface;
use enshrined\svgSanitize\data\TagInterface;
use enshrined\svgSanitize\data\XPath;
use enshrined\svgSanitize\ElementReference\Resolver;
use enshrined\svgSanitize\Exceptions\RegexException;

/**
 * Class Sanitizer
 *
 * @package enshrined\svgSanitize
 */
class Sanitizer
{

    /**
     * @var \DOMDocument
     */
    protected $xmlDocument;

    /**
     * @var array
     */
    protected $allowedTags;

    /**
     * @var array
     */
    protected $allowedAttrs;

    /**
     * @var
     */
    protected $xmlLoaderValue;

    /**
     * @var bool
     */
    protected $xmlErrorHandlerPreviousValue;

    /**
     * @var bool
     */
    protected $minifyXML = false;

    /**
     * @var bool
     */
    protected $removeRemoteReferences = false;

    /**
     * @var int
     */
    protected $useThreshold = 1000;

    /**
     * @var bool
     */
    protected $removeXMLTag = false;

    /**
     * @var int
     */
    protected $xmlOptions = LIBXML_NOEMPTYTAG;

    /**
     * @var array
     */
    protected $xmlIssues = array();

    /**
     * Issues found after MAX_XML_ISSUES were recorded.
     *
     * @var int
     */
    protected $omittedXmlIssues = 0;

    /**
     * Scratch fragment that removeNode() moves nodes through.
     *
     * @var \DOMDocumentFragment|null
     */
    protected $removedNodes;

    /**
     * The most issues getXmlIssues() lists. Further issues are counted in one
     * closing record instead.
     */
    const MAX_XML_ISSUES = 1000;

    /**
     * The most attributes one element may carry. Real SVG uses a few dozen at
     * most. libxml before 2.10 parses one element's attributes in quadratic
     * time, so input past this limit is rejected before it is parsed.
     */
    const MAX_ATTRIBUTES_PER_ELEMENT = 4096;

    const SVG_NAMESPACE_URI = 'http://www.w3.org/2000/svg';

    /**
     * Attributes whose values are never treated as URLs, after DOMPurify's
     * URI_SAFE_ATTRIBUTES. Every other attribute, except `data-*` and `aria-*`,
     * is checked for script and document URLs.
     *
     * @var string[]
     */
    protected static $inertAttributes = array(
        'alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder',
        'role', 'summary', 'title', 'value', 'style', 'xmlns',
    );

    /**
     * @var Resolver
     */
    protected $elementReferenceResolver;

    /**
     * @var int
     */
    protected $useNestingLimit = 15;

    /**
     * @var bool
     */
    protected $allowHugeFiles = false;

    /**
     * Regex fragment matching the start of a URL that leaves the document: a
     * special scheme, or a protocol-relative `//`.
     *
     * A special scheme is remote whatever follows the colon, because the URL
     * parser does not need the slashes (`https:host` and `https:/host` both
     * reach `https://host`). Browsers also read a backslash as a slash in these
     * URLs, so `/\host` and `\\host` are protocol-relative too.
     */
    const REMOTE_URL_START = '(?:(?:https?|ftp|file):|[/\\\\]{2})';

    /**
     *
     */
    function __construct()
    {
        // Load default tags/attributes
        $this->allowedAttrs = array_map('strtolower', AllowedAttributes::getAttributes());
        $this->allowedTags = array_map('strtolower', AllowedTags::getTags());
    }

    /**
     * Set up the DOMDocument
     */
    protected function resetInternal()
    {
        $this->xmlDocument = new \DOMDocument();
        $this->xmlDocument->preserveWhiteSpace = false;
        $this->xmlDocument->strictErrorChecking = false;
        $this->xmlDocument->formatOutput = !$this->minifyXML;
    }

    /**
     * Set XML options to use when saving XML
     * See: DOMDocument::saveXML
     *
     * @param int  $xmlOptions
     */
    public function setXMLOptions($xmlOptions)
    {
        $this->xmlOptions = $xmlOptions;
    }

    /**
     * Get XML options to use when saving XML
     * See: DOMDocument::saveXML
     *
     * @return int
     */
    public function getXMLOptions()
    {
        return $this->xmlOptions;
    }

    /**
     * Get the array of allowed tags
     *
     * @return array
     */
    public function getAllowedTags()
    {
        return $this->allowedTags;
    }

    /**
     * Set custom allowed tags
     *
     * @param TagInterface $allowedTags
     */
    public function setAllowedTags(TagInterface $allowedTags)
    {
        $this->allowedTags = array_map('strtolower', $allowedTags::getTags());
    }

    /**
     * Get the array of allowed attributes
     *
     * @return array
     */
    public function getAllowedAttrs()
    {
        return $this->allowedAttrs;
    }

    /**
     * Set custom allowed attributes
     *
     * @param AttributeInterface $allowedAttrs
     */
    public function setAllowedAttrs(AttributeInterface $allowedAttrs)
    {
        $this->allowedAttrs = array_map('strtolower', $allowedAttrs::getAttributes());
    }

    /**
     * Should we remove references to remote files?
     *
     * @param bool $removeRemoteRefs
     */
    public function removeRemoteReferences($removeRemoteRefs = false)
    {
        $this->removeRemoteReferences = $removeRemoteRefs;
    }

    /**
     * Get XML issues.
     *
     * @return array
     */
    public function getXmlIssues() {
        if ($this->omittedXmlIssues === 0) {
            return $this->xmlIssues;
        }

        $issues = $this->xmlIssues;
        $issues[] = array(
            'message' => sprintf('%d more issues not listed', $this->omittedXmlIssues),
            'line' => 0,
        );

        return $issues;
    }

    /**
     * Record an issue, up to MAX_XML_ISSUES of them. Past that, issues are only
     * counted: an input can hold hundreds of thousands of removable nodes, and
     * keeping a record for each one could use more memory than the input itself.
     *
     * @param string $message
     * @param int $line
     */
    protected function addXmlIssue($message, $line)
    {
        if (count($this->xmlIssues) < self::MAX_XML_ISSUES) {
            $this->xmlIssues[] = array(
                'message' => $message,
                'line' => $line,
            );
            return;
        }

        $this->omittedXmlIssues++;
    }

    /**
     * Can we allow huge files?
     *
     * @return bool
     */
    public function getAllowHugeFiles() {
        return $this->allowHugeFiles;
    }

    /**
     * Set whether we can allow huge files.
     *
     * @param bool $allowHugeFiles
     */
    public function setAllowHugeFiles( $allowHugeFiles ) {
        $this->allowHugeFiles = $allowHugeFiles;
    }


    /**
     * Sanitize the passed string
     *
     * @param string $dirty
     * @return string|false
     */
    public function sanitize($dirty)
    {
        // Every call starts with no issues, so getXmlIssues() never reports a
        // previous document's issues.
        $this->xmlIssues = array();
        $this->omittedXmlIssues = 0;

        // Don't run on an empty string. Compare strictly: empty() is also true
        // for '0', which is not empty input but a document that fails to parse.
        if ($dirty === '' || $dirty === null || $dirty === false) {
            return '';
        }

        $this->resetInternal();
        $this->setUpBefore();

        // Input we cannot process safely (a failed regex, a document the
        // reference graph rejects, or anything unexpected) fails closed with
        // false instead of throwing out of sanitize(). That includes the checks
        // before parsing.
        try {
            $dirty = $this->prepareForParsing($dirty);
            $clean = $dirty === false ? false : $this->sanitizeDocument($dirty);
        } catch (\Throwable $e) {
            $this->addXmlIssue($e->getMessage(), 0);
            $clean = false;
        } finally {
            $this->resetAfter();
        }

        return $clean;
    }

    /**
     * Strip and check the raw input before it reaches libxml.
     *
     * @param string $dirty
     * @return string|false The input to parse, or false if it must be rejected
     */
    protected function prepareForParsing($dirty)
    {
        /*
         * remove php tags, including ones hidden inside other tags
         * i.e. <?p<?php test?>hp echo . ' danger! ';?>
         */
        $dirty = $this->stripPhpTags($dirty);

        // Strip any DOCTYPE/DTD before parsing. This prevents custom entity
        // definitions (which can collide with HTML5 named character references)
        // and DTD-defaulted attributes from ever reaching libxml.
        $dirty = $this->removeDoctype($dirty);
        if ($dirty === false) {
            $this->addXmlIssue('Nested DOCTYPE declaration', 0);
            return false;
        }

        // Stripping can leave nothing behind (input that was only PHP tags or a
        // DOCTYPE). That is not a document, and loadXML('') would throw.
        if (trim($dirty) === '') {
            $this->addXmlIssue('Document is empty', 0);
            return false;
        }

        // Some input is unsafe to hand to libxml at all: libxml before 2.10 parses
        // one element's attributes in quadratic time, and an encoded DTD can be
        // expanded before it is rejected.
        $problem = $this->findProblemBeforeParsing($dirty);
        if ($problem !== null) {
            $this->addXmlIssue($problem, 0);
            return false;
        }

        return $dirty;
    }

    /**
     * Parse and clean the document. Expects setUpBefore() to have run.
     *
     * @param string $dirty
     * @return string|false
     */
    protected function sanitizeDocument($dirty)
    {
        // LIBXML_NONET: never fetch anything over the network while parsing.
        // External entities are already not loaded; this states it outright.
        $options = LIBXML_NONET;
        if ($this->getAllowHugeFiles()) {
            $options |= LIBXML_PARSEHUGE;
        }
        $loaded = $this->xmlDocument->loadXML($dirty, $options);

        // If we couldn't parse the XML then we go no further.
        if (!$loaded) {
            foreach (self::getXmlErrors() as $error) {
                $this->addXmlIssue($error['message'], $error['line']);
            }
            return false;
        }

        // removeDoctype() works on raw bytes, so a DTD can still reach libxml in
        // an encoding it cannot read (UTF-16, for instance). By now libxml has
        // already applied the DTD's entities and attribute defaults, so the only
        // safe answer is to reject the document. A DOCTYPE without an internal
        // subset (e.g. PUBLIC only) is harmless and still allowed.
        $doctype = $this->xmlDocument->doctype;
        if ($doctype !== null && (
            trim((string) $doctype->internalSubset) !== ''
            || $doctype->entities->length > 0
            || $doctype->notations->length > 0
        )) {
            $this->addXmlIssue('Internal DTD subset is not allowed', 0);
            return false;
        }

        // An `<svg>` root in some other namespace is not SVG to an XML reader, but
        // an HTML parser ignores `xmlns` and makes it live SVG when the output is
        // inlined. Accept the SVG namespace, or none.
        $rootNamespace = (string) $this->xmlDocument->documentElement->namespaceURI;
        if ($rootNamespace !== '' && $rootNamespace !== self::SVG_NAMESPACE_URI) {
            $this->addXmlIssue('Root element is not in the SVG namespace', 0);
            return false;
        }

        if ($this->minifyXML) {
            $this->collapseWhitespace();
        }

        // Fails early (with a LogicException) unless the root is a single <svg>.
        $xPath = new XPath($this->xmlDocument);

        // Clean tags and attributes first. The `<use>` limits must be measured on
        // the document a browser will render: before cleaning, a decoy the
        // cleaner removes, an unsafe href hiding a safe one, or an attribute the
        // cleaner renames would show the graph a different reference.
        $this->elementReferenceResolver = null;
        $this->startClean($this->xmlDocument->childNodes, array());
        $this->removeDuplicateIds($xPath);

        // Then measure the `<use>` references and remove any that break the limits.
        $this->elementReferenceResolver = new Resolver($xPath, $this->useNestingLimit);
        $this->elementReferenceResolver->collect();
        $this->removeUnsafeUseElements($this->elementReferenceResolver->getElementsToRemove());

        // Save cleaned XML to a variable
        if ($this->removeXMLTag) {
            $clean = $this->xmlDocument->saveXML($this->xmlDocument->documentElement, $this->xmlOptions);
        } else {
            $clean = $this->xmlDocument->saveXML($this->xmlDocument, $this->xmlOptions);
        }

        // Remove any extra whitespaces when minifying. Text and attribute values
        // were already collapsed before the security checks ran (see
        // collapseWhitespace()), so this only touches the serializer's own
        // line breaks and cannot change a value that was checked.
        if ($this->minifyXML) {
            $clean = $this->pregReplace('/\s+/', ' ', $clean);
        }

        return $clean;
    }

    /**
     * For minify(): collapse every run of whitespace in text, CDATA and
     * attribute values to one space, in the parsed document.
     *
     * This runs before cleaning so the security checks and the `<use>` graph
     * see the values that will be output. Collapsing the serialized output
     * afterwards could change a checked value: in CSS a hex escape swallows one
     * following space, so `url(\2f  /host)` would become `url(\2f /host)`,
     * which is `url(//host)`, and `id="a  b"` would become `id="a b"`.
     */
    protected function collapseWhitespace()
    {
        foreach ($this->walkNodes($this->xmlDocument) as $node) {
            $values = $node instanceof \DOMElement ? iterator_to_array($node->attributes, false) : array($node);
            foreach ($values as $valueNode) {
                if (!$valueNode instanceof \DOMAttr && !$valueNode instanceof \DOMText) {
                    continue;
                }
                $value = $valueNode->nodeValue;
                $collapsed = $this->pregReplace('/\s+/', ' ', $value);
                if ($collapsed !== $value) {
                    $valueNode->nodeValue = $collapsed;
                }
            }
        }
    }

    /**
     * Remove a node from the document.
     *
     * Before PHP 8.1, DOMNode::removeChild() searches the parent's child list
     * for the node first, so removing many children of one parent was
     * quadratic. Moving the node into a scratch fragment unlinks it directly,
     * and removing it from there is instant, because it is the fragment's only
     * child.
     *
     * @param \DOMNode $node
     */
    protected function removeNode(\DOMNode $node)
    {
        if ($this->removedNodes === null || $this->removedNodes->ownerDocument !== $node->ownerDocument) {
            $this->removedNodes = $node->ownerDocument->createDocumentFragment();
        }
        $this->removedNodes->appendChild($node);
        $this->removedNodes->removeChild($node);
    }

    /**
     * Every node under $node, in document order, one at a time.
     *
     * Before PHP 8.4, an XPath query or a node-list snapshot creates a PHP
     * object for every matching node at once, at about half a kilobyte each.
     * Following the sibling links instead keeps only the current path in memory.
     * The next sibling is read before a node is yielded, so the caller may
     * remove that node. The children of a removed node are skipped.
     *
     * @param \DOMNode $node
     * @return \Generator|\DOMNode[]
     */
    protected function walkNodes(\DOMNode $node)
    {
        for ($child = $node->firstChild; $child !== null; $child = $next) {
            $next = $child->nextSibling;
            yield $child;
            if ($child->parentNode !== null && $child->hasChildNodes()) {
                yield from $this->walkNodes($child);
            }
        }
    }

    /**
     * Remove `<?php ... ?>` and `<?= ... ?>` blocks, including blocks that only
     * appear once an inner block has been removed (`<?p<?php a?>hp b?>`).
     *
     * This matches what repeatedly applying `/<\?(=|php)(.+?)\?>/i` until nothing
     * changes would do (the leftmost opener up to the first closer on the same
     * line), but in a single linear pass. The regex loop needed a full pass per
     * nesting level, and the regex itself was quadratic on long lines without JIT.
     *
     * @param string $dirty
     * @return string
     */
    protected function stripPhpTags($dirty)
    {
        // A tag can only be revealed by removing another one, so input without
        // any opener has nothing to strip.
        if (stripos($dirty, '<?php') === false && strpos($dirty, '<?=') === false) {
            return $dirty;
        }

        $length = strlen($dirty);
        // The output is never longer than the input, so write into a buffer of
        // that size. Removing a block then just moves the write position back.
        $buffer = str_repeat(' ', $length);
        $size = 0;
        // Where the leftmost unclosed opener on the current line starts, and
        // where its content begins.
        $openAt = -1;
        $contentAt = -1;

        for ($i = 0; $i < $length; $i++) {
            $char = $dirty[$i];
            $buffer[$size++] = $char;

            if ($char === "\n") {
                // '.' does not match a newline, so a block never spans lines.
                $openAt = -1;
            } elseif ($char === '>') {
                // A closer needs at least one character of content before it.
                if ($openAt >= 0 && $size - 2 > $contentAt && $buffer[$size - 2] === '?') {
                    $size = $openAt;
                    $openAt = -1;
                }
            } elseif ($openAt < 0) {
                if (($char === 'p' || $char === 'P')
                    && $size >= 5
                    && strtolower(substr($buffer, $size - 5, 5)) === '<?php'
                ) {
                    $openAt = $size - 5;
                    $contentAt = $size;
                } elseif ($char === '=' && $size >= 3 && $buffer[$size - 3] === '<' && $buffer[$size - 2] === '?') {
                    $openAt = $size - 3;
                    $contentAt = $size;
                }
            }
        }

        return substr($buffer, 0, $size);
    }

    /**
     * Check the raw input for anything that is unsafe to hand to libxml.
     *
     * @param string $dirty
     * @return string|null The reason to reject the input, or null if it is fine
     */
    protected function findProblemBeforeParsing($dirty)
    {
        $markup = $this->readMarkupBeforeParsing($dirty);
        if ($markup === null) {
            return 'Unsupported document encoding';
        }

        // removeDoctype() has already removed every DOCTYPE from input it can
        // read as bytes. In UTF-16 and UTF-32 it cannot, so look here.
        if ($markup['decoded'] && $this->hasInternalDtdSubset($markup['text'])) {
            return 'Internal DTD subset is not allowed';
        }

        if ($this->hasElementWithTooManyAttributes($markup['text'])) {
            return 'Too many attributes on one element';
        }

        return null;
    }

    /**
     * Read the input's markup the way libxml will, as ASCII.
     *
     * UTF-16 and UTF-32 input is decoded one code unit at a time: ASCII stays,
     * and anything else becomes `x`, so a byte inside a non-ASCII character can
     * never pass for markup. ASCII-compatible encodings are read as they are.
     * Anything else returns null: EBCDIC, UTF-7, an encoding that is not known
     * to be ASCII-compatible, or a declaration that does not match the bytes.
     *
     * @param string $dirty
     * @return array{text: string, decoded: bool}|null
     */
    protected function readMarkupBeforeParsing($dirty)
    {
        // Byte order marks, then the patterns libxml uses to spot `<?xml` in
        // each encoding when there is no byte order mark.
        $signatures = array(
            array("\x00\x00\xFE\xFF", 4, 4, true),
            array("\xFF\xFE\x00\x00", 4, 4, false),
            array("\xFE\xFF", 2, 2, true),
            array("\xFF\xFE", 2, 2, false),
            array("\x00\x00\x00\x3C", 0, 4, true),
            array("\x3C\x00\x00\x00", 0, 4, false),
            array("\x00\x3C\x00\x3F", 0, 2, true),
            array("\x3C\x00\x3F\x00", 0, 2, false),
        );

        $unitSize = 0;
        $bigEndian = false;
        $start = 0;
        foreach ($signatures as $signature) {
            if (strncmp($dirty, $signature[0], strlen($signature[0])) === 0) {
                list(, $start, $unitSize, $bigEndian) = $signature;
                break;
            }
        }

        if ($unitSize === 0) {
            // EBCDIC `<?xm`. Its markup is not ASCII, so it cannot be checked.
            if (strncmp($dirty, "\x4C\x6F\xA7\x94", 4) === 0) {
                return null;
            }
            $declared = $this->findDeclaredEncoding($dirty);
            if ($declared !== null && !$this->isAsciiCompatibleEncoding($declared)) {
                return null;
            }

            return array('text' => $dirty, 'decoded' => false);
        }

        $length = strlen($dirty);
        $units = (int) (($length - $start) / $unitSize);
        $text = str_repeat('x', $units);
        for ($i = 0, $offset = $start; $i < $units; $i++, $offset += $unitSize) {
            $unit = 0;
            for ($byte = 0; $byte < $unitSize; $byte++) {
                $value = ord($dirty[$offset + ($bigEndian ? $byte : $unitSize - 1 - $byte)]);
                $unit = ($unit << 8) | $value;
            }
            if ($unit < 0x80) {
                $text[$i] = chr($unit);
            }
        }

        $declared = $this->findDeclaredEncoding($text);
        if ($declared !== null && !preg_match('/^(?:utf-?(?:16|32)(?:[bl]e)?|ucs-?[24]|iso-10646-ucs-[24])$/i', $declared)) {
            return null;
        }

        return array('text' => $text, 'decoded' => true);
    }

    /**
     * The encoding named in the XML declaration, if there is one.
     *
     * @param string $markup
     * @return string|null
     */
    protected function findDeclaredEncoding($markup)
    {
        if (strncmp(ltrim($markup, "\xEF\xBB\xBF"), '<?xml', 5) !== 0) {
            return null;
        }
        $end = strpos($markup, '?>');
        $declaration = substr($markup, 0, $end === false ? 200 : $end);
        // This runs before parsing, outside the try block in sanitize(), so use
        // preg_match() directly. The subject is one short declaration.
        if (preg_match('/\bencoding\s*=\s*(["\'])([^"\']*)\1/', $declaration, $matches) !== 1) {
            return null;
        }

        return $matches[2];
    }

    /**
     * Is this an encoding whose markup characters are plain ASCII bytes?
     *
     * @param string $encoding
     * @return bool
     */
    protected function isAsciiCompatibleEncoding($encoding)
    {
        return (bool) preg_match(
            '/^(?:utf-?8|(?:us-)?ascii|iso[-_]?8859[-_]?\d+|iso[-_]?646[-_\w]*|latin-?\d+|l\d'
            . '|(?:windows|cp)-?(?:125\d|874|932|936|949|950)|koi8-?[ru]|shift[-_]?jis|sjis|windows-31j'
            . '|euc-?(?:jp|kr|cn|tw)|gb2312|gbk|gb18030|big5(?:-hkscs)?|tis-?620)$/i',
            $encoding
        );
    }

    /**
     * Does a DOCTYPE in this markup carry an internal subset (`[...]`)?
     *
     * @param string $markup
     * @return bool
     */
    protected function hasInternalDtdSubset($markup)
    {
        $lowerMarkup = strtolower($markup);
        $length = strlen($markup);
        $offset = 0;
        while (($start = strpos($lowerMarkup, '<!doctype', $offset)) !== false) {
            for ($i = $start + 9; $i < $length; $i++) {
                $i += strcspn($markup, "\"'[>", $i);
                if ($i >= $length) {
                    break;
                }
                if ($markup[$i] === '[') {
                    return true;
                }
                if ($markup[$i] === '>') {
                    break;
                }
                $close = strpos($markup, $markup[$i], $i + 1);
                if ($close === false) {
                    return false;
                }
                $i = $close;
            }
            if ($i >= $length) {
                return false;
            }
            $offset = $i + 1;
        }

        return false;
    }

    /**
     * Does any element carry more than MAX_ATTRIBUTES_PER_ELEMENT attributes?
     *
     * Counts each `=` in a start tag outside quoted values, and skips comments,
     * CDATA sections, processing instructions, end tags and declarations. Text
     * is never scanned, because `<` cannot appear in it unescaped. Linear in the
     * input.
     *
     * @param string $markup
     * @return bool
     */
    protected function hasElementWithTooManyAttributes($markup)
    {
        $length = strlen($markup);
        $offset = 0;
        while (($start = strpos($markup, '<', $offset)) !== false) {
            $next = $start + 1;
            $skipTo = null;
            if (substr($markup, $next, 3) === '!--') {
                $skipTo = '-->';
            } elseif (substr($markup, $next, 8) === '![CDATA[') {
                $skipTo = ']]>';
            } elseif ($next < $length && $markup[$next] === '?') {
                $skipTo = '?>';
            } elseif ($next < $length && ($markup[$next] === '!' || $markup[$next] === '/')) {
                $skipTo = '>';
            }

            if ($skipTo !== null) {
                $end = strpos($markup, $skipTo, $next);
                if ($end === false) {
                    return false;
                }
                $offset = $end + strlen($skipTo);
                continue;
            }

            $attributes = 0;
            for ($i = $next; $i < $length; $i++) {
                $i += strcspn($markup, "\"'=>", $i);
                if ($i >= $length || $markup[$i] === '>') {
                    break;
                }
                if ($markup[$i] === '=') {
                    if (++$attributes > self::MAX_ATTRIBUTES_PER_ELEMENT) {
                        return true;
                    }
                    continue;
                }
                $close = strpos($markup, $markup[$i], $i + 1);
                if ($close === false) {
                    return false;
                }
                $i = $close;
            }
            if ($i >= $length) {
                return false;
            }
            $offset = $i + 1;
        }

        return false;
    }

    /**
     * Remove any DOCTYPE declaration (and its internal subset) from the input
     * string before it reaches the XML parser.
     *
     * The internal subset is scanned with balanced brackets so that a `>`
     * appearing inside an entity value cannot prematurely terminate the match.
     *
     * Removing one DOCTYPE can join the text around it into a new one
     * (`<!<!DOCTYPE x>DOCTYPE svg [...]>`). Rather than rescanning, which would
     * be quadratic on deep nesting, any DOCTYPE left in the output means the
     * input is rejected.
     *
     * @param string $dirty
     * @return string|false
     */
    protected function removeDoctype($dirty)
    {
        // Lower-case once and search with strpos(). stripos() lower-cases its
        // whole haystack on every call, which is quadratic over many matches.
        $lowerDirty = strtolower($dirty);
        if (strpos($lowerDirty, '<!doctype') === false) {
            return $dirty;
        }

        $output = '';
        $offset = 0;
        $length = strlen($dirty);

        while (($start = strpos($lowerDirty, '<!doctype', $offset)) !== false) {
            $output .= substr($dirty, $offset, $start - $offset);
            $i = $start + strlen('<!DOCTYPE');
            $depth = 0;
            for (; $i < $length; $i++) {
                $char = $dirty[$i];

                // A '[', ']' or '>' inside a DTD comment is not a real internal-subset
                // delimiter and must not affect the bracket depth.
                if ($char === '<' && substr($dirty, $i, 4) === '<!--') {
                    $commentEnd = strpos($dirty, '-->', $i + 4);
                    if ($commentEnd === false) {
                        $i = $length;
                        break;
                    }
                    $i = $commentEnd + 2;
                    continue;
                }

                // Likewise for a '[', ']' or '>' inside a quoted string.
                if ($char === '"' || $char === "'") {
                    $stringEnd = strpos($dirty, $char, $i + 1);
                    if ($stringEnd === false) {
                        $i = $length;
                        break;
                    }
                    $i = $stringEnd;
                    continue;
                }

                if ($char === '[') {
                    $depth++;
                } elseif ($char === ']') {
                    if ($depth > 0) {
                        $depth--;
                    }
                } elseif ($char === '>' && $depth === 0) {
                    $i++;
                    break;
                }
            }
            $offset = $i;
        }

        $output .= substr($dirty, $offset);

        if (stripos($output, '<!DOCTYPE') !== false) {
            return false;
        }

        return $output;
    }

    /**
     * Set up libXML before we start
     */
    protected function setUpBefore()
    {
        // This function has been deprecated in PHP 8.0 because in libxml 2.9.0, external entity loading is
        // disabled by default, so this function is no longer needed to protect against XXE attacks.
        if (\LIBXML_VERSION < 20900 && \function_exists('libxml_disable_entity_loader')) {
            // Turn off the entity loader
            $this->xmlLoaderValue = libxml_disable_entity_loader(true);
        }

        // Suppress the errors because we don't really have to worry about formation before cleansing.
        // See reset in resetAfter().
        $this->xmlErrorHandlerPreviousValue = libxml_use_internal_errors(true);

        // Reset array of altered XML
        $this->xmlIssues = array();
    }

    /**
     * Reset the class after use
     */
    protected function resetAfter()
    {
        // This function has been deprecated in PHP 8.0 because in libxml 2.9.0, external entity loading is
        // disabled by default, so this function is no longer needed to protect against XXE attacks.
        if (\LIBXML_VERSION < 20900 && \function_exists('libxml_disable_entity_loader')) {
            // Reset the entity loader
            libxml_disable_entity_loader($this->xmlLoaderValue);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($this->xmlErrorHandlerPreviousValue);
    }

    /**
     * Start the cleaning with tags, then we move onto attributes and hrefs later
     *
     * @param \DOMNodeList $elements
     * @param array        $elementsToRemove
     */
    protected function startClean(\DOMNodeList $elements, array $elementsToRemove)
    {
        if ($elements->length === 0) {
            return;
        }

        // Walk the children from the last one back through their sibling links.
        // Calling item($i) on a live \DOMNodeList while stepping backwards
        // re-walks the list from the start every time, which is O(n²). A
        // snapshot of the whole list (iterator_to_array) avoids that, but holds
        // a PHP object for every child at once, which before PHP 8.4 costs about
        // half a kilobyte per node. Reading the previous sibling before handling
        // a node also means removing that node never skips a sibling.
        $parent = $elements->item(0)->parentNode;

        for ($currentElement = $parent->lastChild; $currentElement !== null; $currentElement = $previousSibling) {
            $previousSibling = $currentElement->previousSibling;

            /**
             * If the element has exceeded the nesting limit, we should remove it.
             *
             * As it's only <use> elements that cause us issues with nesting DOS attacks
             * we should check what the element is before removing it. For now we'll only
             * remove <use> elements.
             */
            if ('use' === $currentElement->nodeName && in_array($currentElement, $elementsToRemove)) {
                $this->addXmlIssue('Invalid \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                $this->removeNode($currentElement);
                continue;
            }

            if ($currentElement instanceof \DOMElement) {
                // If the tag isn't in the whitelist, remove it and continue with next iteration
                if (!in_array(strtolower($currentElement->tagName), $this->allowedTags)) {
                    $this->addXmlIssue('Suspicious tag \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                    $this->removeNode($currentElement);
                    continue;
                }

                // The `<use>` limits only measure `<use>` in the root's namespace.
                // `<USE>`, or a `<use>` in another namespace (`xmlns=""`), means
                // nothing in XML, but an HTML parser turns it into a live SVG
                // `<use>` when the output is inlined in a page.
                if (strtolower($currentElement->localName) === 'use'
                    && ($currentElement->tagName !== 'use'
                        || $currentElement->namespaceURI !== $this->xmlDocument->documentElement->namespaceURI)
                ) {
                    $this->addXmlIssue('Suspicious tag \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                    $this->removeNode($currentElement);
                    continue;
                }

                // Strip remote @import / url() references from inline <style> text.
                // The text content of <style> is never otherwise inspected, so remote
                // CSS references would pass straight through.
                if (strtolower($currentElement->tagName) === 'style' && $this->removeRemoteReferences) {
                    $css = $currentElement->textContent;
                    $strippedCss = $this->stripRemoteCssReferences($css);
                    $currentElement->textContent = $strippedCss;
                    // Report the change, like every other removal, so callers
                    // (and the CLI scanner) can tell the input was not clean.
                    if ($strippedCss !== $css) {
                        $this->addXmlIssue('Remote reference removed from \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                    }
                }

                $this->cleanHrefs( $currentElement );

                $this->cleanXlinkHrefs( $currentElement );

                $this->cleanAttributesOnWhitelist($currentElement);

                // The reference limits are checked after cleaning, in
                // removeUnsafeUseElements(), once the graph has been built.
                if (strtolower($currentElement->tagName) === 'use') {
                    if ($this->isUseTagDirty($currentElement)
                        || ($this->elementReferenceResolver !== null && $this->isUseTagExceedingThreshold($currentElement))
                    ) {
                        $this->addXmlIssue('Suspicious \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                        $this->removeNode($currentElement);
                        continue;
                    }
                }

                // Strip out font elements that will break out of foreign content.
                if (strtolower($currentElement->tagName) === 'font') {
                    $breaksOutOfForeignContent = false;
                    foreach ($currentElement->attributes as $attribute) {
                        if (in_array(strtolower($attribute->nodeName), ['face', 'color', 'size'])) {
                            $breaksOutOfForeignContent = true;
                            break;
                        }
                    }

                    if ($breaksOutOfForeignContent) {
                        $this->addXmlIssue('Suspicious tag \'' . $currentElement->tagName . '\'', $currentElement->getLineNo());
                        $this->removeNode($currentElement);
                        continue;
                    }
                }
            }

            $this->cleanUnsafeNodes($currentElement);

            if ($currentElement->hasChildNodes()) {
                $this->startClean($currentElement->childNodes, $elementsToRemove);
            }
        }
    }

    /**
     * Remove the `id` from every element after the first one with an id that a
     * `<use>` references.
     *
     * Browsers resolve `#id` to the first element with that id. Removing the id
     * from later duplicates leaves exactly one possible target for every `<use>`,
     * so the `<use>` graph cannot be shown a different element from the one that
     * renders. Ids that no `<use>` references are left alone, because a CSS
     * `#id` rule styles every element that carries them.
     *
     * @param XPath $xPath
     */
    protected function removeDuplicateIds(XPath $xPath)
    {
        $referencedIds = array();
        foreach ($this->walkNodes($this->xmlDocument) as $node) {
            if ($node instanceof \DOMElement && $node->nodeName === 'use') {
                $useId = Helper::extractIdReferenceFromHref(Helper::getElementHref($node));
                if ($useId !== null) {
                    $referencedIds[$useId] = true;
                }
            }
        }
        if ($referencedIds === array()) {
            return;
        }

        $seen = array();
        foreach ($this->walkNodes($this->xmlDocument) as $element) {
            if (!$element instanceof \DOMElement || !$element->hasAttribute('id')) {
                continue;
            }
            $id = $element->getAttribute('id');
            if (!isset($referencedIds[$id])) {
                continue;
            }
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                continue;
            }
            $element->removeAttribute('id');
            $this->addXmlIssue('Duplicate id \'' . $id . '\'', $element->getLineNo());
        }
    }

    /**
     * Remove `<use>` elements that are part of a reference loop, nest too deeply
     * or expand past the threshold. Runs after cleaning, on the reference graph
     * of the cleaned document.
     *
     * @param array $elementsToRemove Elements the reference graph flagged
     */
    protected function removeUnsafeUseElements(array $elementsToRemove)
    {
        // walkNodes() visits one node at a time, and allows removing it. Walking
        // getElementsByTagName() instead was quadratic before PHP 8.3, and an
        // XPath query held every `<use>` in memory at once before PHP 8.4.
        foreach ($this->walkNodes($this->xmlDocument) as $useElement) {
            if (!$useElement instanceof \DOMElement || $useElement->nodeName !== 'use') {
                continue;
            }

            /**
             * If the element has exceeded the nesting limit, we should remove it.
             *
             * As it's only <use> elements that cause us issues with nesting DOS attacks
             * we should check what the element is before removing it. For now we'll only
             * remove <use> elements.
             */
            if (in_array($useElement, $elementsToRemove)) {
                $this->addXmlIssue('Invalid \'' . $useElement->tagName . '\'', $useElement->getLineNo());
                $this->removeNode($useElement);
                continue;
            }

            if ($this->isUseTagExceedingThreshold($useElement)) {
                $this->addXmlIssue('Suspicious \'' . $useElement->tagName . '\'', $useElement->getLineNo());
                $this->removeNode($useElement);
            }
        }
    }

    /**
     * Only allow attributes that are on the whitelist
     *
     * @param \DOMElement $element
     */
    protected function cleanAttributesOnWhitelist(\DOMElement $element)
    {
        // Work on a static snapshot, and read and remove each attribute through
        // its own node. Going through the element by index (item($x)) or by name
        // (getAttribute()/removeAttribute()) walks its attribute list from the
        // start each time, which is O(n²) in the number of attributes.
        $attributes = iterator_to_array($element->attributes, false);

        for ($x = count($attributes) - 1; $x >= 0; $x--) {
            $attribute = $attributes[$x];
            // get attribute name
            $attrName = $attribute->nodeName;

            // Remove attribute if not in whitelist
            if (!in_array(strtolower($attrName), $this->allowedAttrs) && !$this->isAriaAttribute(strtolower($attrName)) && !$this->isDataAttribute(strtolower($attrName))) {

                $element->removeAttributeNode($attribute);
                $this->addXmlIssue('Suspicious attribute \'' . $attrName . '\'', $element->getLineNo());

                // Once removed, skip the remaining checks for this attribute so the
                // same node is never removed twice.
                continue;
            }

            // `ID` means nothing in XML, but an HTML parser lower-cases it into a
            // live `id` when the output is inlined in a page, where the `<use>`
            // limits never measured it.
            if (strtolower($attrName) === 'id' && $attrName !== 'id') {
                $element->removeAttributeNode($attribute);
                $this->addXmlIssue('Suspicious attribute \'' . $attrName . '\'', $element->getLineNo());
                continue;
            }

            // Remove script and document URLs from every attribute that could hold
            // a URL, in every configuration, as DOMPurify does. Some attributes
            // are live today: when the output is inlined in HTML, an `<image>`
            // inside `<title>` or `<desc>` becomes an `<img>` that uses `src` and
            // `srcset`. Checking the rest means a tag allowed later cannot
            // activate a value that was already approved. `srcset` is a list, so
            // each of its candidates is checked.
            $lowerAttrName = strtolower($attrName);
            $mayHoldUrl = !in_array($lowerAttrName, self::$inertAttributes, true)
                && !$this->isAriaAttribute($lowerAttrName)
                && !$this->isDataAttribute($lowerAttrName);
            if ($mayHoldUrl && $this->hasUnsafeUrlScheme($attribute->value, $lowerAttrName === 'srcset')) {
                $element->removeAttributeNode($attribute);
                $this->addXmlIssue('Suspicious attribute \'' . $attrName . '\'', $element->getLineNo());
                continue;
            }

            /**
             * SMIL animation elements can rewrite an attribute at runtime, so an
             * `href` that looked safe when we validated it can be animated into a
             * `javascript:` URI afterwards. Safari only requires the target to be
             * animatable (not transform-specific), so even `<animateTransform>`
             * can retarget `href`/`xlink:href` via `values`/`by`.
             *
             * Drop the targeting attribute so the animation has nothing to animate.
             * Matched case-insensitively on a substring so `xlink:href`, `HrEf` and
             * padded values are all covered.
             */
            if ('attributename' === strtolower($attrName)
                && false !== stripos($attribute->value, 'href')
            ) {
                $element->removeAttributeNode($attribute);
                $this->addXmlIssue('Suspicious attribute \'' . $attrName . '\'', $element->getLineNo());

                continue;
            }

            /**
             * This is used for when a namespace isn't imported properly.
             * Such as xlink:href when the xlink namespace isn't imported.
             * We have to do this as the link is still ran in this case.
             */
            if (false !== stripos($attrName, 'href')) {
                $href = $attribute->value;
                if (false === $this->isHrefSafeValue($href)) {
                    $element->removeAttributeNode($attribute);
                    $this->addXmlIssue('Suspicious attribute \'href\'', $element->getLineNo());
                    continue;
                }
            }

            // Do we want to strip remote references?
            if($this->removeRemoteReferences) {
                $value = $attribute->value;

                // A remote url()/@import reference, or a value that is itself a remote
                // URL (e.g. a bare href/src). Presentation attributes such as `fill`
                // or `mask` are parsed as CSS, so check them with CSS escapes
                // resolved too (`url(\2f\2f host)` is `url(//host)`), including
                // escapes that only look like comments.
                $isRemote = $this->hasRemoteReference($value)
                    || $this->isRemoteUrl($value)
                    || $this->hasRemoteCssReference($value)
                    // `srcset` is a list of URLs, so a remote candidate can follow
                    // a local one. Checking it as one URL misses that, so remove it
                    // whenever remote references are being removed.
                    || strtolower($attrName) === 'srcset';

                // The style attribute is CSS, so resolve escapes/comments and reuse the
                // same remote-token detection used for <style> elements (this also
                // catches image-set() and escape-obfuscated references).
                if (!$isRemote && strtolower($attrName) === 'style') {
                    $normalized = $this->normalizeCss($value);
                    $isRemote = $this->stripRemoteCssTokens($normalized) !== $normalized;
                }

                // Remove attribute if it has a remote reference
                if ($isRemote) {
                    $element->removeAttributeNode($attribute);
                    $this->addXmlIssue('Suspicious attribute \'' . $attrName . '\'', $element->getLineNo());
                }
            }
        }
    }

    /**
     * Clean the xlink:hrefs of script and data embeds
     *
     * @param \DOMElement $element
     */
    protected function cleanXlinkHrefs(\DOMElement $element)
    {
        // Iterate a snapshot: removing from the live attribute map while walking
        // it skips the attribute after each removal.
        foreach (iterator_to_array($element->attributes, false) as $attribute) {
            // Remove any attribute spelled `xlink:href` that is not in the XLink
            // namespace: an unbound prefix such as `XLinK:href`, or `xlink` bound
            // to another namespace. Neither is XLink in XML, so the `<use>` graph
            // ignores it, but an HTML parser maps the spelling to XLink when the
            // output is inlined in a page.
            if (strtolower($attribute->nodeName) === 'xlink:href' && $attribute->namespaceURI !== Helper::XLINK_NAMESPACE_URI) {
                $element->removeAttributeNode($attribute);
                $this->addXmlIssue(sprintf('Unexpected attribute \'%s\'', $attribute->nodeName), $element->getLineNo());
            }
        }
        $this->cleanHrefAttributes($element, 'xlink');
    }

    /**
     * Clean the hrefs of script and data embeds
     *
     * @param \DOMElement $element
     */
    protected function cleanHrefs(\DOMElement $element)
    {
        $this->cleanHrefAttributes($element);
    }

    protected function cleanHrefAttributes(\DOMElement $element, string $prefix = ''): void
    {
        $relevantAttributes = array_filter(
            iterator_to_array($element->attributes, false),
            static function (\DOMAttr $attr) use ($prefix) {
                return strtolower($attr->name) === 'href' && strtolower($attr->prefix) === $prefix;
            }
        );
        foreach ($relevantAttributes as $attribute) {
            if (!$this->isHrefSafeValue($attribute->value)) {
                $element->removeAttributeNode($attribute);
                $this->addXmlIssue(sprintf('Suspicious attribute \'%s\'', $attribute->nodeName), $element->getLineNo());
                continue;
            }
            // in case the attribute name is `HrEf`/`xlink:HrEf`, adjust it to `href`/`xlink:href`
            $canonicalName = strtolower($attribute->nodeName);
            if (!in_array($attribute->nodeName, $this->allowedAttrs, true)
                && in_array($canonicalName, $this->allowedAttrs, true)
            ) {
                $element->removeAttributeNode($attribute);
                // Never overwrite a correctly spelled attribute: that is the one
                // the element already follows. Keep `xlink:href` in the XLink
                // namespace: when only a prefix like `XLINK` is bound,
                // setAttribute('xlink:href') would create an attribute in no
                // namespace, which the `<use>` graph cannot see but an HTML
                // parser reads as XLink.
                if ($prefix === 'xlink') {
                    if (!$element->hasAttributeNS(Helper::XLINK_NAMESPACE_URI, 'href')) {
                        $element->setAttributeNS(Helper::XLINK_NAMESPACE_URI, 'xlink:href', $attribute->value);
                    }
                } elseif (!$element->hasAttribute($canonicalName)) {
                    $element->setAttribute($canonicalName, $attribute->value);
                }
            }
        }
    }

    /**
     * Only allow whitelisted starts to be within the href.
     *
     * This will stop scripts etc from being passed through, with or without attempting to hide bypasses.
     * This stops the need for us to use a complicated script regex.
     *
     * @param $value
     * @return bool
     */
    protected function isHrefSafeValue($value) {

        // Allow empty values
        if (empty($value)) {
            return true;
        }

        // Allow fragment identifiers.
        if ('#' === substr($value, 0, 1)) {
            return true;
        }

        // Allow relative URIs.
        if ('/' === substr($value, 0, 1)) {
            return true;
        }

        // Allow HTTPS domains.
        if ('https://' === substr($value, 0, 8)) {
            return true;
        }

        // Allow HTTP domains.
        if ('http://' === substr($value, 0, 7)) {
            return true;
        }

        // Allow known data URIs.
        if (in_array(substr($value, 0, 14), array(
            'data:image/png', // PNG
            'data:image/gif', // GIF
            'data:image/jpg', // JPG
            'data:image/jpe', // JPEG
            'data:image/pjp', // PJPEG
        ))) {
            return true;
        }

        // Allow known short data URIs.
        if (in_array(substr($value, 0, 12), array(
            'data:img/png', // PNG
            'data:img/gif', // GIF
            'data:img/jpg', // JPG
            'data:img/jpe', // JPEG
            'data:img/pjp', // PJPEG
        ))) {
            return true;
        }

        return false;
    }

    /**
     * Does a URL, or with $isList any candidate in a `srcset` list, use a script
     * scheme (`javascript:`, `vbscript:`) or a `data:` URL that is not an image?
     *
     * The URL parser drops tabs and line breaks anywhere in a URL and trims
     * control characters, so they are removed before matching.
     *
     * @param string $value
     * @param bool $isList
     * @return bool
     */
    protected function hasUnsafeUrlScheme($value, $isList = false)
    {
        $value = $this->pregReplace('/[\x00-\x1F\x7F]/', '', $value);

        return $this->pregMatch('~' . ($isList ? '(?:^|,)' : '^') . '\s*+(?:(?:java|vb)script:|data:(?!image/))~i', $value);
    }

    /**
     * Removes non-printable ASCII characters from string & trims it
     *
     * @param string $value
     * @return bool
     */
    protected function removeNonPrintableCharacters($value)
    {
        return trim($this->pregReplace('/[^ -~]/xu','',$value));
    }

    /**
     * Does this attribute value embed a remote reference anywhere within it?
     *
     * Detects a remote `url(...)` or remote `@import` regardless of quoting and
     * regardless of where it appears in the value (e.g. amongst other CSS
     * declarations in a `style` attribute). Only remote targets are flagged, so
     * local (`/x`) and fragment (`#x`) references are preserved.
     *
     * @param $value
     * @return bool
     */
    protected function hasRemoteReference($value)
    {
        $value = $this->removeNonPrintableCharacters($value);

        if ($this->pregMatch('~url\(\s*+[\'"]?\s*+' . self::REMOTE_URL_START . '~i', $value)) {
            return true;
        }

        if ($this->pregMatch('~@import\s++(?:url\(\s*+)?[\'"]?\s*+' . self::REMOTE_URL_START . '~i', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Is the value itself a remote URL (a bare href/src rather than a url() wrapper)?
     *
     * Flags absolute (`http(s)`/`ftp`/`file`) and protocol-relative (`//`) URLs while
     * leaving local (`/x`) and fragment (`#x`) references untouched. See
     * REMOTE_URL_START for the forms that count.
     *
     * @param $value
     * @return bool
     */
    protected function isRemoteUrl($value)
    {
        $value = $this->removeNonPrintableCharacters($value);

        return $this->pregMatch('~^\s*+' . self::REMOTE_URL_START . '~i', $value);
    }

    /**
     * Strip remote references (url(), @import, image-set() and quoted remote URLs)
     * from CSS text, used for inline <style> content when removeRemoteReferences
     * is enabled.
     *
     * CSS escapes and comments are resolved first so obfuscated references (e.g.
     * `\75 rl(` or `@\69 mport`) cannot hide from the token match. This remains
     * best-effort: a regex-based stripper does not parse CSS the way a browser
     * does, so untrusted CSS should still be isolated at the embedding boundary.
     *
     * When a block does contain a stripped remote reference, its CSS escapes are
     * normalised (decoded) in the output; any benign escapes in that same block are
     * rewritten to their decoded equivalents (semantically identical).
     *
     * @param string $css
     * @return string
     */
    protected function stripRemoteCssReferences($css)
    {
        $normalized = $this->normalizeCss($css);
        $strippedNormalized = $this->stripRemoteCssTokens($normalized);

        // If decoding escapes/comments exposed a remote reference that the raw text
        // hides, keep the normalized (and stripped) result. Otherwise strip the
        // original in place, leaving legitimate escaped CSS untouched.
        if ($strippedNormalized !== $normalized && $normalized !== $css) {
            $result = $strippedNormalized;
        } else {
            $result = $this->stripRemoteCssTokens($css);
        }

        // Check the result once more, the way a browser reads it. Stripping can
        // join the text around a removed token into a new one, an escaped `/*`
        // can pass for a comment in the normalized copy only, and control
        // characters inside a URL are dropped by the browser. Rather than strip
        // again, drop CSS that still holds a remote reference.
        if ($this->hasRemoteCssReference($result)) {
            return '';
        }

        return $result;
    }

    /**
     * Does this CSS still reference a remote URL when a browser reads it?
     *
     * Resolves escapes and drops control characters, as browsers do inside a URL,
     * but keeps comments: an escaped `/*` or a `/*` inside a string is not a
     * comment, and a real one inside `url()` is part of the URL.
     *
     * @param string $css
     * @return bool
     */
    protected function hasRemoteCssReference($css)
    {
        $decoded = $this->pregReplace('/[\x00-\x1F\x7F]/', '', $this->decodeCssEscapes($css));

        return $this->stripRemoteCssTokens($decoded) !== $decoded;
    }

    /**
     * Remove the CSS constructs that can trigger a remote fetch.
     *
     * @param string $css
     * @return string
     */
    protected function stripRemoteCssTokens($css)
    {
        // Terminate on ')' when present, or on the rule/line boundary ('}', CR, LF)
        // or end of input otherwise. A CSS tokenizer closes an unclosed url()/
        // function token implicitly and still fetches, so requiring a closing paren
        // would let a value that omits it slip past.
        // Removing a construct can join the text on either side into a new one
        // (`uurl(//a)rl(//b)` becomes `url(//b)`). stripRemoteCssReferences()
        // checks its result again and drops CSS where that happened.
        $css = $this->pregReplace('~url\(\s*+[\'"]?\s*+' . self::REMOTE_URL_START . '[^)}\r\n]*\)?~i', '', $css);
        $css = $this->pregReplace('~@import\b[^;]*;?~i', '', $css);
        // image-set() accepts a bare remote string with no url() token of its own.
        $css = $this->stripRemoteImageSets($css);
        // A quoted remote URL can still reach a fetch from outside image-set(),
        // for example through a custom property (`--u:"https://…"` used as
        // `image-set(var(--u) 1x)`), or through image() and src(). Empty every
        // quoted string that starts with a remote URL.
        $css = $this->pregReplace('~([\'"])\s*+' . self::REMOTE_URL_START . '(?:[^\'"\\\\\r\n]|\\\\.)*+\1?~is', '$1$1', $css);

        return $css;
    }

    /**
     * Remove every `image-set(...)` / `-webkit-image-set(...)` whose arguments hold
     * a quoted remote URL. The arguments run to the matching `)`, skipping nested
     * functions and quoted strings, or to the end of the block (`}`) or of the
     * input, so an unclosed one is still caught.
     *
     * This is a linear scan. A regex here rescanned the rest of the stylesheet
     * from every `image-set(`, which was quadratic on many unclosed tokens.
     *
     * @param string $css
     * @return string
     */
    protected function stripRemoteImageSets($css)
    {
        $output = '';
        $offset = 0;
        $length = strlen($css);
        // Lower-case once and search with strpos(). stripos() lower-cases its
        // whole haystack on every call, which is quadratic over many matches.
        $lowerCss = strtolower($css);

        while (($start = strpos($lowerCss, 'image-set', $offset)) !== false) {
            $cursor = $start + strlen('image-set');
            $cursor += strspn($css, " \t\n\r\f\v", $cursor);

            if ($cursor >= $length || $css[$cursor] !== '(') {
                $output .= substr($css, $offset, $cursor - $offset);
                $offset = $cursor;
                continue;
            }

            $argumentsStart = $cursor + 1;
            $argumentsEnd = $this->findCssFunctionEnd($css, $argumentsStart);

            if (!$this->hasQuotedRemoteUrl($css, $argumentsStart, $argumentsEnd)) {
                // Any later image-set( before $argumentsEnd sits inside these
                // arguments, which were all checked.
                $output .= substr($css, $offset, $argumentsEnd - $offset);
                $offset = $argumentsEnd;
                continue;
            }

            $removeFrom = $start;
            if ($start - 8 >= $offset && substr($lowerCss, $start - 8, 8) === '-webkit-') {
                $removeFrom = $start - 8;
            }

            $output .= substr($css, $offset, $removeFrom - $offset);
            $offset = ($argumentsEnd < $length && $css[$argumentsEnd] === ')') ? $argumentsEnd + 1 : $argumentsEnd;
        }

        return $output . substr($css, $offset);
    }

    /**
     * Find where a CSS function's arguments end: the offset of the `)` that
     * closes it, or the end of the input. Nested functions, `{}` blocks, quoted
     * strings, comments and escapes are skipped, so a `)` or `}` inside them
     * does not end the function.
     *
     * @param string $css
     * @param int $from Offset just after the opening `(`
     * @return int
     */
    protected function findCssFunctionEnd($css, $from)
    {
        $length = strlen($css);
        $depth = 1;

        for ($i = $from; $i < $length; $i++) {
            $i += strcspn($css, "()'\"/\\", $i);
            if ($i >= $length) {
                break;
            }

            $char = $css[$i];
            if ($char === '\\') {
                // Skip the escaped character.
                $i++;
            } elseif ($char === '/') {
                // Skip a comment. An unclosed one runs to the end of the input.
                if (substr($css, $i, 2) === '/*') {
                    $end = strpos($css, '*/', $i + 2);
                    if ($end === false) {
                        return $length;
                    }
                    $i = $end + 1;
                }
            } elseif ($char === '"' || $char === "'") {
                // Skip to the closing quote. A string also ends at a line break.
                for ($i++; $i < $length; $i++) {
                    $i += strcspn($css, $char . "\\\n", $i);
                    if ($i >= $length || $css[$i] !== '\\') {
                        break;
                    }
                    $i++;
                }
            } elseif ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                if (--$depth === 0) {
                    return $i;
                }
            }
        }

        return $length;
    }

    /**
     * Does any quote between $from and $to open a remote URL?
     *
     * @param string $css
     * @param int $from
     * @param int $to
     * @return bool
     */
    protected function hasQuotedRemoteUrl($css, $from, $to)
    {
        for ($quote = $from; $quote < $to; $quote++) {
            $quote += strcspn($css, '\'"', $quote, $to - $quote);
            if ($quote >= $to) {
                break;
            }
            if ($this->pregMatch('~\G\s*+' . self::REMOTE_URL_START . '~i', $css, $quote + 1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * preg_replace() that throws instead of returning null when PCRE fails.
     *
     * @param string $pattern
     * @param string $replacement
     * @param string $subject
     * @return string
     * @throws RegexException
     */
    protected function pregReplace($pattern, $replacement, $subject)
    {
        $result = preg_replace($pattern, $replacement, $subject);
        if ($result === null) {
            throw new RegexException('Regular expression failed (PCRE error ' . preg_last_error() . ')');
        }

        return $result;
    }

    /**
     * preg_replace_callback() that throws instead of returning null when PCRE fails.
     *
     * @param string $pattern
     * @param callable $callback
     * @param string $subject
     * @return string
     * @throws RegexException
     */
    protected function pregReplaceCallback($pattern, $callback, $subject)
    {
        $result = preg_replace_callback($pattern, $callback, $subject);
        if ($result === null) {
            throw new RegexException('Regular expression failed (PCRE error ' . preg_last_error() . ')');
        }

        return $result;
    }

    /**
     * preg_match() that throws instead of returning false when PCRE fails.
     *
     * @param string $pattern
     * @param string $subject
     * @param int $offset
     * @return bool
     * @throws RegexException
     */
    protected function pregMatch($pattern, $subject, $offset = 0)
    {
        $matches = array();
        $result = preg_match($pattern, $subject, $matches, 0, $offset);
        if ($result === false) {
            throw new RegexException('Regular expression failed (PCRE error ' . preg_last_error() . ')');
        }

        return $result === 1;
    }

    /**
     * Resolve CSS escapes and comments so obfuscated tokens can be matched.
     *
     * @param string $css
     * @return string
     */
    protected function normalizeCss($css)
    {
        $css = $this->decodeCssEscapes($css);
        // Replace comments with a space so they can neither glue nor split tokens.
        $css = $this->replaceCssComments($css);

        return $css;
    }

    /**
     * Replace each CSS comment with a space. An unclosed comment runs to the end
     * of the input, as it does in a browser.
     *
     * This is a linear scan. Without PCRE's JIT, a regex retried the rest of the
     * input from every unclosed `/*`.
     *
     * @param string $css
     * @return string
     */
    protected function replaceCssComments($css)
    {
        $output = '';
        $offset = 0;

        while (($start = strpos($css, '/*', $offset)) !== false) {
            $output .= substr($css, $offset, $start - $offset) . ' ';
            $end = strpos($css, '*/', $start + 2);
            if ($end === false) {
                return $output;
            }
            $offset = $end + 2;
        }

        return $output . substr($css, $offset);
    }

    /**
     * Decode CSS escape sequences (`\XX` hex escapes and `\c` literal escapes)
     * into the characters they represent.
     *
     * @param string $css
     * @return string
     */
    protected function decodeCssEscapes($css)
    {
        return $this->pregReplaceCallback(
            // 1. A backslash before a line break continues a string: both vanish.
            // 2. A hex escape, which swallows one following whitespace character
            //    (CR LF counts as one).
            // 3. Any other escaped character stands for itself.
            '~\\\\(?:\r\n|[\n\r\f])|\\\\([0-9A-Fa-f]{1,6})(?:\r\n|[ \t\r\n\f])?|\\\\(.)~s',
            function ($matches) {
                if (isset($matches[1]) && $matches[1] !== '') {
                    $codepoint = hexdec($matches[1]);
                    // Zero, surrogates and values past U+10FFFF are not valid
                    // characters, and would not be valid UTF-8 either.
                    if ($codepoint === 0 || $codepoint > 0x10FFFF || ($codepoint >= 0xD800 && $codepoint <= 0xDFFF)) {
                        return "\xEF\xBF\xBD"; // U+FFFD replacement character
                    }
                    return $this->codepointToUtf8($codepoint);
                }
                if (isset($matches[2])) {
                    return $matches[2];
                }
                return '';
            },
            $css
        );
    }

    /**
     * Encode a Unicode code point as a UTF-8 byte sequence (avoids an mbstring
     * dependency, which the library does not otherwise require).
     *
     * @param int $codepoint
     * @return string
     */
    protected function codepointToUtf8($codepoint)
    {
        if ($codepoint < 0x80) {
            return chr($codepoint);
        }
        if ($codepoint < 0x800) {
            return chr(0xC0 | ($codepoint >> 6))
                . chr(0x80 | ($codepoint & 0x3F));
        }
        if ($codepoint < 0x10000) {
            return chr(0xE0 | ($codepoint >> 12))
                . chr(0x80 | (($codepoint >> 6) & 0x3F))
                . chr(0x80 | ($codepoint & 0x3F));
        }
        return chr(0xF0 | ($codepoint >> 18))
            . chr(0x80 | (($codepoint >> 12) & 0x3F))
            . chr(0x80 | (($codepoint >> 6) & 0x3F))
            . chr(0x80 | ($codepoint & 0x3F));
    }

    /**
     * Should we minify the output?
     *
     * @param bool $shouldMinify
     */
    public function minify($shouldMinify = false)
    {
        $this->minifyXML = (bool) $shouldMinify;
    }

    /**
     * Should we remove the XML tag in the header?
     *
     * @param bool $removeXMLTag
     */
    public function removeXMLTag($removeXMLTag = false)
    {
        $this->removeXMLTag = (bool) $removeXMLTag;
    }

    /**
     * Whether `<use ... xlink:href="#identifier">` elements shall be
     * removed in case expansion would exceed this threshold.
     *
     * @param int $useThreshold
     */
    public function useThreshold($useThreshold = 1000)
    {
        $this->useThreshold = (int)$useThreshold;
    }

    /**
     * Check to see if an attribute is an aria attribute or not
     *
     * @param $attributeName
     *
     * @return bool
     */
    protected function isAriaAttribute($attributeName)
    {
        return strpos($attributeName, 'aria-') === 0;
    }

    /**
     * Check to see if an attribute is an data attribute or not
     *
     * @param $attributeName
     *
     * @return bool
     */
    protected function isDataAttribute($attributeName)
    {
        return strpos($attributeName, 'data-') === 0;
    }

    /**
     * Make sure our use tag is only referencing internal resources
     *
     * @param \DOMElement $element
     * @return bool
     */
    protected function isUseTagDirty(\DOMElement $element)
    {
        $href = Helper::getElementHref($element);
        return $href && strpos($href, '#') !== 0;
    }

    /**
     * Determines whether `<use ... xlink:href="#identifier">` is expanded
     * recursively in order to create DoS scenarios. The amount of a actually
     * used element needs to be below `$this->useThreshold`.
     *
     * @param \DOMElement $element
     * @return bool
     */
    protected function isUseTagExceedingThreshold(\DOMElement $element)
    {
        if ($this->useThreshold <= 0) {
            return false;
        }
        $useId = Helper::extractIdReferenceFromHref(
            Helper::getElementHref($element)
        );
        if ($useId === null) {
            return false;
        }
        foreach ($this->elementReferenceResolver->findByElementId($useId) as $subject) {
            // Count every `<use>` the target renders, at every level, not only
            // the deepest ones.
            if ($subject->countRenderedUse() >= $this->useThreshold) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set the nesting limit for <use> tags.
     *
     * @param $limit
     */
    public function setUseNestingLimit($limit)
    {
        $this->useNestingLimit = (int) $limit;
    }

    /**
     * Remove nodes that are either invalid or malformed.
     *
     * @param \DOMNode $currentElement The current element.
     */
    protected function cleanUnsafeNodes(\DOMNode $currentElement) {
        // Replace CDATA node with encoded text node
        if ($currentElement instanceof \DOMCdataSection) {
            $textNode = $currentElement->ownerDocument->createTextNode($currentElement->nodeValue);
            // insertBefore() and removeNode() rather than replaceChild(), which,
            // like removeChild(), searches the parent's children before PHP 8.1.
            $currentElement->parentNode->insertBefore($textNode, $currentElement);
            $this->removeNode($currentElement);
        // If the element doesn't have a tagname, remove it and continue with next iteration
        } elseif (!$currentElement instanceof \DOMElement && !$currentElement instanceof \DOMText) {
            $this->addXmlIssue('Suspicious node \'' . $currentElement->nodeName . '\'', $currentElement->getLineNo());
            $this->removeNode($currentElement);
            return;
        }

        // No need to recurse: startClean() calls this for every node it walks.
        // Recursing here as well re-walked each subtree once per ancestor, which
        // multiplied the work by the nesting depth.
    }

    /**
     * Retrieve array of errors
     * @return array
     */
    private static function getXmlErrors()
    {
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $errors[] = [
                'message' => trim($error->message),
                'line' => $error->line,
            ];
        }

        return $errors;
    }
}
