<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Plugin\Magento\Customer\Model;

class Vat
{

    public function afterCheckVatNumber(
        \Magento\Customer\Model\Vat $subject,
        $result
    ) {
        // add here actual checking logic
    }
}
