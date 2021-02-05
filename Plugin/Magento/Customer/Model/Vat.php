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
use Dutchento\Vatfallback\Service\Exceptions\NoValidationException;
use Dutchento\Vatfallback\Service\ValidateVatInterface;
use Magento\Customer\Model\Vat as Subject;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

/**
 * Class Vat
 * @package Dutchento\Vatfallback\Plugin\Magento\Customer\Model
 */
class Vat
{
    /** @var ValidateVatInterface */
    private $validationService;

    /**
     * @var CleanNumberString
     */
    private $cleanNumberService;

    /**
     * Vat constructor.
     * @param ValidateVatInterface $validationService
     * @param CleanNumberString $cleanNumberService
     */
    public function __construct(
        ValidateVatInterface $validationService,
        CleanNumberString $cleanNumberService
    ) {
        $this->validationService = $validationService;
        $this->cleanNumberService = $cleanNumberService;
    }

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param $countryCode
     * @param $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     * @return DataObject
     * @throws \Exception
     */
    public function aroundCheckVatNumber(
        Subject $subject,
        callable $proceed,
        $countryCode,
        $vatNumber,
        $requesterCountryCode = '',
        $requesterVatNumber = ''
    ): DataObject {
        /*
         * Clean the vat number before running the core vat check.
         * For example, a vat number like 'BE 0123.456.789' would return false,
         * while '0123456789' would return true.
         */
        $vatNumber = $this->cleanNumberService->returnStrippedString($vatNumber);

        /** @var DataObject $gatewayResponse */
        $gatewayResponse = $proceed($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber);

        // If the result is false we start trying the fallback
        if ($gatewayResponse->getRequestSuccess() !== false) {
            return $gatewayResponse;
        }

        // Should we even be checking for VAT?
        // This check is duplicated in the original checkVatNumber call
        if (!$subject->canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)) {
            return $gatewayResponse;
        }

        try {
            $response = $this->validationService->byNumberAndCountry($vatNumber, $countryCode);

            return $response['result'] ?
                $this->createGatewayResponseObject($vatNumber, true, __('VAT Number is valid.')) :
                $this->createGatewayResponseObject($vatNumber, false, __('Please enter a valid VAT number.'));
        } catch (NoValidationException $exception) {

        }

        return $gatewayResponse;
    }

    /**
     * @param string $vatNumber
     * @param bool $success
     * @param Phrase $message
     * @return DataObject
     * @throws \Exception
     */
    public function createGatewayResponseObject(string $vatNumber, bool $isValid, Phrase $message): DataObject
    {
        return new DataObject([
            'is_valid' => $isValid,
            'request_date' => (new \DateTimeImmutable())->format('Y-m-d'),
            'request_identifier' => $vatNumber,
            'request_success' => true,
            'request_message' => $message,
        ]);
    }
}
