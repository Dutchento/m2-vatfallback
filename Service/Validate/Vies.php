<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationUnavailableException;
use Exception;
use Dutchento\Vatfallback\Service\ConfigurationInterface;
use Dutchento\Vatfallback\Service\Vies\Client;
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
    /** @var Client */
    protected $client;


    /**
     * Vatlayer constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ScopeConfigInterface $scopeConfig,
        Client $client
    ) {
        $this->configuration = $configuration;
        $this->scopeConfig = $scopeConfig;
        $this->client  = $client;
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

        // call API layer endpoint
        try {
            $response = $this->client->getTaxationCustomsVies(
                $countryIso2,
                $vatNumber,
                $this->getMerchantCountryCode(),
                $this->getMerchantVatNumber(),
                $this->configuration->getViesTimeout()
            );
        } catch (Exception $error) {
            throw new ValidationUnavailableException("API unavailable {$error->getMessage()}");
        }

        $contents = $response->getBody()->getContents();

        // did we get a valid statuscode
        if ($response->getStatusCode() > 299) {
            throw new ValidationUnavailableException("API unavailable returns: status {$response->getStatusCode()} '{$contents}'");
        }

        if (false !== strpos($contents, 'No, invalid VAT number')) {
            return false;
        }

        if (false !== strpos($contents, 'Yes, valid VAT number')) {
            return true;
        }

        throw new ValidationIgnoredException('VIES could not resolve result');
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
