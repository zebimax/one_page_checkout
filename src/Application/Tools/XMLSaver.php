<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 18.02.15
 * Time: 13:56
 */

namespace Application\Tools;


use Application\Tools\Interfaces\SaverInterface;

class XMLSaver implements SaverInterface
{
    const DEFAULT_VERSION = '1.0';
    /** @var \XMLWriter */
    private $xml;
    private $xmlDir;

    /**
     * @param $xmlDir
     * @throws \Exception
     */
    public function __construct($xmlDir)
    {
        if (!is_dir($xmlDir) || !is_writable($xmlDir)) {
            throw new \Exception('Directory for XMLSaver must be writable');
        }
        $this->xmlDir = $xmlDir;
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        $this->saveOrderXml($data);
    }

    /**
     * @param array $data
     */
    private function saveOrderXml(array $data)
    {
        $this->xml = new \XMLWriter();
        $this->xml->openUri("{$this->xmlDir}{$data['id']}.xml");
        $this->xml->setIndent(true);
        $version = isset($data['version']) ? $data['version'] : self::DEFAULT_VERSION;
        $this->xml->startDocument($version);
        foreach ($data['data'] as $elementName => $element) {
            $this->processElement($elementName, $element);
        }
        $this->xml->flush();
    }

    /**
     * @param $elementName
     * @param array $element
     */
    private function processElement($elementName, array $element)
    {
        $this->xml->startElement($elementName);
        $this->processAttributes($element);
        if (isset($element['text']) && strlen($element['text'])) {
            $this->xml->text($element['text']);
        }
        if (isset($element['elements'])) {
            $this->processComplexElement($element['elements']);
        }
        $this->xml->endElement();
    }
    /**
     * @param array $elements
     */
    private function processComplexElement(array $elements)
    {
        foreach ($elements as $elementName => $element) {
            $this->processElement($elementName, $element);
        }
    }
    /**
     * @param $element
     */
    private function processAttributes($element)
    {
        if (!empty($element['attributes'])) {
            foreach ((array)$element['attributes']  as $attributeKey => $attributeValue) {
                $this->xml->writeAttribute($attributeKey, $attributeValue);
            }
        }
    }
}