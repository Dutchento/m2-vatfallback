<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Config\Data as MagentoData;

class Data extends MagentoData
{
    /**
     * Data constructor.
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        string $cacheId = 'vat_numbers',
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}