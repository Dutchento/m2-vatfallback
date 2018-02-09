<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

interface ValidateVatInterface
{
    /**
     * Validate by giving a vatnumber and issueing country
     * @param string $vatInput
     * @param string $countryIso2
     * @return array
     */
    public function byNumberAndCountry(string $vatInput, string $countryIso2): array;
}
