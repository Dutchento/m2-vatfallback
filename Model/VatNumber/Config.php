<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber;

use Dutchento\Vatfallback\Model\VatNumber\ConfigInterface;

class Config implements ConfigInterface
{
    /** @var Data  */
    private $dataSource;

    /**
     * Config constructor.
     * @param Data $dataSource
     */
    public function __construct(DataInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        return $this->dataSource->get();
    }
}
