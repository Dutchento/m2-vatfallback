<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Exception;
use GuzzleHttp\Client;
use Dutchento\Vatfallback\Service\ConfigurationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Information as StoreInformation;

/**
 * Class Vies
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Vies implements ValidationServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configuration = $configuration;
        $this->scopeConfig = $scopeConfig;
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
            return false;
        }

        // call API layer endpoint
        try {
            $client = new Client(['base_uri' => 'http://ec.europa.eu']);

            $response = $client->request('GET', '/taxation_customs/vies/viesquer.do', [
                'connect_timeout' => max(1, $this->configuration->getViesTimeout()),
                'query' => [
                    'ms' => $countryIso2,
                    'iso' => $countryIso2,
                    'vat' => $countryIso2 . $vatNumber,
                    'requesterMs' => $this->getMerchantCountryCode(),
                    'requesterIso' => $this->getMerchantCountryCode(),
                    'requesterVat' => $this->getMerchantVatNumber(),
                    'BtnSubmitVat' => 'Verify',
                ]
            ]);
        } catch (Exception $error) {
            throw new FailedValidationException("HTTP error {$error->getMessage()}");
        }

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new FailedValidationException(
                "Vatlayer API responded with status {$response->getStatusCode()}, 
                body {$response->getBody()->getContents()}"
            );
        }

        // body of API contains a valid flag
        return (false !== strpos($response->getBody()->getContents(), 'Yes, valid VAT number'));
    }

    /**
     * Get merchant country code from config
     *
     * @return string
     */
    public function getMerchantCountryCode(): string
    {
        return (string)$this->scopeConfig->getValue(StoreInformation::XML_PATH_STORE_INFO_COUNTRY_CODE);
    }

    /**
     * Get merchant VAT number from config
     *
     * @return string
     */
    public function getMerchantVatNumber(): string
    {
        return (string)$this->scopeConfig->getValue(StoreInformation::XML_PATH_STORE_INFO_VAT_NUMBER);
    }
}
