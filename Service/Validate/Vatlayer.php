<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\ConfigurationInterface;
use Dutchento\Vatfallback\Service\Vatlayer\Client as VatlayerClient;
use Exception;

/**
 * Class Vatlayer
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Vatlayer implements ValidationServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var VatlayerClient  */
    protected $vatlayerClient;

    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigurationInterface $configuration,
        VatlayerClient $vatlayerClient
    ) {
        $this->configuration = $configuration;
        $this->vatlayerClient = $vatlayerClient;
    }

    /**
     * @inheritdoc
     */
    public function getValidationServiceName(): string
    {
        return 'Vatlayer';
    }

    /**
     * @inheritdoc
     * @throws FailedValidationException
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        if (!$this->configuration->isVatlayerValidation()) {
            return false;
        }

        // call API layer endpoint
        try {
            $clientResponse = $this->vatlayerClient->retrieveVatnumberEndpoint($vatNumber, $countryIso2);
        } catch (Exception $error) {
            throw new FailedValidationException("HTTP error {$error->getMessage()}");
        }

        if (isset($clientResponse['error'])) {
            throw new FailedValidationException($clientResponse['error']['info']);
        }

        return (bool)$clientResponse['valid'];
    }
}
