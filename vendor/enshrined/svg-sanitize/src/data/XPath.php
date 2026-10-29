<?php
namespace enshrined\svgSanitize\data;

class XPath extends \DOMXPath
{
    const DEFAULT_NAMESPACE_PREFIX = 'svg';

    /**
     * @var string
     */
    protected $defaultNamespaceURI;

    public function __construct(\DOMDocument $doc)
    {
        parent::__construct($doc);
        $this->handleDefaultNamespace();
    }

    /**
     * @param string $nodeName
     * @return string
     */
    public function createNodeName($nodeName)
    {
        if (empty($this->defaultNamespaceURI)) {
            return $nodeName;
        }
        return self::DEFAULT_NAMESPACE_PREFIX . ':' . $nodeName;
    }

    protected function handleDefaultNamespace()
    {
        $rootElements = $this->getRootElements();

        if (count($rootElements) !== 1) {
            throw new \LogicException(
                sprintf('Got %d svg elements, expected exactly one', count($rootElements)),
                1570870568
            );
        }
        $this->defaultNamespaceURI = (string)$rootElements[0]->namespaceURI;

        if ($this->defaultNamespaceURI !== '') {
            $this->registerNamespace(self::DEFAULT_NAMESPACE_PREFIX, $this->defaultNamespaceURI);
        }
    }

    /**
     * @return \DOMElement[]
     */
    protected function getRootElements()
    {
        // Only `<svg>` elements directly under the document count, so look at its
        // children. Walking getElementsByTagName('svg') visited every `<svg>` in
        // the document, and before PHP 8.3 each step searched it again.
        $rootElements = [];
        foreach ($this->document->childNodes as $node) {
            if ($node instanceof \DOMElement && $node->tagName === 'svg') {
                $rootElements[] = $node;
            }
        }
        return $rootElements;
    }
}
