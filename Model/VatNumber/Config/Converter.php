<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber\Config;

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
     * Convert config creating an assoc array with the country code as key
     * and the pattern and other properties as value
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source): array
    {
        $result = [];

        /** @var \DOMNode $vatNumberNode */
        foreach ($source->documentElement->childNodes as $vatNumberNode) {
            if ($vatNumberNode->attributes === null) {
                continue;
            }

            $countryCode = $vatNumberNode->attributes->getNamedItem('countryCode')->nodeValue;
            $result[strtoupper($countryCode)] = $this->stripAndValidatePattern($vatNumberNode->textContent);
        }

        return $result;
    }

    /**
     * Remove unwanted characters and validate regex
     * @param string $pattern
     * @return string
     * @throws \InvalidArgumentException
     */
    public function stripAndValidatePattern(string $pattern): string
    {
        $pattern = trim($vatNumberNode->textContent);
        $pattern = '/' .trim($pattern, '/') . '/';
        if (preg_match($pattern, null) === false) {
            throw new \InvalidArgumentException("Regex pattern '{$pattern}' does not appear to be valid");
        }

        return $pattern;
    }
}
