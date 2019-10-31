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
use Dutchento\Vatfallback\Service\Exceptions\InvalidConfigurationException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationUnavailableException;
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
            throw new ValidationDisabledException('Vatlayer is disabled');
        }

        $apiKey = $this->configuration->getVatlayerApikey();
        if (empty($apiKey)) {
            throw new InvalidConfigurationException('Vatlayer API is not setup correctly');
        }

        // call API layer endpoint
        try {
            $response = $this->vatlayerClient
                ->retrieveVatnumberEndpoint(
                    $vatNumber,
                    $countryIso2,
                    $apiKey,
                    $this->configuration->getVatlayerTimeout(),
                    $this->configuration->getVatlayerHttpsEnabled()
                );
        } catch (Exception $exception) {
            throw new ValidationUnavailableException("API unavailable {$exception->getMessage()}");
        }

        $contents = $response->getBody()->getContents();

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new ValidationUnavailableException("API unavailable returns: status {$response->getStatusCode()} '{$contents}'");
        }

        // Response body should be JSON
        $result = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationFailedException("No valid JSON response, body {$contents}");
        }

        if (isset($result['error'])) {
            throw new ValidationFailedException('Vatlayer could not be queried ' . $result['error']['info']);
        }

        if (! isset($result['valid'])) {
            throw new ValidationIgnoredException('Vatlayer did not return validation');
        }

        return (bool)$result['valid'];
    }
}
