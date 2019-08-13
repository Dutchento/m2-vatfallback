<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

use Dutchento\Vatfallback\Service\Validate\FailedValidationException;
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

                if ($validationService->validateVATNumber($cleanVatString, $countryIso2)) {
                    return [
                        'result' => true,
                        'service' => $validationName
                    ];
                }

            } catch (FailedValidationException $exception) {
                $this->logger->error("vatfallback {$validationName} error: {$exception->getMessage()}");
            }
        }

        return [
            'result' => false,
            'service' => 'None'
        ];
    }
}
