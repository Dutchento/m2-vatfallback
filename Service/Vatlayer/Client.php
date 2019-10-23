<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Vatlayer;

/**
 * Class Client
 * @package Dutchento\Vatfallback\Service\Vatlayer
 */
class Client extends \GuzzleHttp\Client
{
    /** @var null | array */
    protected static $validationResult = [];

    public function retrieveVatnumberEndpoint(
        string $vatNumber,
        string $countryIso2,
        string $apiKey,
        int $timeout = 1,
        bool $secure = false
    ) {
        $cacheKey = $countryIso2 . $vatNumber;
        if (isset(self::$validationResult[$cacheKey])) {
            return self::$validationResult[$cacheKey];
        }

        $options = [
            'connect_timeout' => max(1, $timeout),
            'query' => [
                'access_key' => $apiKey,
                'vat_number' => $countryIso2 . $vatNumber,
                'format' => 1,
            ],
        ];

        $scheme = 'http' . ($secure ? 's' : '');
        $url = $scheme . '://apilayer.net/api/validate';

        $response = $this->request('GET', $url, $options);
        self::$validationResult[$cacheKey] = $response;

        return $response;
    }
}
