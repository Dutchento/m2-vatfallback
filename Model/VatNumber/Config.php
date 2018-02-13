<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber;

use Dutchento\Vatfallback\Model\VatNumber\Config\Data;

class Config implements ConfigInterface
{
    /**
     * @var Data
     */
    private $dataSource;

    public function __construct(Data $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function get(): array
    {
        return $this->dataSource->get();
    }
}
