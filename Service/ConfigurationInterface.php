<?php


namespace Dutchento\Vatfallback\Service;

use Magento\Store\Api\Data\StoreInterface;

interface ConfigurationInterface
{

    const XMLPATH_CUSTOMER_VATFALLBACK_VIES_VALIDATION = 'vatfallback/vatfallback/vies_validation';
    const XMLPATH_CUSTOMER_VATFALLBACK_VIES_TIMEOUT = 'vatfallback/vatfallback/vies_timeout';

    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_VALIDATION = 'vatfallback/vatfallback/vatlayer_validation';
    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_APIKEY = 'vatfallback/vatfallback/vatlayer_apikey';
    const XMLPATH_CUSTOMER_VATFALLBACK_VATLAYER_TIMEOUT = 'vatfallback/vatfallback/vatlayer_timeout';

    const XMLPATH_CUSTOMER_VATFALLBACK_REGEXP_VALIDATION = 'vatfallback/vatfallback/regexp_validation';

    public function isViesValidation(StoreInterface $store = null): bool;
    public function getViesTimeout(StoreInterface $store = null): int;

    public function isVatlayerValidation(StoreInterface $store = null): bool;
    public function getVatlayerApikey(StoreInterface $store = null): string;
    public function getVatlayerTimeout(StoreInterface $store = null): int;

    public function isRegExpValidation(StoreInterface $store = null): bool;

}
