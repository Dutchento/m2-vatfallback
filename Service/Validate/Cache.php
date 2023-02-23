<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\CacheInterface;
use Dutchento\Vatfallback\Service\ConfigurationInterface;

/**
 * Class Cache
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Cache implements ValidationServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var CacheInterface */
    protected $cache;

    /**
     * Cache constructor.
     * @param ConfigurationInterface $configuration
     * @param CacheInterface         $cache
     */
    public function __construct(
        ConfigurationInterface $configuration,
        CacheInterface         $cache
    ) {
        $this->configuration = $configuration;
        $this->cache         = $cache;
    }

    /**
     * @inheritdoc
     */
    public function getValidationServiceName(): string
    {
        return $this->cache->getValidationServiceName();
    }

    /**
     * @inheritdoc
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        return $this->cache->load($vatNumber, $countryIso2);
    }
}
