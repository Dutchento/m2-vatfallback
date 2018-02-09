<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Plugin\Magento\Customer\Model;

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
        $gatewayResponse = $proceed();

        if ($gatewayResponse->request_success === false) {
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
