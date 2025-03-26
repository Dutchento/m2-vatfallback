<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

interface CacheInterface
{
    public function load(string $vatNumber, string $countryIso2): bool;

    public function save(string $vatNumber, string $countryIso2, bool $result, string $validationName): bool;

    public function getValidationServiceName(): string;

    public function getUsedValidationServiceName(string $vatNumber, string $countryIso2): string;
}
