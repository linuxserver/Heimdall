<?php declare(strict_types = 1);
namespace TheSeer\Tokenizer;

use DOMDocument;
use XMLWriter;
use function count;
use const ENT_DISALLOWED;
use const ENT_NOQUOTES;
use const ENT_XML1;

class XMLSerializer {
    /** @var NamespaceUri */
    private $xmlns;

    /**
     * XMLSerializer constructor.
     */
    public function __construct(?NamespaceUri $xmlns = null) {
        if ($xmlns === null) {
            $xmlns = new NamespaceUri('https://github.com/theseer/tokenizer');
        }
        $this->xmlns = $xmlns;
    }

    public function toDom(TokenCollection $tokens): DOMDocument {
        $dom                     = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($this->toXML($tokens));

        return $dom;
    }

    public function toXML(TokenCollection $tokens): string {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);

        $writer->startDocument();
        $this->appendToWriter($writer, $tokens);
        $writer->endDocument();

        return $writer->outputMemory();
    }

    public function appendToWriter(XMLWriter $writer, TokenCollection $tokens): void {
        $writer->startElement('source');
        $writer->writeAttribute('xmlns', $this->xmlns->asString());

        if (count($tokens) > 0) {
            $writer->startElement('line');
            $writer->writeAttribute('no', '1');

            $iterator      = $tokens->getIterator();
            $previousToken = $iterator->current();
            $previousLine  = $previousToken->getLine();

            foreach ($iterator as $token) {
                $line = $token->getLine();

                if ($previousLine < $line) {
                    $writer->endElement();

                    $writer->startElement('line');
                    $writer->writeAttribute('no', (string)$line);
                    $previousLine = $line;
                }

                $value = $token->getValue();

                if ($value !== '') {
                    $writer->startElement('token');
                    $writer->writeAttribute('name', $token->getName());
                    $writer->writeRaw(htmlspecialchars($value, ENT_NOQUOTES | ENT_DISALLOWED | ENT_XML1));
                    $writer->endElement();
                }
            }

            $writer->endElement();
        }

        $writer->endElement();
    }
}
