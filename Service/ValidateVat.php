<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

use Dutchento\Vatfallback\Service\Validate\Regex;

class ValidateVat implements ValidateVatInterface
{
    /**
     * @inheritdoc
     */
    public function byNumberAndCountry(string $vatInput, string $countryIso2): array
    {
        $cleanVatString = (new CleanNumberString())->returnStrippedString($vatInput);

        $regexService = new Regex();
        if ($regexService->validateVATNumber($cleanVatString, $countryIso2)) {
            return [
                'result' => true,
                'service' => 'regex'
            ];
        }

        return [
            'result' => false,
            'service' => null
        ];
    }
}
