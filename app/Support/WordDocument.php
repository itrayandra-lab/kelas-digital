<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

/**
 * Reads a Word (.docx) file without extra libraries: headings, bullet lists, bold/italic and tables become HTML for
 * lessons, and every paragraph becomes plain text for the test importer.
 */
class WordDocument
{
    private const W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    private DOMXPath $xpath;

    /** Style id => lowercase style name. Localized Word writes ids like "Judul1", but names stay "heading 1". */
    private array $styles = [];

    public function __construct(string $path)
    {
        $zip = new ZipArchive;

        if ($zip->open($path) !== true || ($xml = $zip->getFromName('word/document.xml')) === false) {
            throw new RuntimeException('File ini bukan dokumen Word (.docx) yang bisa dibaca. Simpan ulang dari Word sebagai .docx lalu coba lagi.');
        }

        $stylesXml = $zip->getFromName('word/styles.xml');
        $zip->close();

        $this->xpath = $this->load($xml);

        if ($stylesXml !== false) {
            $styles = $this->load($stylesXml);
            foreach ($styles->query('//w:style') as $style) {
                $this->styles[$style->getAttributeNS(self::W, 'styleId')] = strtolower($styles->evaluate('string(w:name/@w:val)', $style));
            }
        }
    }

    public function toHtml(): string
    {
        return $this->blocks($this->xpath->query('/w:document/w:body')->item(0));
    }

    /** Text of every paragraph in reading order, table cells included; line breaks inside a paragraph are kept. */
    public function paragraphs(): array
    {
        $texts = [];

        foreach ($this->xpath->query('//w:body//w:p') as $p) {
            $text = trim($this->runs($p, false));
            if ($text !== '') {
                $texts[] = $text;
            }
        }

        return $texts;
    }

    private function load(string $xml): DOMXPath
    {
        $doc = new DOMDocument;
        $doc->loadXML($xml, LIBXML_NONET);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('w', self::W);

        return $xpath;
    }

    private function blocks(DOMElement $parent): string
    {
        $html = '';
        $inList = false;

        foreach ($this->xpath->query('w:p|w:tbl', $parent) as $node) {
            $isItem = $node->localName === 'p' && $this->isListItem($node);

            if ($inList && ! $isItem) {
                $html .= '</ul>';
                $inList = false;
            }

            if ($node->localName === 'tbl') {
                $html .= $this->table($node);

                continue;
            }

            $inner = trim($this->runs($node, true));
            if ($inner === '') {
                continue;
            }

            if ($isItem) {
                $html .= ($inList ? '' : '<ul>')."<li>{$inner}</li>";
                $inList = true;

                continue;
            }

            $tag = $this->headingTag($node);
            $html .= "<{$tag}>{$inner}</{$tag}>";
        }

        return $inList ? $html.'</ul>' : $html;
    }

    private function table(DOMElement $table): string
    {
        $rows = $this->xpath->query('w:tr', $table);
        $html = '';

        foreach ($rows as $i => $row) {
            // The first row of a real table is its header; a one-row table is just a highlighted box.
            $cell = $i === 0 && $rows->length > 1 ? 'th' : 'td';
            $html .= '<tr>';
            foreach ($this->xpath->query('w:tc', $row) as $tc) {
                $html .= "<{$cell}>".$this->blocks($tc)."</{$cell}>";
            }
            $html .= '</tr>';
        }

        return "<table>{$html}</table>";
    }

    private function runs(DOMElement $paragraph, bool $asHtml): string
    {
        $out = '';

        foreach ($this->xpath->query('.//w:r', $paragraph) as $run) {
            if (! $this->ownedBy($run, $paragraph)) {
                continue;
            }

            $text = '';
            foreach ($run->childNodes as $child) {
                $text .= match ($child->localName) {
                    't' => $asHtml ? e($child->textContent) : $child->textContent,
                    'tab' => ' ',
                    'br', 'cr' => $asHtml ? '<br>' : "\n",
                    default => '',
                };
            }

            if ($asHtml && trim($text) !== '') {
                if ($this->isOn($run, 'b')) {
                    $text = "<strong>{$text}</strong>";
                }
                if ($this->isOn($run, 'i')) {
                    $text = "<em>{$text}</em>";
                }
            }

            $out .= $text;
        }

        return $out;
    }

    /** A run inside a text box belongs to the text box's own paragraph, not to the paragraph holding the box. */
    private function ownedBy(DOMElement $run, DOMElement $paragraph): bool
    {
        $node = $run->parentNode;
        while ($node && $node->localName !== 'p') {
            $node = $node->parentNode;
        }

        return $node !== null && $node->isSameNode($paragraph);
    }

    private function isOn(DOMElement $run, string $property): bool
    {
        $node = $this->xpath->query("w:rPr/w:{$property}", $run)->item(0);

        return $node && ! in_array($node->getAttributeNS(self::W, 'val'), ['0', 'false'], true);
    }

    private function styleName(DOMElement $paragraph): string
    {
        return $this->styles[$this->xpath->evaluate('string(w:pPr/w:pStyle/@w:val)', $paragraph)] ?? '';
    }

    private function isListItem(DOMElement $paragraph): bool
    {
        return $this->xpath->query('w:pPr/w:numPr', $paragraph)->length > 0
            || str_starts_with($this->styleName($paragraph), 'list');
    }

    /** Heading 1 becomes h2 because the lesson page already uses h1 for the lesson title. */
    private function headingTag(DOMElement $paragraph): string
    {
        $name = $this->styleName($paragraph);

        if ($name === 'title' || $name === 'heading 1') {
            return 'h2';
        }

        return preg_match('/^heading ([2-9])$/', $name, $m) ? ($m[1] === '2' ? 'h3' : 'h4') : 'p';
    }
}
