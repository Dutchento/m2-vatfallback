<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Api;

use Dutchento\Vatfallback\Api\Data\CompanyLookupResultInterface;
use Dutchento\Vatfallback\Api\Data\CompanyLookupResultInterfaceFactory;
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
     * @var CompanyLookupResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * CompanyLookup constructor.
     * @param VatlayerClient $vatlayerClient
     * @param LoggerInterface $logger
     * @param CleanNumberString $cleanNumberString
     * @param ConfigurationInterface $configuration
     * @param CompanyLookupResultInterfaceFactory $resultFactory
     */
    public function __construct(
        VatlayerClient $vatlayerClient,
        LoggerInterface $logger,
        CleanNumberString $cleanNumberString,
        ConfigurationInterface $configuration,
        CompanyLookupResultInterfaceFactory $resultFactory
    ) {
        $this->vatlayerClient = $vatlayerClient;
        $this->logger = $logger;
        $this->cleanNumberString = $cleanNumberString;
        $this->configuration = $configuration;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @inheritdoc
     */
    public function byVatnumber(string $vatNumber): CompanyLookupResultInterface
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

            return $this->resultFactory->create([
                'data' => [
                    'status' => $data['valid'] ?? false,
                    'country' => $data['country_code'] ?? 'Unknown',
                    'company_name' => $data['company_name'] ?? 'Unknown',
                    'company_address' => $data['company_address'] ?? 'Unknown',
                    'message' => $data['error']['message'] ?? '',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $message = $exception->getMessage();
        }

        return $this->resultFactory->create([
            'data' => [
                'status' => false,
                'message' => $message,
            ],
        ]);
    }
}
