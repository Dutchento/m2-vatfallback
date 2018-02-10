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
use Dutchento\Vatfallback\Service\Validate\Vies;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class ValidateVat implements ValidateVatInterface
{
    /** @var LoggerInterface  */
    protected $logger;
    /** @var Vatlayer */
    protected $vatLayerService;
    /** @var Vies */
    protected $viesService;
    /** @var Regex */
    protected $regexService;

    /**
     * Vat constructor.
     * @param LoggerInterface $logger
     * @param Vatlayer $vatLayerService
     */
    public function __construct(
        LoggerInterface $logger,
        Vatlayer $vatLayerService,
        Vies $viesService,
        Regex $regexService
    ) {
        $this->logger = $logger;
        $this->vatLayerService = $vatLayerService;
        $this->viesService = $viesService;
        $this->regexService = $regexService;
    }

    /**
     * @inheritdoc
     */
    public function byNumberAndCountry(string $vatInput, string $countryIso2): array
    {
        $cleanVatString = (new CleanNumberString())->returnStrippedString($vatInput);

        // use the unofficial VIES api
        try {
            return [
                'result' => (bool)$this->viesService->validateVATNumber($cleanVatString, $countryIso2),
                'service' => 'vies'
            ];
        } catch (FailedValidationException $error) {
            $this->logger->error("vatfallback VIES error: {$error->getMessage()}");
        }

        // use the Vatlayer api
        try {
            return [
                'result' => (bool)$this->vatLayerService->validateVATNumber($cleanVatString, $countryIso2),
                'service' => 'vatlayer'
            ];
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
