<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Plugin\Magento\Customer\Model;

use Dutchento\Vatfallback\Service\ValidateVat;
use Magento\Framework\DataObject;

class Vat
{
    /** @var \Dutchento\Vatfallback\Service\ValidateVatInterface  */
    private $validationService;

    /**
     * Vat constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param ValidateVatInterface $validationService
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ValidateVatInterface $validationService
    ) {
        $this->validationService = $validationService;
    }

    /**
     * @param \Magento\Customer\Model\Vat $subject
     * @param callable $proceed
     * @param $countryCode
     * @param $vatNumber
     * @param $requesterCountryCode
     * @param $requesterVatNumber
     * @return DataObject
     */
    public function aroundCheckVatNumber(
        \Magento\Customer\Model\Vat $subject,
        callable $proceed,
        $countryCode,
        $vatNumber,
        $requesterCountryCode,
        $requesterVatNumber
    ): DataObject {
        /** @var DataObject $gatewayResponse */
        $gatewayResponse = $proceed($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber);

        // if the result is false we start trying the fallback
        if ($gatewayResponse->getRequestSuccess() !== false) {
            return $gatewayResponse;
        }

        $response = $this->validationService->byNumberAndCountry($vatNumber, $countryCode);

        return $response['result'] ?
            $this->createGatewayResponseObject($vatNumber, true, __('VAT Number is valid.')) :
            $this->createGatewayResponseObject($vatNumber, false, __('Please enter a valid VAT number.')) ;
    }

    /**
     * @param string $vatNumber
     * @param bool $success
     * @return DataObject
     */
    public function createGatewayResponseObject(string $vatNumber, bool $success, string $message): DataObject
    {
        return new DataObject([
            'is_valid' => $success,
            'request_date' => (new \DateTimeImmutable())->format('Y-m-d'),
            'request_identifier' => $vatNumber,
            'request_success' => $success,
            'request_message' => $message,
        ]);
    }
}
