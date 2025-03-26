<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Vies;

/**
 * Class Client
 * @package Dutchento\Vatfallback\Service\Vies
 */
class Client
{
    /** WSDL of VAT validation service */
    public const VAT_VALIDATION_WSDL_URL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /** @var null | mixed */
    protected static $validationResult = [];

    public function getViesResponse(
        string $countryIso,
        string $vatNumber,
        int    $timeout = 1,
    ): mixed {
        $cacheKey = $countryIso . $vatNumber;
        if (isset(self::$validationResult[$cacheKey])) {
            return self::$validationResult[$cacheKey];
        }
        $soapClient = $this->createVatNumberValidationSoapClient(false, $timeout);

        $requestParams                = [];
        $requestParams['countryCode'] = $countryIso;
        $requestParams['vatNumber']   = $vatNumber;

        // Send request to service
        $result = $soapClient->checkVatApprox($requestParams);

        self::$validationResult[$cacheKey] = $result;

        return $result;
    }

    /**
     * Create SOAP client based on VAT validation service WSDL
     *
     * @param boolean $trace
     * @return \SoapClient
     */
    protected function createVatNumberValidationSoapClient($trace = false, $timeout = 1)
    {
        return new \SoapClient(self::VAT_VALIDATION_WSDL_URL, [
            'trace'              => $trace,
            'connection_timeout' => max(1, $timeout)
        ]);
    }
}
