<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Vatlayer;

use GuzzleHttp\Client as GuzzleClient;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Client
{
    /** @var string */
    protected $vatlayerApiKey;

    /** @var float */
    protected $vatlayerTimeout;

    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->vatlayerApiKey = (string)$scopeConfig->getValue(
            'customer/vatfallback/vatlayer_apikey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->vatlayerTimeout = (float)$scopeConfig->getValue(
            'customer/vatfallback/vatlayer_timeout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Call the Vatlayer API endpoint
     * @param string $vatNumber
     * @param string $countryIso2
     * @return array
     * @throws \RuntimeException
     */
    public function retrieveVatnumberEndpoint(string $vatNumber, string $countryIso2): array
    {
        // call API layer endpoint
        try {
            $client = new GuzzleClient(['base_uri' => 'http://apilayer.net']);

            $response = $client->request('GET', '/api/validate', [
                'connect_timeout' => max(1, $this->vatlayerTimeout),
                'query' => [
                    'access_key' => $this->vatlayerApiKey,
                    'vat_number' => $countryIso2 . $vatNumber,
                    'format' => 1
                ]
            ]);
        } catch (\Exception $error) {
            throw new \RuntimeException("HTTP error {$error->getMessage()}");
        }

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new \RuntimeException(
                "Vatlayer API responded with status {$response->getStatusCode()}, 
                body {$response->getBody()->getContents()}"
            );
        }

        // Response body should be JSON
        $validationResult = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("No valid JSON response, body {$response->getBody()->getContents()}");
        }

        return $validationResult;
    }
}
