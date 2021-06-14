<?php declare(strict_types=1);

namespace Helper;

class XmlHelper extends AssertHelper
{
    public function saveXmlFormatted(\DOMDocument $doc): string
    {
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        return $doc->saveXML();
    }

    public function formatXmlString(string $xmlString): string
    {
        $doc = new \DOMDocument();
        //I tried to use trim($xmlString, "\t\n\r") but this did not have the expected behaviour.
        $xmlString = \str_replace(["\n", "\r", "\t", "    ",], "", $xmlString);
        $doc->loadXML($xmlString);
        return $this->saveXmlFormatted($doc);
    }

    public function getExpectedXmlFromFile(
        string $expectedXmlFile,
        array $replaces,
        bool $format = true
    ): string {
        return $this->getExpectedXmlFromString(\file_get_contents($expectedXmlFile), $replaces, $format);
    }

    public function getExpectedXmlFromString(
        string $expectedXml,
        array $replaces,
        bool $format = true
    ): string {
        foreach ($replaces as $key => $value) {
            $expectedXml = \str_replace('{{' . $key .'}}', $value, $expectedXml);
        }

        return $format ? $this->formatXmlString($expectedXml) : $expectedXml;
    }

    public function checkAndStripGuid(
        \DOMNode $element,
        string $attributeName,
        bool $optional = true,
        ?string $expectedValue = null
    ): void {
        $documentSetGuid = $element->getAttribute($attributeName);
        if (!$optional || !empty($documentSetGuid)) {
            $this->assertValidGuid($documentSetGuid);

            if (!empty($expectedValue)) {
                $this->assertEquals($expectedValue, $documentSetGuid);
            }

            $element->removeAttribute($attributeName);
        }
    }

    public function checkAndRemoveAttribute(\DOMElement $node, string $attributeName, bool $allowEmpty = false): void
    {
        $this->assertTrue(
            $node->hasAttribute($attributeName),
            "Attribute $attributeName was expected on element but was not found"
        );

        if (!$allowEmpty) {
            $this->assertNotEmpty(
                $node->getAttribute($attributeName),
                "Attribute $attributeName did not have any value"
            );
        }

        $node->removeAttribute($attributeName);
    }

    public function assertNotEmptyAndRemove(\DOMDocument $doc, string $element, string $attribute): void
    {
        $node = $doc->getElementsByTagName($element)->item(0);
        $this->assertNotEmpty($node);
        $this->checkAndRemoveAttribute($node, $attribute);
    }
}
