<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Model\VatNumber\Config;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\ValidationStateInterface;

/**
 * Class Reader
 * @package Dutchento\Vatfallback\VatNumber\Config
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Reader extends Filesystem
{
    protected $_idAttributes = [
        '/config/vat_number' => 'countryCode'
    ];

    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        string $fileName = 'vat_numbers.xml',
        array $idAttributes = [],
        string $domDocumentClass = Dom::class,
        string $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
