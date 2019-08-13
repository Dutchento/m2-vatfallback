<?php


namespace Dutchento\Vatfallback\Service\Vies;


/**
 * Class Client
 * @package Dutchento\Vatfallback\Service\Vatlayer
 */
class Client extends \GuzzleHttp\Client
{

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'base_uri' => 'http://ec.europa.eu'
        ], $config);

        parent::__construct($config);
    }

    public function getTaxationCustomsVies(
        string $countryIso,
        string $vatNumber,
        string $merchantCountryCode,
        string $merchantVatNumber,
        int $connectionTimeout = 1
    ) {

        $options = [
            'connect_timeout' => max(1, $connectionTimeout),
            'query' => [
                'ms' => $countryIso,
                'iso' => $countryIso,
                'vat' => $vatNumber,
                'requesterMs' => $merchantCountryCode,
                'requesterIso' => $merchantCountryCode,
                'requesterVat' => $merchantVatNumber,
                'BtnSubmitVat' => 'Verify',
            ],
        ];

        return $this->request(
            'GET', '
            /taxation_customs/vies/viesquer.do',
            $options
        );
    }

}
