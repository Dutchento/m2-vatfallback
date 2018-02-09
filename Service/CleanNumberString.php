<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

class CleanNumberString
{
    private $vatInput;

    /**
     * CleanNumberString constructor.
     * @param string $vatInput
     */
    public function __construct(string $vatInput)
    {
        $this->vatInput = $vatInput;
    }

    /**
     * Return the given vat number without country node
     * @return string
     */
    public function returnStrippedString(): string
    {
        // strip first 2 letters as country code
        $vatNrWithoutCountry = preg_replace('/^[a-z]{2}/i','',$this->vatInput);

        // remove anything not alpha numeric
        return preg_replace('/[^0-9a-z]+/i','',$vatNrWithoutCountry);
    }
}
