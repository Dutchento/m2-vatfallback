<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleReader;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var string
     */
    private $schema;

    /**
     * SchemaLocator constructor.
     * @param ModuleReader $moduleReader
     */
    public function __construct(ModuleReader $moduleReader)
    {
        $this->schema = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Dutchento_Vatfallback') . '/vat_numbers.xsd';
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * Get path to per file validation schema
     *
     * @return string
     */
    public function getPerFileSchema(): string
    {
        return $this->schema;
    }
}