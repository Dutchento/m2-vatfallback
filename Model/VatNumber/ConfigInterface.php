<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */
namespace Dutchento\Vatfallback\Model\VatNumber;

interface ConfigInterface
{
    /**
     * @return array
     */
    public function get(): array;
}