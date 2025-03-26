<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */
declare(strict_types=1);

namespace Dutchento\Vatfallback\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class VatCheck extends TagScope
{
    const TYPE_IDENTIFIER = 'vat_check';
    const CACHE_TAG = 'VAT_CHECK';

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(
        FrontendPool $cacheFrontendPool
    ) {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
