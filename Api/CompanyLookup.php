<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Api;

use Dutchento\Vatfallback\Service\CleanNumberString;
use Dutchento\Vatfallback\Service\Vatlayer\Client as VatlayerClient;
use Psr\Log\LoggerInterface;

/**
 * Class CompanyLookup
 * @package Dutchento\Vatfallback\Api
 */
class CompanyLookup implements CompanyLookupInterface
{
    /** @var VatlayerClient */
    protected $vatlayerClient;

    /** @var LoggerInterface */
    protected $logger;

    /** @var CleanNumberString */
    protected $cleanNumberString;

    /**
     * CompanyLookup constructor.
     * @param VatlayerClient $vatlayerClient
     */
    public function __construct(
        VatlayerClient $vatlayerClient,
        LoggerInterface $logger,
        CleanNumberString $cleanNumberString
    ) {
        $this->vatlayerClient = $vatlayerClient;
        $this->logger = $logger;
        $this->cleanNumberString = $cleanNumberString;
    }

    /**
     * @inheritdoc
     */
    public function byVatnumber(string $vatNumber): array
    {
        $country = substr($vatNumber, 0, 2);
        $cleanVatnumber = $this->cleanNumberString->returnStrippedString($vatNumber);

        $message = 'Could not validate';

        try {
            $response = $this->vatlayerClient->retrieveVatnumberEndpoint($cleanVatnumber, $country);

            return [
                'status' => $response['valid'] ?? false,
                'country' => $response['country_code'] ?? 'Unknown',
                'company_name' => $response['company_name'] ?? 'Unknown',
                'company_address' => $response['company_address'] ?? 'Unknown',
                'message' => $response['error']['message'] ?? '',
            ];
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $message = $exception->getMessage();
        }

        return [
            'status' => false,
            'message' => $message
        ];
    }
}
