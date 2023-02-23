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

interface ConfigurationInterface
{
    const XMLPATH_CUSTOMER_VATFALLBACK_VIES_VALIDATION = 'customer/vatfallback/vies_validation';
    const XMLPATH_CUSTOMER_VATFALLBACK_VIES_TIMEOUT = 'customer/vatfallback/vies_timeout';

    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_VALIDATION = 'customer/vatfallback/vatlayer_validation';
    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_APIKEY = 'customer/vatfallback/vatlayer_apikey';
    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_USE_HTTPS = 'customer/vatfallback/vatlayer_use_https';
    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_TIMEOUT = 'customer/vatfallback/vatlayer_timeout';

    const XMLPATH_CUSTOMER_VATFALLBACK_REGEXP_VALIDATION = 'customer/vatfallback/regexp_validation';

    const XMLPATH_CUSTOMER_VATFALLBACK_CACHE_VALIDATION = 'customer/vatfallback/cache_validation';
    const XMLPATH_CUSTOMER_VATFALLBACK_CACHE_LIFETIME = 'customer/vatfallback/cache_lifetime';


    public function isViesValidation(StoreInterface $store = null): bool;
    public function getViesTimeout(StoreInterface $store = null): int;

    public function isVatlayerValidation(StoreInterface $store = null): bool;
    public function getVatlayerApikey(StoreInterface $store = null): string;
    public function getVatlayerHttpsEnabled(StoreInterface $store = null): bool;
    public function getVatlayerTimeout(StoreInterface $store = null): int;

    public function isRegExpValidation(StoreInterface $store = null): bool;

    public function isCacheValidation(StoreInterface $store = null): bool;
    public function getCacheLifetime(StoreInterface $store = null): int;

}
