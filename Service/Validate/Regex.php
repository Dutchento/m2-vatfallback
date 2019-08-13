<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

/**
 * Class Regex
 * @package Dutchento\Vatfallback\Service\Validate
 */

use Dutchento\Vatfallback\Model\VatNumber\ConfigInterface;

/**
 * Class Regex
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Regex implements ValidationServiceInterface
{
    /** @var ConfigInterface */
    protected $vatNumberConfig;

    /**
     * Regex constructor.
     * @param ConfigInterface $vatNumberConfig
     */
    public function __construct(
            ConfigInterface $vatNumberConfig
    ) {
        $this->vatNumberConfig = $vatNumberConfig;
    }

    /**
     * @inheritdoc
     */
    public function getValidationServiceName(): string
    {
        return 'RegExp';
    }

    /**
     * @inheritdoc
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        $vatPatternMap = $this->vatNumberConfig->get();

        // as fallback use a pattern that always validates
        $regex = $vatPatternMap[$countryIso2] ?? '#.*#';
        return (bool)preg_match($regex, $vatNumber);
    }
}
