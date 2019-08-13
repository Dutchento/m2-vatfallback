<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

use Dutchento\Vatfallback\Service\Exceptions\GenericException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Dutchento\Vatfallback\Service\Validate\ValidationServiceInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ValidateVat
 * @package Dutchento\Vatfallback\Service
 */
class ValidateVat implements ValidateVatInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var CleanNumberString */
    protected $cleanNumberString;

    /** @var ValidationServiceInterface[] */
    protected $validationServices;

    /**
     * ValidateVat constructor.
     *
     * @param LoggerInterface $logger
     * @param CleanNumberString $cleanNumberString
     * @param ValidationServiceInterface[] $validationSerives
     */
    public function __construct(
        LoggerInterface $logger,
        CleanNumberString $cleanNumberString,
        array $validationSerives = []
    ) {
        $this->logger = $logger;
        $this->cleanNumberString = $cleanNumberString;
        $this->validationServices = $validationSerives;
    }

    /**
     * @inheritdoc
     */
    public function byNumberAndCountry(string $vatInput, string $countryIso2): array
    {
        $cleanVatString = $this->cleanNumberString->returnStrippedString($vatInput);

        /** @var ValidationServiceInterface $validationService */
        foreach ($this->validationServices as $validationService) {

            $validationName = $validationService->getValidationServiceName();
            try {

                $result = $validationService->validateVATNumber($cleanVatString, $countryIso2);
                return [
                    'result' => $result,
                    'service' => $validationName
                ];

            } catch (ValidationDisabledException $exception) {
                // validation disabled, proceed next
                $this->logger->notice("vatfallback {$validationName} disabled: {$exception->getMessage()}");

            } catch (ValidationIgnoredException $exception) {
                // validation ignored, proceed next
                $this->logger->notice("vatfallback {$validationName} ignored: {$exception->getMessage()}");

            } catch (ValidationFailedException $exception) {
                // validation failed, a problem occured
                $this->logger->error("vatfallback {$validationName} failed: {$exception->getMessage()}");

                return [
                    'result' => false,
                    'service' => $validationName
                ];

            } catch (GenericException $exception) {
                // Generic exception, log and continue
                $this->logger->error("vatfallback {$validationName} error: {$exception->getMessage()}");
            }
        }

        return [
            'result' => false,
            'service' => 'None'
        ];
    }
}
