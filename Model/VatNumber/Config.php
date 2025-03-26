<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */
namespace Dutchento\Vatfallback\Model\VatNumber;

use Magento\Framework\Config\DataInterface;

class Config implements ConfigInterface
{
    /** @var DataInterface */
    private $dataSource;

    /** @var array */
    private $map;

    /**
     * Config constructor.
     * @param DataInterface $dataSource
     */
    public function __construct(
        DataInterface $dataSource
    ) {
        $this->dataSource = $dataSource;
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        if (null !== $this->map) {
            return $this->map;
        }

        $map = (array)$this->dataSource->get();
        $this->map = $map;
        return $map;
    }
}
