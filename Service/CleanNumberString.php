<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

/**
 * Class CleanNumberString
 * @package Dutchento\Vatfallback\Service
 */
class CleanNumberString
{
    /**
     * Return the given vat number without country node
     * @return string
     */
    public function returnStrippedString($vatInput): string
    {
        return preg_replace([
                // strip first 2 letters as country code
                '/^[a-z]{2}/i',
                // remove anything not alpha numeric
                '/[^0-9a-z]+/i'
            ],
            '',
            $vatInput
        );
    }
}
