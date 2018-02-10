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

class CompanyLookup implements CompanyLookupInterface
{
    /** @var VatlayerClient */
    protected $vatlayerClient;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * CompanyLookup constructor.
     * @param VatlayerClient $vatlayerClient
     */
    public function __construct(
        VatlayerClient $vatlayerClient,
        LoggerInterface $logger
    ) {
        $this->vatlayerClient = $vatlayerClient;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function byVatnumber(string $vatNumber): array
    {
        $country = substr($vatNumber, 0, 2);
        $cleanVatnumber = (new CleanNumberString())->returnStrippedString($vatNumber);
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