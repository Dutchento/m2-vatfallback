<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber\Config;

use DOMDocument;
use DOMNode;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Stdlib\BooleanUtils;

class Converter implements ConverterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * Converter constructor.
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * Convert config.
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source): array
    {
        $result = [];

        /** @var DOMNode $vatNumberNode */
        foreach ($source->documentElement->childNodes as $vatNumberNode) {
            if ($vatNumberNode->nodeType !== XML_ELEMENT_NODE
                || !$this->booleanUtils->toBoolean($vatNumberNode->attributes->getNamedItem('active')->nodeValue ?? true)) {
                continue;
            }
            $pattern = $vatNumberNode->textContent;
            $id = $vatNumberNode->attributes->getNamedItem('id')->nodeValue;
            $countryCode = strtoupper($vatNumberNode->attributes->getNamedItem('countryCode')->nodeValue);
            $example = $vatNumberNode->attributes->getNamedItem('example')->nodeValue;

            $pattern = trim($pattern);

            $result[$countryCode] = [
                'id' => $id,
                'pattern' => $pattern,
                'example' => $example,
            ];
        }

        return $result;
    }
}
