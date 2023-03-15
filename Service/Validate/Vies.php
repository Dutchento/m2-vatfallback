<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\ConfigurationInterface;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationUnavailableException;
use Dutchento\Vatfallback\Service\Vies\Client;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Vies
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Vies implements ValidationServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var Client */
    protected $client;

    /**
     * Vies constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Client               $client
     */
    public function __construct(
        ConfigurationInterface $configuration,
        Client                 $client,
    ) {
        $this->configuration = $configuration;
        $this->client        = $client;
    }

    /**
     * @inheritdoc
     */
    public function getValidationServiceName(): string
    {
        return 'Vies';
    }

    /**
     * @inheritdoc
     * @param string $vatNumber
     * @param string $countryIso2
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        // check if service is enabled and configured
        if (!$this->configuration->isViesValidation()) {
            throw new ValidationDisabledException('VIES is disabled');
        }

        if (!extension_loaded('soap')) {
            throw new ValidationUnavailableException("PHP SOAP extension is required");
        }

        try {
            $result = $this->client->getViesResponse(
                $countryIso2,
                $vatNumber,
                $this->configuration->getViesTimeout(),
            );
        } catch (\Exception $exception) {
            throw new ValidationFailedException("API unavailable {$exception->getMessage()}");
        }

        if (!isset($result->valid)) {
            $resultJson = json_encode($result);
            throw new ValidationIgnoredException("No valid response, body {$resultJson}");
        }

        return (bool)$result->valid;
    }
}
