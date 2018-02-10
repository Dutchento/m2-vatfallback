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
use Dutchento\Vatfallback\Service\Validate\Regex;
use Dutchento\Vatfallback\Service\Validate\Vatlayer;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class ValidateVat implements ValidateVatInterface
{
    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;
    /** @var Vatlayer */
    protected $vatLayerService;
    /** @var Regex */
    protected $regexService;

    /**
     * Vat constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param Vatlayer $vatLayerService
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Vatlayer $vatLayerService,
        Regex $regexService
    ) {
        $this->logger = $logger;
        $this->vatLayerService = $vatLayerService;
        $this->regexService = $regexService;
    }

    /**
     * @inheritdoc
     */
    public function byNumberAndCountry(string $vatInput, string $countryIso2): array
    {
        $cleanVatString = (new CleanNumberString())->returnStrippedString($vatInput);

        // use the Vatlayer api
        try {
            if ($this->vatLayerService->validateVATNumber($cleanVatString, $countryIso2)) {
                return [
                    'result' => true,
                    'service' => 'vatlayer'
                ];
            }
        } catch (FailedValidationException $error) {
            $this->logger->error("vatfallback Vatlayer error: {$error->getMessage()}");
        }

        // offline Regex validation
        if ($this->regexService->validateVATNumber($cleanVatString, $countryIso2)) {
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
