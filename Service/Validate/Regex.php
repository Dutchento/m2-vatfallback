<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

class Regex
{
    /**
     * @param string $vatNumber
     * @param string $countryIso2
     * @return bool
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        $regex = $this->getRegexMapping($countryIso2);

        return (bool)preg_match($regex, $vatNumber);
    }

    /**
     * Based on rules in http://ec.europa.eu/taxation_customs/vies/faqvies.do
     * check if nif is valid
     *
     * Source; https://ellislab.com/forums/viewthread/159799
     *
     * @param string $countryIso2
     * @return string
     */
    public function getRegexMapping(string $countryIso2): string
    {
        $mapping =  [
            'AT' => '/^U[0-9]{8}$/',
            'BE' => '/^[0]{0,1}[0-9]{9}$/',
            'BG' => '/^[0-9]{9,10}$/',
            'CZ' => '/^[0-9]{8,10}$/',
            'DE' => '/^[0-9]{9}$/',
            'CY' => '/^[0-9]{8}[A-Z]$/',
            'DK' => '/^[0-9]{8}$/',
            'EE' => '/^[0-9]{9}$/',
            'GR' => '/^[0-9]{9}$/',
            'EL' => '/^[0-9]{9}$/',
            'ES' => '/^([a-zA-Z]\d{7}[0-9])|([0-9]\d{7}[a-zA-Z])|([a-zA-Z]\d{7}[0-9a-zA-Z])$/',
            'FI' => '/^[0-9]{8}$/',
            'FR' => '/^[0-9A-Z]{2}[0-9]{9}$/',
            'GB' => '/^(([1-9]\d{8})|([1-9]\d{11})|(GD[1-9]\d{2})|(HA[1-9]\d{2}))$/',
            'HU' => '/^[0-9]{8}$/',
            'IE' => '/^[0-9][A-Z0-9\\+\\*][0-9]{5}[A-Z]$/',
            'IT' => '/^[0-9]{11}$/',
            'LT' => '/^([0-9]{9}|[0-9]{12})$/',
            'LU' => '/^[0-9]{8}$/',
            'LV' => '/^[0-9]{11}$/',
            'MT' => '/^[0-9]{8}$/',
            'NL' => '/^[0-9]{9}B[0-9]{2}$/',
            'PL' => '/^[0-9]{10}$/',
            'PT' => '/^[0-9]{9}$/',
            'SE' => '/^[0-9]{12}$/',
            'SI' => '/^[0-9]{8}$/',
            'SK' => '/^[0-9]{10}$/',
        ];

        return $mapping[strtoupper($countryIso2)] ?? '' ;
    }
}