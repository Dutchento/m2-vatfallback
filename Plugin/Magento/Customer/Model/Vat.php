<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Plugin\Magento\Customer\Model;

use Dutchento\Vatfallback\Service\CleanNumberString;
use Magento\Framework\DataObject;

class Vat
{

    public function aroundCheckVatNumber(
        \Magento\Customer\Model\Vat $subject,
        callable $proceed,
        $countryCode,
        $vatNumber,
        $requesterCountryCode,
        $requesterVatNumber
    ): DataObject
    {
        /** @var DataObject $gatewayResponse */
        $gatewayResponse = $proceed($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber);

        // if the result is false we start trying the fallback
        if ($gatewayResponse->request_success === false) {
            $cleanVatString = (new CleanNumberString($vatNumber))->returnStrippedString();



            $gatewayResponse = new DataObject([
                'is_valid' => false,
                'request_date' => '',
                'request_identifier' => '',
                'request_success' => false,
                'request_message' => __('Error during VAT Number verification.'),
            ]);
        }

        return $gatewayResponse;
    }
}
