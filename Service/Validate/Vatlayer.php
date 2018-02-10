<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use GuzzleHttp\Client;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Vatlayer implements ValidationServiceInterface
{
    /** @var bool */
    protected $vatlayerIsEnabled;

    /** @var string */
    protected $vatlayerApiKey;

    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->vatlayerIsEnabled = (bool)$scopeConfig->getValue(
            'customer/vatfallback/vatlayer_validation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->vatlayerApiKey = (string)$scopeConfig->getValue(
            'customer/vatfallback/vatlayer_apikey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @inheritdoc
     * @throws FailedValidationException
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        // check if service is enabled and configured
        if (!$this->vatlayerIsEnabled || '' === $this->vatlayerApiKey) {
            return false;
        }

        // call API layer endpoint
        try {
            $client = new Client(['base_uri' => 'http://apilayer.net']);

            $response = $client->request('GET', '/api/validate', [
                'connect_timeout' => 1.5,
                'query' => [
                    'access_key' => $this->vatlayerApiKey,
                    'vat_number' => $countryIso2 . $vatNumber,
                    'format' => 1
                ]
            ]);
        } catch (\Exception $error) {
            throw new FailedValidationException("HTTP error {$error->getMessage()}");
        }

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new FailedValidationException(
                "Vatlayer API responded with status {$response->getStatusCode()}, 
                body {$response->getBody()->getContents()}"
            );
        }

        // Response body should be JSON
        $validationResult = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FailedValidationException("No valid JSON response, body {$response->getBody()->getContents()}");
        }

        return (bool)$validationResult['valid'];
    }
}
