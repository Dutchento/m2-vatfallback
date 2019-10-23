<?php


namespace Dutchento\Vatfallback\Service;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
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
}
