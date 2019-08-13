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
use Dutchento\Vatfallback\Service\ConfigurationInterface;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;

/**
 * Class Regex
 * @package Dutchento\Vatfallback\Service\Validate
 */
class Regex implements ValidationServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var ConfigInterface */
    protected $vatNumberConfig;

    /**
     * Regex constructor.
     * @param ConfigInterface $vatNumberConfig
     */
    public function __construct(
            ConfigurationInterface $configuration,
            ConfigInterface $vatNumberConfig
    ) {
        $this->configuration = $configuration;
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
        if (!$this->configuration->isRegExpValidation()) {
            throw new ValidationDisabledException('RegExp is disabled');
        }

        $vatPatternMap = $this->vatNumberConfig->get();

        // as fallback use a pattern that always validates
        $regex = $vatPatternMap[$countryIso2] ?? '#.*#';
        $result = preg_match($regex, $vatNumber);

        if (false === $result) {
            throw new ValidationFailedException("RegExp error occured validating '{$vatNumber}' against '{$regex}'");
        }

        return $result > 0;
    }
}
