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
use Dutchento\Vatfallback\Service\ConfigurationInterface;
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
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * CompanyLookup constructor.
     * @param VatlayerClient $vatlayerClient
     * @param LoggerInterface $logger
     * @param CleanNumberString $cleanNumberString
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        VatlayerClient $vatlayerClient,
        LoggerInterface $logger,
        CleanNumberString $cleanNumberString,
        ConfigurationInterface $configuration
    ) {
        $this->vatlayerClient = $vatlayerClient;
        $this->logger = $logger;
        $this->cleanNumberString = $cleanNumberString;
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function byVatnumber(string $vatNumber): array
    {
        $country = substr($vatNumber, 0, 2);
        $cleanVatnumber = $this->cleanNumberString->returnStrippedString($vatNumber);

        try {
            $response = $this->vatlayerClient->retrieveVatnumberEndpoint(
                $cleanVatnumber,
                $country,
                $this->configuration->getVatlayerApikey(),
                $this->configuration->getVatlayerTimeout(),
                $this->configuration->getVatlayerHttpsEnabled()
            );
            $data = json_decode($response->getBody(), true);

            return [
                'status' => $data['valid'] ?? false,
                'country' => $data['country_code'] ?? 'Unknown',
                'company_name' => $data['company_name'] ?? 'Unknown',
                'company_address' => $data['company_address'] ?? 'Unknown',
                'message' => $data['error']['message'] ?? '',
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
