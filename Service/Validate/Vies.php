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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Information as StoreInformation;

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
     * @throws \Dutchento\Vatfallback\Service\Validate\FailedValidationException
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        // call API layer endpoint
        try {
            $client = new Client(['base_uri' => 'http://ec.europa.eu']);

            $response = $client->request('GET', '/taxation_customs/vies/viesquer.do', [
                'connect_timeout' => 1.5,
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

        // body of API contains a valid flag
        return (false !== strpos($response->getBody()->getContents(), 'validStyle'));
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
