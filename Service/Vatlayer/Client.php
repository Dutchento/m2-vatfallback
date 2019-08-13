<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Vatlayer;

use Dutchento\Vatfallback\Service\ConfigurationInterface;
use GuzzleHttp\Client as GuzzleClient;
use RuntimeException;
use Exception;

/**
 * Class Client
 * @package Dutchento\Vatfallback\Service\Vatlayer
 */
class Client
{

    /** @var null | array */
    protected static $validationResult = [];

    /** @var ConfigurationInterface */
    private $configuration;

    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigurationInterface $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Call the Vatlayer API endpoint
     * @param string $vatNumber
     * @param string $countryIso2
     * @return array
     * @throws RuntimeException
     */
    public function retrieveVatnumberEndpoint(string $vatNumber, string $countryIso2): array
    {
        if (!$this->configuration->isVatlayerValidation()) {
            throw new RuntimeException("Vatlayer isn't enabled");
        }

        $vatlayerApiKey = $this->configuration->getVatlayerApikey();
        if (!$vatlayerApiKey) {
            throw new RuntimeException("Vatlayer API key isn't setup, did you forget to configure vatlayer");
        }

        $vatlayerTimeout = $this->configuration->getVatlayerTimeout();

        $cacheKey = $countryIso2 . $vatNumber;
        if (isset(self::$validationResult[$cacheKey])) {
            return self::$validationResult[$cacheKey];
        }

        // call API layer endpoint
        try {
            $client = new GuzzleClient(['base_uri' => 'http://apilayer.net']);

            $response = $client->request('GET', '/api/validate', [
                'connect_timeout' => max(1, $vatlayerTimeout),
                'query' => [
                    'access_key' => $vatlayerApiKey,
                    'vat_number' => $countryIso2 . $vatNumber,
                    'format' => 1
                ]
            ]);
        } catch (Exception $error) {
            throw new RuntimeException("HTTP error {$error->getMessage()}");
        }

        $contents = $response->getBody()->getContents();

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new RuntimeException(
                "Vatlayer API responded with status {$response->getStatusCode()}, 
                body {$contents}"
            );
        }

        // Response body should be JSON
        $result = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("No valid JSON response, body {$contents}");
        }

        // Cache result
        self::$validationResult[$cacheKey] = $result;
        return $result;
    }
}
