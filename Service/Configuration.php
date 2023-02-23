<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */
namespace Dutchento\Vatfallback\Service;


use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;


class Configuration implements ConfigurationInterface
{

    /** @var StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function isViesValidation(StoreInterface $store = null): bool
    {
        return (bool)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VIES_VALIDATION);
    }

    public function getViesTimeout(StoreInterface $store = null): int
    {
        return +$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VIES_TIMEOUT);
    }

    public function isVatlayerValidation(StoreInterface $store = null): bool
    {
        return (bool)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_VALIDATION);
    }

    public function getVatlayerApikey(StoreInterface $store = null): string
    {
        return (string)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_APIKEY);
    }

    public function getVatlayerHttpsEnabled(StoreInterface $store = null): bool
    {
        return (bool) $this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_USE_HTTPS);
    }

    public function getVatlayerTimeout(StoreInterface $store = null): int
    {
        return +$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_TIMEOUT);
    }

    public function isRegExpValidation(StoreInterface $store = null): bool
    {
        return (bool)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_REGEXP_VALIDATION);
    }

    public function isCacheValidation(StoreInterface $store = null): bool
    {
        return (bool)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_CACHE_VALIDATION);
    }

    public function getCacheLifetime(StoreInterface $store = null): int
    {
        return (bool)$this->storeManager
            ->getStore($store)
            ->getConfig(self::XMLPATH_CUSTOMER_VATFALLBACK_CACHE_LIFETIME);
    }
}
