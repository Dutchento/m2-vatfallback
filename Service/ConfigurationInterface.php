<?php


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

    public function isViesValidation(StoreInterface $store = null): bool;
    public function getViesTimeout(StoreInterface $store = null): int;

    public function isVatlayerValidation(StoreInterface $store = null): bool;
    public function getVatlayerApikey(StoreInterface $store = null): string;
    public function getVatlayerHttpsEnabled(StoreInterface $store = null): bool;
    public function getVatlayerTimeout(StoreInterface $store = null): int;

    public function isRegExpValidation(StoreInterface $store = null): bool;

}
