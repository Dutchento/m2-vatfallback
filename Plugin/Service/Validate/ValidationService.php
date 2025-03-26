<?php

namespace Dutchento\Vatfallback\Plugin\Service\Validate;

use Dutchento\Vatfallback\Service\Validate\ValidationServiceInterface;

class ValidationService
{
    public function beforeValidateVATNumber(ValidationServiceInterface $subject, string $vatNumber, string $countryIso2): array
    {
        return [$vatNumber, $this->getCountryCodeForVatNumber($countryIso2)];
    }

    /**
     * Returns the country code to use in the VAT number which is not always the same as the normal country code
     *
     * @param string $countryCode
     * @return string
     */
    private function getCountryCodeForVatNumber(string $countryCode): string
    {
        // Greece uses a different code for VAT numbers then its country code
        // See: http://ec.europa.eu/taxation_customs/vies/faq.html#item_11
        // And https://en.wikipedia.org/wiki/VAT_identification_number:
        // "The full identifier starts with an ISO 3166-1 alpha-2 (2 letters) country code
        // (except for Greece, which uses the ISO 639-1 language code EL for the Greek language,
        // instead of its ISO 3166-1 alpha-2 country code GR)"

        return $countryCode === 'GR' ? 'EL' : $countryCode;
    }
}
