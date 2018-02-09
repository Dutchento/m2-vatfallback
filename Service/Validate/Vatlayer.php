<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

class Vatlayer implements ValidationServiceInterface
{
    /**
     * @inheritdoc
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        if(!$accessKey = $this->config->getConfigVatLayerApiToken()) { // no api token set in config
            return false;
        }

        $curlHandle = curl_init('http://apilayer.net/api/validate?' . http_build_query([
            'access_key' => $accessKey,
            'vat_number' => $countryIso2 . $vatNumber,
            'format' => 1
        ]));

        // could not create a cURL request
        if(!$curlHandle) {
            return false;
        }

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curlHandle);
        curl_close($curlHandle);

        $validationResult = json_decode($json, true);
        if(json_last_error() !== JSON_ERROR_NONE) { // no valid JSON output form the API
            return false;
        }

        if(isset($validationResult['valid'])) { // JSON contains a valid flag
            return (bool)$validationResult['valid'];
        }

        return false;
    }
}