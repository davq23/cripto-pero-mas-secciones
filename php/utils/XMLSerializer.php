<?php

namespace utils;

class XMLSerializer
{
    /**
     * Serializa un arreglo numérico de arreglos asociativos en un XML
     *
     * @param array $array
     * @param string $rootElementName
     * @param string $childElementName
     * @return \DOMDocument
     */
    public static function serializeArray(array &$array, string $rootElementName, string $childElementName, string $encoding = 'utf-8')
    {
        $document = new \DOMDocument('1.0', $encoding);

        $rootNode = $document->createElement($rootElementName);

        $document->appendChild($rootNode);

        $fragment = $document->createDocumentFragment();

        for ($i = 0; $i < count($array); $i++) {
            $currentElement = $document->createElement($childElementName);

            foreach ($array[$i] as $key => $value) {
                $keyProperty = $document->createElement($key);
                $keyProperty->appendChild($document->createTextNode($value));

                $currentElement->appendChild($keyProperty);
            }

            $fragment->appendChild($currentElement);
        }

        $rootNode->appendChild($fragment);

        return $document;
    }
    public static function serializeArrayOne(array &$array, string $rootElementName, string $encoding = 'utf-8')
    {
        $document = new \DOMDocument('1.0', $encoding);

        $rootNode = $document->createElement($rootElementName);

        $document->appendChild($rootNode);

        $fragment = $document->createDocumentFragment();

        foreach ($array as $key => $value) {
            $keyProperty = $document->createElement($key);
            $keyProperty->appendChild($document->createTextNode($value));

            $fragment->appendChild($keyProperty);
        }

        $rootNode->appendChild($fragment);

        return $document;
    }
}
