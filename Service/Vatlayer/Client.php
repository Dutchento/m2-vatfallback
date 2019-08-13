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


    public function __construct(array $config = [])
    {
        $config = array_merge([
            'base_uri' => 'https://apilayer.com/'
        ], $config);

        parent::__construct($config);
    }

    public function retrieveVatnumberEndpoint(
            string $vatNumber,
            string $countryIso2,
            string $apiKey,
            int $timeout = 1
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
                'format' => 1
            ]
        ];

        $response =  $this->request('GET', '/api/validate', $options);
        self::$validationResult[$cacheKey] = $response;

        return $response;
    }
}
